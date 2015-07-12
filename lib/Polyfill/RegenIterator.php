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

	public function rewind() {
		$this->context->rewind();
	}

	public function current() {
		if ($this->counter === 0) {
			$callback = $this->callback;
			$this->lastValue = $callback($this->context);
		}
		return $this->lastValue;
	}

	public function key() {
		return $this->counter;
	}

	public function next() {
		$this->counter++;
		$callback = $this->callback;
		$this->lastValue = $callback($this->context);
	}

	public function valid() {
		return $this->context->active;
	}
}
