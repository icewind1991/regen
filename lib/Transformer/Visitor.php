<?php

namespace Regen\Transformer;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class Visitor extends NodeVisitorAbstract {
	/**
	 * @var TransformerInterface[]
	 */
	private $transformers = [];

	public function addTransformer(TransformerInterface $transformer) {
		$this->transformers[] = $transformer;
	}

	/**
	 * @return TransformerInterface[]
	 */
	public function getTransformers() {
		return $this->transformers;
	}

	/**
	 * @return Node[]
	 */
	public function getExtraNodes() {
		return array_reduce($this->transformers, function ($nodes, TransformerInterface $transformer) {
			return array_merge($nodes, $transformer->getExtraNodes());
		}, []);
	}

	public function enterNode(Node $node) {
		foreach ($this->transformers as $transformer) {
			if (in_array($node->getType(), $transformer->getTypes())) {
				$result = $transformer->apply($node);
				if ($result instanceof Node) {
					$node = $result;
				}
			}
		}
		return $node;
	}
}
