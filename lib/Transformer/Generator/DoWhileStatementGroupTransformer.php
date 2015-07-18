<?php

namespace Regen\Transformer\Generator;

use PhpParser\Node;

class DoWhileStatementGroupTransformer extends WhileStatementGroupTransformer {
	/**
	 * @param Node[] $statements
	 * @param Node\Expr $condition
	 * @param Node $loopEndStatement
	 * @return Node[]
	 */
	protected function addLoopCondition(array $statements, $condition, $loopEndStatement) {
		array_push($statements, new Node\Stmt\If_(new Node\Expr\BooleanNot($condition), [
			'stmts' => [$loopEndStatement, new Node\Stmt\Break_()]
		]));
		return $statements;
	}
}
