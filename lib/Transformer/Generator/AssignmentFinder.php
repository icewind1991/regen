<?php

namespace Regen\Transformer\Generator;

use PhpParser\Node;
use PhpParser\NodeTraverser;

/**
 * Get a list of all variables which are assigned in a function
 */
class AssignmentFinder {
	public function getNames(Node $function) {
		if (!$function instanceof Node\Stmt\Function_ && !$function instanceof Node\Expr\Closure) {
			throw new \InvalidArgumentException('Not a closure or function');
		}
		$traverser = new NodeTraverser();
		$visitor = new AssignmentVisitor();
		$traverser->addVisitor($visitor);
		$traverser->traverse($function->stmts);
		return array_unique($visitor->getVariables());
	}
}
