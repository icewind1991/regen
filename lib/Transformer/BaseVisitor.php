<?php

namespace Regen\Transformer;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

class BaseVisitor extends NodeVisitorAbstract {
	/**
	 * @param string $variable
	 * @param string $method
	 * @param array $args
	 * @return Node\Expr\MethodCall
	 */
	protected function getMethodCall($variable, $method, array $args = []) {
		return new Node\Expr\MethodCall(new Node\Expr\Variable($variable), $method, $args);
	}

	/**
	 * @param string $variable
	 * @param string $param
	 * @return Node\Expr\PropertyFetch
	 */
	protected function getProperty($variable, $param) {
		return new Node\Expr\PropertyFetch(
			new Node\Expr\Variable($variable), $param
		);
	}

	/**
	 * @param string $variable
	 * @param string|null $param
	 * @param mixed $value
	 * @return Node\Expr\Assign
	 */
	protected function assignValue($variable, $param, $value) {
		if (!is_null($param)) {
			$var = $this->getProperty($variable, $param);
		} else {
			$var = new Node\Expr\Variable($variable);
		}
		return new Node\Expr\Assign($var, $this->getValue($value));
	}

	/**
	 * @param mixed $value
	 * @return Node\Expr|null
	 * @throws \InvalidArgumentException
	 */
	protected function getValue($value) {
		if ($value instanceof Node\Expr) {
			return $value;
		}
		if (is_int($value)) {
			return new Node\Scalar\LNumber($value);
		}
		throw new \InvalidArgumentException();
	}

	/**
	 * @param Node[] $nodes
	 * @param NodeVisitorAbstract[] $visitors
	 * @return Node[]
	 */
	protected function traverseNodes($nodes, $visitors) {
		$traverser = new NodeTraverser();
		foreach ($visitors as $visitor) {
			$traverser->addVisitor($visitor);
		}
		return $traverser->traverse($nodes);
	}
}
