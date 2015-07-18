<?php

namespace Regen\Transformer;

use PhpParser\Node;

class ArgumentUnpackingVisitor extends BaseVisitor {

	public function leaveNode(Node $node) {
		if ($node instanceof Node\Expr\FuncCall) {
			$args = $node->args;
			$lastArg = array_pop($args);
			if ($lastArg instanceof Node\Arg) {
				if ($lastArg->unpack) {
					$argumentValues = array_map(function (Node\Arg $arg) {
						return new Node\Expr\ArrayItem($arg->value);
					}, $args);
					$allArgs = new Node\Expr\FuncCall(new Node\Name('array_merge'), [
						new Node\Expr\Array_($argumentValues),
						$lastArg->value
					]);
					return new Node\Expr\FuncCall(new Node\Name('call_user_func_array'), [
						new Node\Arg(new Node\Scalar\String_($node->name)),
						new Node\Arg($allArgs)
					]);
				}
			}
			return null;
		} else {
			return null;
		}
	}
}
