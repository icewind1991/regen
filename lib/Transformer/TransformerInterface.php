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

	/**
	 * Get any extra nodes which should be added to the result code
	 *
	 * @return Node[]
	 */
	public function getExtraNodes();

	/**
	 * Apply the transformation on a node
	 *
	 * @param Node $node
	 * @return Node|null
	 */
	public function apply(Node $node);
}
