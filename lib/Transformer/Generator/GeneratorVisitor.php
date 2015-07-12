<?php

namespace Regen\Transformer\Generator;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PhpParser\PrettyPrinter\Standard;

/**
 * Rewrite for statements into while loops
 */
class GeneratorVisitor extends NodeVisitorAbstract {
	/**
	 * @var GeneratorDetector
	 */
	private $generatorDetector;

	/**
	 * @var StateCounter
	 */
	private $stateCounter;

	/**
	 * @var AssignmentFinder
	 */
	private $assignmentFinder;

	public function __construct() {
		$this->generatorDetector = new GeneratorDetector();
		$this->stateCounter = new StateCounter();
		$this->assignmentFinder = new AssignmentFinder();
	}


	public function leaveNode(Node $node) {
		if (($node instanceof Node\Stmt\Function_ || $node instanceof Node\Expr\Closure)
			&& $this->generatorDetector->isGenerator($node)
		) {
			$assignmentNames = $this->assignmentFinder->getNames($node);
			$statementGroup = new StatementGroup($node->stmts, $this->stateCounter->getNextState(), null, null);
			$groups = $this->flattenStatementGroups($statementGroup);
			$switch = $this->generateSwitch($groups);
			$loop = new Node\Stmt\While_(new Node\Expr\PropertyFetch        (
				new Node\Expr\Variable('context'), 'active'
			), [
				new Node\Expr\Assign(
					new Node\Expr\PropertyFetch        (
						new Node\Expr\Variable('context'), 'current'
					),
					new Node\Expr\PropertyFetch        (
						new Node\Expr\Variable('context'), 'next'
					)
				),
				$switch
			]);

			$paramNames = array_map(function (Node\Param $param) {
				return $param->name;
			}, $node->params);
			$allNames = array_merge($paramNames, $assignmentNames);
			$uses = array_map(function ($name) {
				return new Node\Expr\ClosureUse($name, true);
			}, $allNames);

			$closure = new Node\Expr\Closure([
				'params' => [new Node\Param('context', null, '\Regen\Polyfill\GeneratorContext')],
				'uses' => $uses,
				'stmts' => [$loop]
			]);
			$initializations = array_map(function ($name) {
				return new Node\Expr\Assign(new Node\Expr\Variable($name), new Node\Scalar\LNumber(0));
			}, $assignmentNames);
			$returnStatement = new Node\Stmt\Return_(
				new Node\Expr\New_(new Node\Name('\Regen\Polyfill\RegenIterator'), [new Node\Arg($closure)])
			);

			$nodes = $initializations;
			$nodes[] = $returnStatement;
			$node->stmts = $nodes;
		} else {
			return null;
		}
	}

	/**
	 * @param StatementGroup $inputGroup
	 * @return StatementGroup[]
	 */
	protected function flattenStatementGroups(StatementGroup $inputGroup) {
		$inputGroup->statements[] = $this->getStopCall();
		/** @var StatementGroup[] $groups */
		$groups = [$inputGroup];
		$outputGroups = [];
		while ($group = array_shift($groups)) {
			/** @var StatementGroup $group */
			$groupedStatements = $this->splitStatements($group->statements);
			if (count($groupedStatements) > 1) {
				$splitGroups = $this->generateGroupsFromStatements($group->statements, $group->parent, $group->state);
				$groups = array_merge($groups, $splitGroups);
			} else {
				foreach ($group->statements as &$statement) {
					if ($statement instanceof Node\Stmt\If_) {
						$newGroup = $this->getIfBodyGroup($statement->stmts, $group);
						$groups[] = $newGroup;
						$statement->stmts = [$this->getStateAssignment($newGroup->state)];
						if ($statement->else) {
							$newGroup = $this->getIfBodyGroup($statement->else->stmts, $group);
							$groups[] = $newGroup;
							$statement->else->stmts = [$this->getStateAssignment($newGroup->state)];
						}
						foreach ($statement->elseifs as $elseIf) {
							$newGroup = $this->getIfBodyGroup($elseIf->stmts, $group);
							$groups[] = $newGroup;
							$elseIf->stmts = [$this->getStateAssignment($newGroup->state)];
						}
						if (!$statement->else) {
							if ($sibling = $group->findNextSibling()) {
								$statement->else = new Node\Stmt\Else_([$this->getStateAssignment($sibling->state)]);
							} else {
								$statement->else = new Node\Stmt\Else_([$this->getStopCall()]);
							}
						}
					}

					if ($statement instanceof Node\Stmt\While_) {
						if ($sibling = $group->findNextSibling()) {
							$loopEndStatement = $this->getStateAssignment($sibling->state);
						} else {
							$loopEndStatement = $this->getStopCall();
						}
						$childStatements = $statement->stmts;
						array_unshift($childStatements, new Node\Stmt\If_(new Node\Expr\BooleanNot($statement->cond), [
							'stmts' => [$loopEndStatement]
						]));
						$inWhileState = $this->stateCounter->getNextState();
						$childStatements[] = $this->getStateAssignment($inWhileState);
						$oldGroupStatements = $group->statements;
						array_pop($oldGroupStatements); //remove the while
						$inWhileGroup = new StatementGroup($childStatements, $inWhileState,
							$group->nextSibling, $group->parent);
						$oldGroupStatements[] = $this->getStateAssignment($inWhileGroup->state);
						$beforeWhileGroup = new StatementGroup($oldGroupStatements, $group->state, $inWhileGroup,
							$group->parent);
						$group = $beforeWhileGroup;
						$groups[] = $inWhileGroup;
					}

					if ($statement instanceof Node\Expr\Yield_) {
						$statement = new Node\Stmt\Return_($statement->value);
						if ($sibling = $group->findNextSibling()) {
							array_unshift($group->statements, $this->getStateAssignment($sibling->state));
						} else {
							array_unshift($group->statements, $this->getStopCall());
						}
					}
				}
				if ($group) {
					$outputGroups[] = $group;
				}
			}
		}
		return $outputGroups;
	}

	/**
	 * @param StatementGroup[] $groups
	 * @return Node\Stmt\Switch_
	 */
	protected function generateSwitch(array $groups) {
		$cases = array_map(function (StatementGroup $group) {
			$statements = $group->statements;
			$lastStatement = $statements[count($statements) - 1];
			if (!$lastStatement instanceof Node\Stmt\Return_) {
				$statements[] = new Node\Stmt\Break_();
			}
			return new Node\Stmt\Case_(new Node\Scalar\LNumber($group->state), $statements);
		}, $groups);
		return new Node\Stmt\Switch_(new Node\Expr\PropertyFetch        (
			new Node\Expr\Variable('context'), 'current'
		), $cases);
	}

	/**
	 * @param Node[] $statements
	 * @param StatementGroup $parentGroup
	 * @return StatementGroup
	 */
	protected function getIfBodyGroup(array $statements, StatementGroup $parentGroup) {
		$lastStatement = array_pop($statements);
		if ($parentGroup->nextSibling) {
			$endStatement = $this->getStateAssignment($parentGroup->nextSibling->state);
		} else {
			$endStatement = $this->getStopCall();
		}
		if ($lastStatement instanceof Node\Stmt\Return_ || $lastStatement instanceof Node\Expr\Yield_) {
			$statements[] = $endStatement;
		}
		$statements[] = $lastStatement;
		return new StatementGroup($statements, $this->stateCounter->getNextState(), null, $parentGroup);
	}

	protected function getStateAssignment($state) {
		return new Node\Expr\Assign(
			new Node\Expr\PropertyFetch        (
				new Node\Expr\Variable('context'), 'next'
			),
			new Node\Scalar\LNumber($state)
		);
	}

	protected function getStopCall() {
		return new Node\Expr\MethodCall(new Node\Expr\Variable('context'), 'stop');
	}

	/**
	 * @param Node[] $statements
	 * @param StatementGroup $parent
	 * @param int $firstState
	 * @return StatementGroup[]
	 */
	protected function generateGroupsFromStatements($statements, $parent = null, $firstState) {
		$groupedStatements = $this->splitStatements($statements);
		/** @var StatementGroup[] $groups */
		$groups = array_map(function ($statements, $state) use ($parent) {
			if (is_null($state)) {
				$state = $this->stateCounter->getNextState();
			}
			return new StatementGroup($statements, $state, null, $parent);
		}, $groupedStatements, [$firstState]);
		for ($i = 0; $i < (count($groups) - 1); $i++) {
			$groups[$i]->nextSibling = $groups[$i + 1];
		}
		return $groups;
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
				in_array('stmts', $statement->getSubNodeNames()) ||
				$statement instanceof Node\Expr\Yield_
			) {
				$counter++;
				$result[$counter] = [];
			}
		}
		return array_filter($result, function (array $group) {
			return count($group) > 0;
		});
	}
}
