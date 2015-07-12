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
		return ['Stmt_ClassMethod'];
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
		if ($node instanceof Node\Stmt\ClassMethod) {
			foreach ($node->params as $param) {
				if ($param->type) {
					$typeName = $param->type->toString();
					if (isset($this->typeChecks[$typeName])) {
						array_unshift($node->stmts, $this->buildTypeCheck($param->name, $typeName));
						$param->type = null;
					}
				}
			}
			if ($node->returnType) {
				$returnType = (string)$node->returnType;
				$node->returnType = null;
				$traverser = new NodeTraverser();
				$traverser->addVisitor(new ReturnTypeVisitor($this->buildTypeCheck('__return', $returnType)));
				$node->stmts = $traverser->traverse($node->stmts);
			}
		}
	}

	public function getExtraNodes() {
		return [];
	}
}
