<?php

namespace Regen\Transformer\Generator;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use Regen\Transformer\HasChildNodeVisitor;

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
			if ($this->hasYield($statement)) {
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
			case 'Stmt_Do':
				return new DoWhileStatementGroup($statements, $state, $sibling, $parent);
			case 'Expr_Yield':
				return new YieldStatementGroup($statements, $state, $sibling, $parent);
			case 'Stmt_Break':
				return new BreakStatementGroup($statements, $state, $sibling, $parent);
			case 'Stmt_Switch':
				return new SwitchStatementGroup($statements, $state, $sibling, $parent);
			case 'Stmt_Continue':
				return new ContinueStatementGroup($statements, $state, $sibling, $parent);
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

		$this->setNextSiblings($groups);
		$groups[count($groups) - 1]->nextSibling = $group->nextSibling;
		return $groups;
	}

	/**
	 * @param integer $state
	 * @return Node\Expr\Assign
	 */
	protected function getStateAssignment($state) {
		return new Node\Expr\Assign(
			new Node\Expr\PropertyFetch(
				new Node\Expr\Variable('context'), 'next'
			),
			new Node\Scalar\LNumber($state)
		);
	}

	/**
	 * @return Node\Expr\MethodCall
	 */
	protected function getStopCall() {
		return new Node\Expr\MethodCall(new Node\Expr\Variable('context'), 'stop');
	}

	/**
	 * @param StatementGroup $group
	 * @return Node\Expr
	 */
	protected function getGroupEndCall(StatementGroup $group) {
		if ($sibling = $group->findNextSibling()) {
			return $this->getStateAssignment($sibling->state);
		} else {
			return $this->getStopCall();
		}
	}

	/**
	 * @param StatementGroup[] $groups
	 */
	protected function setNextSiblings($groups) {
		for ($i = 0; ($i < count($groups) - 1); $i++) {
			$groups[$i]->nextSibling = $groups[$i + 1];
		}
	}

	protected function hasYield(Node $node) {
		if ($node instanceof Node\Expr\Yield_) {
			return true;
		} else {
			$traverser = new NodeTraverser();
			$yieldVisitor = new HasChildNodeVisitor('Expr_Yield', ['Stmt_Function', 'Expr_Closure']);
			$breakVisitor = new HasChildNodeVisitor('Stmt_Break', ['Stmt_Function', 'Expr_Closure']);
			$continueVisitor = new HasChildNodeVisitor('Stmt_Continue', ['Stmt_Function', 'Expr_Closure']);
			$traverser->addVisitor($yieldVisitor);
			$traverser->addVisitor($breakVisitor);
			$traverser->addVisitor($continueVisitor);
			$traverser->traverse([$node]);
			return $yieldVisitor->exists() || $breakVisitor->exists() || $continueVisitor->exists();
		}
	}
}
