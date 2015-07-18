<?php

namespace Regen\Transformer;

use PhpParser\Node;

class VariadicFunctionVisitor extends BaseVisitor {

	public function leaveNode(Node $node) {
		if ($this->isFunctionOrClosure($node)) {
			/** @var Node\Stmt\Function_|Node\Expr\Closure $node */
			$params = $node->params;
			$lastParam = array_pop($params);
			if ($this->isVariadic($lastParam)) {
				$node->params = [];
				$getParams = $this->assignValue($lastParam->name, null, new Node\Expr\FuncCall(new Node\Name('func_get_args')));
				$assignments = array_map(function (Node\Param $param) use ($lastParam) {
					return $this->assignValue($param->name, null, new Node\Expr\FuncCall(
						new Node\Name('array_shift'),
						[new Node\Arg(new Node\Expr\Variable($lastParam->name))]
					));
				}, $params);
				$node->stmts = array_merge([$getParams], $assignments, $node->stmts);
			}
			return null;
		} else {
			return null;
		}
	}

	/**
	 * @param Node\Param|null $param
	 * @return bool
	 */
	protected function isVariadic($param) {
		return $param instanceof Node\Param && $param->variadic;
	}
}
