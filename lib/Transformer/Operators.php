<?php

namespace Regen\Transformer;

use PhpParser\Node;

class Operators implements TransformerInterface
{
	/**
	 * @return string[]
	 */
	public function getTypes() {
		return ['Expr_BinaryOp_Spaceship', 'Expr_BinaryOp_Coalesce'];
	}

	/**
	 * @param Node\Expr $expr
	 * @return Node\Expr\BinaryOp\BooleanAnd
	 */
	protected function getIssetAndNotNull($expr) {
		return new Node\Expr\BinaryOp\BooleanAnd(
			new Node\Expr\Isset_([$expr]),
			new Node\Expr\BooleanNot(
				new Node\Expr\FuncCall(new Node\Name('is_null'), [new Node\Arg($expr)])
			)
		);
	}

	public function apply(Node $node) {
		if ($node instanceof Node\Expr\BinaryOp\Spaceship) {
			return new Node\Expr\StaticCall(new Node\Name('\Regen\Polyfill\Operators'), 'spaceship',
				[
					new Node\Arg($node->left),
					new Node\Arg($node->right)
				]);
		} elseif ($node instanceof Node\Expr\BinaryOp\Coalesce) {
			return new Node\Expr\Ternary($this->getIssetAndNotNull($node->left), $node->left, $node->right);
		} else {
			return null;
		}
	}

	public function getExtraNodes() {
		return [];
	}
}
