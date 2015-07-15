<?php

namespace Regen\Transformer\Generator;

use PhpParser\Node;

class BreakStatementGroupTransformer extends StatementGroupTransformer {
	/**
	 * @param StatementGroup $group
	 * @return array [StatementGroup[] $doneGroups, StatementGroup[] $newGroups]
	 * @throws \Exception
	 */
	public function transformGroup(StatementGroup $group) {
		$statement = array_pop($group->statements);

		if ($statement instanceof Node\Stmt\Break_) {
			if ($statement->num) {
				if ($statement->num instanceof Node\Scalar\LNumber) {
					$count = $statement->num->value;
				} else {
					throw new \Exception('only constant integer breaks are supported');
				}
			} else {
				$count = 1;
			}
			$activeGroup = $group->parent;
			while ($activeGroup && ($count > 0)) {
				if (
					$activeGroup instanceof WhileStatementGroup
				) {
					$count--;
				}
				if ($count > 0) {
					if (!$activeGroup->parent) {
						throw new \Exception('invalid break statement');
					}
					$activeGroup = $activeGroup->parent;
				}
			}
			if ($sibling = $activeGroup->findNextSibling()) {
				array_push($group->statements, $this->getStateAssignment($sibling->state));
			} else {
				array_push($group->statements, $this->getStopCall());
			}
		}

		return [
			[$group],
			[]
		];
	}
}
