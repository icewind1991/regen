<?php

namespace Regen\Transformer;

use PhpParser\Node;

interface TransformerInterface {
	/**
	 * Get the types of nodes this transformer applies on
	 *
	 * @return string[]
	 */
	public function getTypes();

	public function getExtraNodes();

	/**
	 * Apply the transformation on a node
	 *
	 * @param Node $node
	 */
	public function apply(Node $node);
}
