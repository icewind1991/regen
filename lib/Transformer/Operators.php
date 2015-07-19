<?php

namespace Regen\Transformer;

use PhpParser\Node;
use Regen\Regen;

class Operators implements TransformerInterface {
	/**
	 * @var int
	 */
	private $target;

	/**
	 * Operators constructor.
	 * @param int $target
	 */
	public function __construct($target) {
		$this->target = $target;
	}


	/**
	 * @return string[]
	 */
	public function getTypes() {
		return ['Expr_BinaryOp_Spaceship', 'Expr_BinaryOp_Coalesce', 'Expr_BinaryOp_Pow'];
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
		if ($node instanceof Node\Expr\BinaryOp\Spaceship && $this->target < Regen::TARGET_70) {
			return new Node\Expr\StaticCall(new Node\Name('\Regen\Polyfill\Operators'), 'spaceship',
				[
					new Node\Arg($node->left),
					new Node\Arg($node->right)
				]);
		} elseif ($node instanceof Node\Expr\BinaryOp\Coalesce && $this->target < Regen::TARGET_70) {
			return new Node\Expr\Ternary($this->getIssetAndNotNull($node->left), $node->left, $node->right);
		} elseif ($node instanceof Node\Expr\BinaryOp\Pow && $this->target < Regen::TARGET_56) {
			return new Node\Expr\FuncCall(new Node\Name('pow'), [
				new Node\Arg($node->left),
				new Node\Arg($node->right),
			]);
		} else {
			return null;
		}
	}

	public function getExtraNodes() {
		return [];
	}
}
