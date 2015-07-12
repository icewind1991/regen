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
	 * @var StatementGroup
	 */
	public $nextSibling;

	/**
	 * @var StatementGroup
	 */
	public $parent;

	/**
	 * @param \PhpParser\Node[] $statements
	 * @param int $state
	 * @param StatementGroup $nextSibling
	 * @param StatementGroup $parent
	 */
	public function __construct(array $statements, $state, $nextSibling, $parent) {
		$this->statements = $statements;
		$this->state = $state;
		$this->nextSibling = $nextSibling;
		$this->parent = $parent;
	}

	public function findNextSibling() {
		if ($this->nextSibling) {
			return $this->nextSibling;
		} elseif ($this->parent) {
			return $this->parent->findNextSibling();
		} else {
			return null;
		}
	}
}
