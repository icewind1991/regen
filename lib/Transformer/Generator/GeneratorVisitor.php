<?php

namespace Regen\Transformer\Generator;

use PhpParser\Node;
use Regen\Transformer\BaseVisitor;

/**
 * Rewrite for statements into while loops
 */
class GeneratorVisitor extends BaseVisitor {
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


	public function enterNode(Node $node) {
		if (($node instanceof Node\Stmt\Function_ || $node instanceof Node\Expr\Closure)
			&& $this->generatorDetector->isGenerator($node)
		) {
			list($node) = $this->traverseNodes([$node], [new ForVisitor()]);
			$assignmentNames = $this->assignmentFinder->getNames($node);
			$statementGroup = new StatementGroup($node->stmts, $this->stateCounter->getNextState(), null, null);
			$groups = $this->flattenStatementGroups($statementGroup);
			$switch = $this->generateSwitch($groups);
			$loop = $this->generateLoop($switch);

			$paramNames = array_map(function (Node\Param $param) {
				return $param->name;
			}, $node->params);

			$closure = $this->getClosure($loop, array_merge($paramNames, $assignmentNames));
			$node->stmts = array_map(function ($name) {
				return $this->assignValue($name, null, 0);
			}, $assignmentNames);
			$returnStatement = new Node\Stmt\Return_(
				new Node\Expr\New_(new Node\Name('\Regen\Polyfill\RegenIterator'), [new Node\Arg($closure)])
			);

			$node->stmts[] = $returnStatement;
			return $node;
		} else {
			return null;
		}
	}

	protected function getClosure($loop, $names) {
		$uses = array_map(function ($name) {
			return new Node\Expr\ClosureUse($name, true);
		}, $names);

		return new Node\Expr\Closure([
			'params' => [new Node\Param('context', null, '\Regen\Polyfill\GeneratorContext')],
			'uses' => $uses,
			'stmts' => [$loop]
		]);
	}

	protected function generateLoop(Node\Stmt\Switch_ $switch) {
		return new Node\Stmt\While_($this->getProperty('context', 'active'), [
			$this->assignValue('context', 'current', $this->getProperty('context', 'next')),
			$switch
		]);
	}

	/**
	 * @param StatementGroup $inputGroup
	 * @return StatementGroup[]
	 */
	protected function flattenStatementGroups(StatementGroup $inputGroup) {
		$inputGroup->statements[] = $this->getMethodCall('context', 'stop');
		/** @var StatementGroup[] $groups */
		$groups = [$inputGroup];
		$outputGroups = [];
		while ($group = array_shift($groups)) {
			/** @var StatementGroup $group */
			$groupTransformer = $group->getTransformer($this->stateCounter);

			list($done, $new) = $groupTransformer->transformGroup($group);

			$outputGroups = array_merge($outputGroups, $done);
			$groups = array_merge($groups, $new);
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
			return new Node\Stmt\Case_($this->getValue($group->state), $statements);
		}, $groups);
		return new Node\Stmt\Switch_($this->getProperty('context', 'current'), $cases);
	}
}
