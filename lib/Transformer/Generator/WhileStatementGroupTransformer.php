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

		$loopEndStatement = $this->getGroupEndCall($group);
		$childStatements = $statement->stmts;
		$childStatements = $this->addLoopCondition($childStatements, $statement->cond, $loopEndStatement);
		$inWhileState = $this->stateCounter->getNextState();
		$childStatements[] = $this->getStateAssignment($inWhileState);
		$oldGroupStatements = $group->statements;
		array_pop($oldGroupStatements); //remove the while
		$loopBodyGroup = new LoopBodyStatementGroup([], $inWhileState, $group->nextSibling, $group);
		$inWhileGroup = $this->buildGroupForStatements($childStatements, $inWhileState, $group->nextSibling, $loopBodyGroup);
		$oldGroupStatements[] = $this->getStateAssignment($inWhileGroup->state);
		$beforeWhileGroup = $this->buildGroupForStatements($oldGroupStatements, $group->state, $inWhileGroup, $group->parent);

		return [
			[$beforeWhileGroup],
			[$inWhileGroup]
		];
	}

	/**
	 * @param Node[] $statements
	 * @param Node\Expr $condition
	 * @param Node $loopEndStatement
	 * @return Node[]
	 */
	protected function addLoopCondition(array $statements, $condition, $loopEndStatement) {
		array_unshift($statements, new Node\Stmt\If_(new Node\Expr\BooleanNot($condition), [
			'stmts' => [$loopEndStatement, new Node\Stmt\Break_()]
		]));
		return $statements;
	}
}
