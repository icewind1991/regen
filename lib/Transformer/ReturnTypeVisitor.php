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
			$temporaryVar = new Node\Expr\Variable('__return');
			$assign = new Node\Expr\Assign($temporaryVar, $node->expr);
			$check->else = new Node\Stmt\Else_([new Node\Stmt\Return_($temporaryVar)]);
			return [$assign, $check];
		} else {
			return null;
		}
	}
}
