<?php

namespace Regen\Transformer\Generator;

use PhpParser\Node;

class WhileStatementGroupTransformer extends StatementGroupTransformer {
	/**
	 * @param StatementGroup $group
	 * @return array [StatementGroup[] $doneGroups, StatementGroup[] $newGroups]
	 */
	public function transformGroup(StatementGroup $group) {
		$statement = end($group->statements);

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
		$inWhileGroup = $this->buildGroupForStatements($childStatements, $inWhileState, $group->nextSibling, $group);
		$group->nextSibling = $inWhileGroup; // save the start of the while loop
		$oldGroupStatements[] = $this->getStateAssignment($inWhileGroup->state);
		$beforeWhileGroup = $this->buildGroupForStatements($oldGroupStatements, $group->state, $inWhileGroup, $group->parent);

		return [
			[$beforeWhileGroup],
			[$inWhileGroup]
		];
	}
}
