<?php

namespace Regen\Transformer;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class ReturnTypeVisitor extends NodeVisitorAbstract {
	private $returnTypeCheck;

	public function  __construct(Node\Stmt\IF_ $check) {
		$this->returnTypeCheck = $check;
	}

	public function leaveNode(Node $node) {
		if ($node instanceof Node\Stmt\Return_) {
			$check = clone($this->returnTypeCheck);
			if (is_null($node->expr)) {
				$node->expr = new Node\Expr\Cast\Unset_(new Node\Scalar\LNumber(0));
			}
			$temporaryVar = new Node\Expr\Variable('__return');
			$check->else = new Node\Stmt\Else_([new Node\Stmt\Return_($temporaryVar)]);
			$assign = new Node\Expr\Assign($temporaryVar, $node->expr);
			return [$assign, $check];
		} else {
			return null;
		}
	}
}
