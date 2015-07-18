<?php

namespace Regen\Transformer\Generator;

use PhpParser\Node;

class BreakStatementGroupTransformer extends StatementGroupTransformer {
	/**
	 * @param StatementGroup $group
	 * @return array [StatementGroup[] $doneGroups, StatementGroup[] $newGroups]
	 */
	public function transformGroup(StatementGroup $group) {
		$statement = array_pop($group->statements);

		if ($statement instanceof Node\Stmt\Break_) {
			$count = $this->getCount($statement);
			$targetGroup = $this->getTargetGroup($group, $count);
			array_push($group->statements, $this->getGroupEndCall($targetGroup));
		}

		return [
			[$group],
			[]
		];
	}

	/**
	 * @param Node\Stmt\Break_|Node\Stmt\Continue_ $statement
	 * @return int
	 * @throws \Exception
	 */
	protected function getCount($statement) {
		if ($statement->num) {
			if ($statement->num instanceof Node\Scalar\LNumber) {
				return $statement->num->value;
			} else {
				throw new \Exception('only constant integer breaks are supported');
			}
		} else {
			return 1;
		}
	}

	protected function parentGroupCounts(StatementGroup $group) {
		return $group instanceof WhileStatementGroup ||
		$group instanceof SwitchStatementGroup ||
		$group instanceof DoWhileStatementGroup;
	}

	/**
	 * @param StatementGroup $group
	 * @param int $count
	 * @return StatementGroup
	 * @throws \Exception
	 */
	protected function getTargetGroup(StatementGroup $group, $count) {
		$activeGroup = $group->parent;
		while ($activeGroup && ($count > 0)) {
			if ($this->parentGroupCounts($activeGroup)) {
				$count--;
			}
			if ($count > 0) {
				$activeGroup = $activeGroup->parent;
			}
		}
		if (is_null($activeGroup)) {
			throw new \Exception('invalid break statement');
		}
		return $activeGroup;
	}
}
