<?php

namespace Regen\Transformer\Generator;

use PhpParser\Node;

class YieldStatementGroupTransformer extends StatementGroupTransformer {
	/**
	 * @param StatementGroup $group
	 * @return array [StatementGroup[] $doneGroups, StatementGroup[] $newGroups]
	 */
	public function transformGroup(StatementGroup $group) {
		$statement = array_pop($group->statements);

		if ($statement instanceof Node\Expr\Yield_) {
			$newStatement = new Node\Stmt\Return_($statement->value);
			array_push($group->statements, $this->getGroupEndCall($group));

			array_push($group->statements, $newStatement);
		}

		return [
			[$group],
			[]
		];
	}
}
