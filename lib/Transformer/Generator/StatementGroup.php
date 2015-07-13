<?php

namespace Regen\Transformer\Generator;

use PhpParser\Node;

class StatementGroup {
	/**
	 * @var Node[]
	 */
	public $statements;

	/**
	 * @var int
	 */
	public $state;

	/**
	 * @var StatementGroup|null
	 */
	public $nextSibling;

	/**
	 * @var StatementGroup|null
	 */
	public $parent;

	/**
	 * @param \PhpParser\Node[] $statements
	 * @param int $state
	 * @param StatementGroup|null $nextSibling
	 * @param StatementGroup|null $parent
	 */
	public function __construct(array $statements, $state, $nextSibling, $parent) {
		$this->statements = $statements;
		$this->state = $state;
		$this->nextSibling = $nextSibling;
		$this->parent = $parent;
	}

	/**
	 * @return StatementGroup|null
	 */
	public function findNextSibling() {
		if ($this->nextSibling) {
			return $this->nextSibling;
		} elseif ($this->parent) {
			return $this->parent->findNextSibling();
		} else {
			return null;
		}
	}

	/**
	 * @param StateCounter $counter
	 * @return StatementGroupTransformer
	 */
	public function getTransformer(StateCounter $counter) {
		return new StatementGroupTransformer($counter);
	}
}
