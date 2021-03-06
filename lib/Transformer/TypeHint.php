<?php

namespace Regen\Transformer;

use PhpParser\Node;
use PhpParser\NodeTraverser;

/**
 * scalar type hints and return type hints
 */
class TypeHint implements TransformerInterface {

	private $typeChecks = [
		'string' => 'is_string',
		'bool' => 'is_bool',
		'boolean' => 'is_bool',
		'float' => 'is_float',
		'int' => 'is_int',
		'integer' => 'is_int'
	];

	/**
	 * @return string[]
	 */
	public function getTypes() {
		return ['Stmt_ClassMethod', 'Stmt_Function', 'Expr_Closure'];
	}

	/**
	 * @param $varName
	 * @param $type
	 * @return Node\Stmt\If_
	 */
	protected function buildTypeCheck($varName, $type) {
		$variable = new Node\Expr\Variable($varName);
		if (isset($this->typeChecks[$type])) {
			$checkMethod = $this->typeChecks[$type];
			return new Node\Stmt\If_(new Node\Expr\BooleanNot(new Node\Expr\FuncCall(new Node\Name($checkMethod),
				[new Node\Arg($variable)])),
				[
					'stmts' => [
						new Node\Stmt\Throw_(new Node\Expr\New_(new Node\Name('\TypeError')))
					]
				]);
		} else {
			return new Node\Stmt\If_(new Node\Expr\Instanceof_($variable, new Node\Name($type)));
		}
	}

	public function apply(Node $node) {
		if ($node instanceof Node\Stmt\ClassMethod ||
			$node instanceof Node\Stmt\Function_ ||
			$node instanceof Node\Expr\Closure
		) {
			$this->applyParamType($node);
			$this->applyReturnType($node);
		}
	}

	/**
	 * @param Node\Stmt\ClassMethod|Node\Stmt\Function_|Node\Expr\Closure $node
	 */
	protected function applyReturnType($node) {
		if ($node->returnType) {
			$returnType = (string)$node->returnType;
			$node->returnType = null;
			$traverser = new NodeTraverser();
			$traverser->addVisitor(new ReturnTypeVisitor($this->buildTypeCheck('__return', $returnType)));
			$node->stmts = $traverser->traverse($node->stmts);
		}
	}

	/**
	 * @param Node\Stmt\ClassMethod|Node\Stmt\Function_|Node\Expr\Closure $node
	 */
	protected function applyParamType($node) {
		foreach ($node->params as $param) {
			if ($param->type) {
				$typeName = $param->type->toString();
				if (isset($this->typeChecks[$typeName])) {
					array_unshift($node->stmts, $this->buildTypeCheck($param->name, $typeName));
					$param->type = null;
				}
			}
		}
	}

	public function getExtraNodes() {
		return [];
	}
}
