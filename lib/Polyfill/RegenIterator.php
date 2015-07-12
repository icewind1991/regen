<?php

namespace Regen\Polyfill;

class RegenIterator implements \Iterator {
	/**
	 * @var callable
	 */
	private $callback;

	/**
	 * @var GeneratorContext
	 */
	private $context;

	private $counter = 0;

	private $lastValue = null;

	public function __construct(callable $callback) {
		$this->callback = $callback;
		$this->context = new GeneratorContext();
	}

	function rewind() {
		$this->context->rewind();
	}

	function current() {
		if ($this->counter === 0) {
			$callback = $this->callback;
			$this->lastValue = $callback($this->context);
		}
		return $this->lastValue;
	}

	function key() {
		return $this->counter;
	}

	function next() {
		$this->counter++;
		$callback = $this->callback;
		$this->lastValue = $callback($this->context);
	}

	function valid() {
		return $this->context->active;
	}
}
