<?php

namespace Regen\Transformer\Generator;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

/**
 * Rewrite for statements into while loops
 */
class ForVisitor extends NodeVisitorAbstract {
	public function leaveNode(Node $node) {
		if ($node instanceof Node\Stmt\For_) {
			$conditions = $node->cond;
			$mainCond = array_pop($conditions);
			if (is_null($mainCond)) {
				$mainCond = new Node\Scalar\LNumber(1);
			}
			$body = array_merge($conditions, $node->stmts, $node->loop);
			$nodes = $node->init;
			$nodes[] = new Node\Stmt\While_($mainCond, $body);
			return $nodes;
		} else {
			return null;
		}
	}
}
