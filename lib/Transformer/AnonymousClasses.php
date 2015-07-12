<?php

namespace Regen\Transformer;

use PhpParser\Node;

class AnonymousClasses implements TransformerInterface {
	private $extraNodes = [];

	/**
	 * @return string[]
	 */
	public function getTypes() {
		return ['Expr_New'];
	}

	public function getExtraNodes() {
		$nodes = $this->extraNodes;
		$this->extraNodes = [];
		return $nodes;
	}


	public function apply(Node $node) {
		if ($node instanceof Node\Expr\New_) {
			if ($node->class instanceof Node\Stmt\Class_) {
				$class = $node->class;
				$name = uniqid('class_');
				$nameNode = new Node\Name($name);
				$class->name = $nameNode;
				$node->class = $nameNode;
				$this->extraNodes[] = $class;
			}
		}
	}
}
