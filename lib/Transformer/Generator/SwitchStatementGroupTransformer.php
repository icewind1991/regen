<?php

namespace Regen\Transformer\Generator;

use PhpParser\Node;

class SwitchStatementGroupTransformer extends StatementGroupTransformer {
	/**
	 * @param StatementGroup $group
	 * @return array [StatementGroup[] $doneGroups, StatementGroup[] $newGroups]
	 */
	public function transformGroup(StatementGroup $group) {
		$lastStatement = end($group->statements);
		/** @var StatementGroup[] $newGroups */
		$newGroups = [];
		if ($lastStatement instanceof Node\Stmt\Switch_) {
			foreach ($lastStatement->cases as $case) {
				$newGroups = array_merge($newGroups, $this->transformCase($case, $group));
			}

			$this->setNextSiblings($newGroups);
		}


		return [
			[$group],
			$newGroups
		];
	}

	/**
	 * @param Node\Stmt\Case_ $case
	 * @param StatementGroup $group
	 * @return StatementGroup[]
	 */
	private function transformCase(Node\Stmt\Case_ $case, StatementGroup $group) {
		$groupedStatements = $this->splitStatements($case->stmts);
		$first = true;
		$newGroups = [];
		foreach ($groupedStatements as $statements) {
			$newGroup = $this->buildGroupForStatements($statements, $this->stateCounter->getNextState(), null, $group);
			if ($first) {
				$case->stmts = [$this->getStateAssignment($newGroup->state), new Node\Stmt\Break_()];
			}
			$newGroups[] = $newGroup;
			$first = false;
		}
		return $newGroups;
	}
}
