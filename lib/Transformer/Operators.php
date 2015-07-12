<?php

namespace Regen\Transformer;

use PhpParser\Node;

class Operators implements TransformerInterface {
	/**
	 * @return string[]
	 */
	public function getTypes() {
		return ['Expr_BinaryOp_Spaceship', 'Expr_BinaryOp_Coalesce'];
	}

	/**
	 * @param Node\Expr $expr
	 */
	protected function getIssetAndNotNull($expr) {
		return new Node\Expr\BinaryOp\BooleanAnd(
			new Node\Expr\Isset_([$expr]),
			new Node\Expr\BooleanNot(
				new Node\Expr\FuncCall(new Node\Name('is_null'), [$expr])
			)
		);
	}

	public function apply(Node $node) {
		if ($node instanceof Node\Expr\BinaryOp\Spaceship) {
			return new Node\Expr\StaticCall(new Node\Name('\Regen\Polyfill\Operators'), 'spaceship',
				[
					$node->left,
					$node->right
				]);
		} elseif ($node instanceof Node\Expr\BinaryOp\Coalesce) {
			return new Node\Expr\Ternary($this->getIssetAndNotNull($node->left), $node->left, $node->right);
		}
	}

	public function getExtraNodes() {
		return [];
	}
}
