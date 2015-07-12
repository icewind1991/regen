<?php

namespace Regen\Transformer\Generator;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

/**
 * Check if a function is a generator
 */
class AssignmentVisitor extends NodeVisitorAbstract
{
	/**
	 * @var Node\Name[]
	 */
	private $variables = [];

	/**
	 * @var string[]
	 */
	private $exclude = ['Stmt_Function', 'Expr_Closure'];

	/**
	 * @return Node\Name[]
	 */
	public function getVariables() {
		return $this->variables;
	}

	public function enterNode(Node $node) {
		if (in_array($node->getType(), $this->exclude)) {
			return NodeTraverser::DONT_TRAVERSE_CHILDREN;
		}
		if ($node instanceof Node\Expr\Assign) {
			if ($node->var instanceof Node\Expr\Variable) {
				$this->variables[] = $node->var->name;
			}
		}
		return null;
	}
}
