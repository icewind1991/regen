<?php

namespace Regen\Transformer\Generator;

use PhpParser\Node;
use Regen\Transformer\BaseVisitor;

/**
 * Fix continue statements in for loops
 */
class ForContinueVisitor extends BaseVisitor {
	/**
	 * @var Node[]
	 */
	private $stepStatements;

	/**
	 * @param \PhpParser\Node[] $stepStatements
	 */
	public function __construct(array $stepStatements) {
		$this->stepStatements = $stepStatements;
	}

	public function leaveNode(Node $node) {
		if ($node instanceof Node\Stmt\Continue_) {
			return array_merge($this->stepStatements, [$node]);
		} else {
			return null;
		}
	}
}
