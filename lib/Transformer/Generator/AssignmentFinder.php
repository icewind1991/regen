<?php

namespace Regen\Transformer\Generator;

use PhpParser\Node;
use PhpParser\NodeTraverser;

/**
 * Check if a function is a generator
 */
class AssignmentFinder {
	public function getNames(Node $function) {
		if (!$function instanceof Node\Stmt\Function_ and !$function instanceof Node\Expr\Closure) {
			throw new \InvalidArgumentException('Not a closure or function');
		}
		$traverser = new NodeTraverser();
		$visitor = new AssignmentVisitor();
		$traverser->addVisitor($visitor);
		$traverser->traverse($function->stmts);
		return array_unique($visitor->getVariables());
	}
}
