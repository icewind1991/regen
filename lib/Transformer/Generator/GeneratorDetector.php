<?php

namespace Regen\Transformer\Generator;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use Regen\Transformer\HasChildNodeVisitor;

/**
 * Check if a function is a generator
 */
class GeneratorDetector {
	public function isGenerator(Node $function) {
		if (!$function instanceof Node\Stmt\Function_ && !$function instanceof Node\Expr\Closure) {
			throw new \InvalidArgumentException('Not a closure or function');
		}
		$traverser = new NodeTraverser();
		$visitor = new HasChildNodeVisitor('Expr_Yield', ['Stmt_Function', 'Expr_Closure']);
		$traverser->addVisitor($visitor);
		$traverser->traverse($function->stmts);
		return $visitor->exists();
	}
}
