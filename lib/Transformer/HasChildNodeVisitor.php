<?php

namespace Regen\Transformer;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

/**
 * Check if a function is a generator
 */
class HasChildNodeVisitor extends NodeVisitorAbstract {
	/**
	 * @var string
	 */
	private $type;

	private $exists = false;

	/**
	 * @var string[]
	 */
	private $exclude;

	/**
	 * HasChildNodeVistor constructor.
	 *
	 * @param string $type
	 * @param string[] $exclude dont travel into these node types
	 */
	public function __construct($type, $exclude = []) {
		$this->type = $type;
		$this->exclude = $exclude;
	}

	/**
	 * @return boolean
	 */
	public function exists() {
		return $this->exists;
	}


	public function enterNode(Node $node) {
		if (in_array($node->getType(), $this->exclude)) {
			return NodeTraverser::DONT_TRAVERSE_CHILDREN;
		}
		if ($node->getType() === $this->type) {
			$this->exists = true;
		}
	}
}
