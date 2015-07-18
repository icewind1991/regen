<?php

namespace Regen\Transformer\Generator;

use PhpParser\Node;

class ContinueStatementGroupTransformer extends BreakStatementGroupTransformer {
	/**
	 * @param StatementGroup $group
	 * @return array [StatementGroup[] $doneGroups, StatementGroup[] $newGroups]
	 */
	public function transformGroup(StatementGroup $group) {
		$statement = array_pop($group->statements);

		if ($statement instanceof Node\Stmt\Continue_) {
			$count = $this->getCount($statement);
			$targetGroup = $this->getTargetGroup($group, $count);
			// start of the loop is saved in the next group
			array_push($group->statements, $this->getStateAssignment($targetGroup->state));
		}

		return [
			[$group],
			[]
		];
	}


	protected function parentGroupCounts(StatementGroup $group) {
		return $group instanceof LoopBodyStatementGroup;
	}
}
