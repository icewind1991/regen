<?php

namespace Regen\Polyfill;

class GeneratorContext {
	public $current = 0;
	public $next = 0;
	public $active = true;

	public function rewind() {
		$this->current = 0;
		$this->next = 0;
	}

	public function stop() {
		$this->active = false;
	}
}
