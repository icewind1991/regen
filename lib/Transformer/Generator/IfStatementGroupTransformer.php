<?php

namespace Regen\Transformer\Generator;

use PhpParser\Node;

class IfStatementGroupTransformer extends StatementGroupTransformer {
	/**
	 * @param StatementGroup $group
	 * @return array [StatementGroup[] $doneGroups, StatementGroup[] $newGroups]
	 */
	public function transformGroup(StatementGroup $group) {
		$statement = end($group->statements);
		$newGroups = [];
		$newGroup = $this->getIfBodyGroup($statement->stmts, $group);
		$newGroups[] = $newGroup;
		$statement->stmts = [$this->getStateAssignment($newGroup->state)];
		if ($statement->else) {
			$newGroup = $this->getIfBodyGroup($statement->else->stmts, $group);
			$newGroups[] = $newGroup;
			$statement->else->stmts = [$this->getStateAssignment($newGroup->state)];
		}
		foreach ($statement->elseifs as $elseIf) {
			$newGroup = $this->getIfBodyGroup($elseIf->stmts, $group);
			$newGroups[] = $newGroup;
			$elseIf->stmts = [$this->getStateAssignment($newGroup->state)];
		}
		if (!$statement->else) {
			$statement->else = new Node\Stmt\Else_([$this->getGroupEndCall($group)]);
		}
		return [
			[$group],
			$newGroups
		];
	}

	/**
	 * @param Node[] $statements
	 * @param StatementGroup $parentGroup
	 * @return StatementGroup
	 */
	protected function getIfBodyGroup(array $statements, StatementGroup $parentGroup) {
		$lastStatement = array_pop($statements);
		$endStatement = $this->getGroupEndCall($parentGroup);
		$statements[] = $endStatement;

		if (!is_null($lastStatement)) {
			$statements[] = $lastStatement;
		}
		return $this->buildGroupForStatements($statements, $this->stateCounter->getNextState(), null, $parentGroup);
	}
}
