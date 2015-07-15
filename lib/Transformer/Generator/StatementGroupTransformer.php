<?php

namespace Regen\Transformer\Generator;

use PhpParser\Node;

class StatementGroupTransformer {
	/**
	 * @var StateCounter
	 */
	protected $stateCounter;

	/**
	 * StatementGroupTransformer constructor.
	 * @param StateCounter $stateCounter
	 */
	public function __construct(StateCounter $stateCounter) {
		$this->stateCounter = $stateCounter;
	}

	/**
	 * @param StatementGroup $group
	 * @return array [StatementGroup[] $doneGroups, StatementGroup[] $newGroups]
	 */
	public function transformGroup(StatementGroup $group) {
		$splitGroups = $this->splitGroup($group);
		if (count($splitGroups) > 1) {
			return [
				[],
				$splitGroups
			];
		} else {
			return [[$group], []];
		}
	}

	/**
	 * @param Node[] $statements
	 * @return Node[][]
	 */
	protected function splitStatements($statements) {
		$result = [];
		$counter = 0;
		foreach ($statements as $statement) {
			$result[$counter][] = $statement;
			if (
				$statement instanceof Node\Stmt\If_ ||
				$statement instanceof Node\Stmt\While_ ||
				$statement instanceof Node\Expr\Yield_ ||
				$statement instanceof Node\Stmt\Break_
			) {
				$counter++;
				$result[$counter] = [];
			}
		}
		$result = array_filter($result, function (array $group) {
			return count($group) > 0;
		});
		return array_values($result);
	}

	/**
	 * @param Node[] $statements
	 * @param int $state
	 * * @param StatementGroup|null $parent
	 * @param StatementGroup|null $sibling
	 * @return StatementGroup
	 */
	protected function buildGroupForStatements(array $statements, $state, $sibling, $parent) {
		$lastStatement = end($statements);
		$type = $lastStatement->getType();
		switch ($type) {
			case 'Stmt_If':
				return new IfStatementGroup($statements, $state, $sibling, $parent);
			case 'Stmt_While':
				return new WhileStatementGroup($statements, $state, $sibling, $parent);
			case 'Expr_Yield':
				return new YieldStatementGroup($statements, $state, $sibling, $parent);
			case 'Stmt_Break':
				return new BreakStatementGroup($statements, $state, $sibling, $parent);
			default:
				return new StatementGroup($statements, $state, $sibling, $parent);
		}
	}

	/**
	 * @param StatementGroup $group
	 * @return StatementGroup[]
	 */
	protected function splitGroup(StatementGroup $group) {
		$splitStatement = $this->splitStatements($group->statements);

		/** @var StatementGroup[] $groups */
		$groups = array_map(function ($statements, $state) use ($group) {
			if (is_null($state)) {
				$state = $this->stateCounter->getNextState();
			}
			return $this->buildGroupForStatements($statements, $state, null, $group->parent);
		}, $splitStatement, [$group->state]);

		for ($i = 0; $i < (count($groups) - 1); $i++) {
			$groups[$i]->nextSibling = $groups[$i + 1];
		}
		$groups[count($groups) - 1]->nextSibling = $group->nextSibling;
		return $groups;
	}

	protected function getStateAssignment($state) {
		return new Node\Expr\Assign(
			new Node\Expr\PropertyFetch(
				new Node\Expr\Variable('context'), 'next'
			),
			new Node\Scalar\LNumber($state)
		);
	}

	protected function getStopCall() {
		return new Node\Expr\MethodCall(new Node\Expr\Variable('context'), 'stop');
	}

	/**
	 * @param StatementGroup $group
	 * @return Node\Stmt;
	 */
	protected function getGroupEndCall(StatementGroup $group) {
		if ($sibling = $group->findNextSibling()) {
			return $this->getStateAssignment($sibling->state);
		} else {
			return $this->getStopCall();
		}
	}
}
