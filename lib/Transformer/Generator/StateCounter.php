<?php

namespace Regen\Transformer\Generator;

class StateCounter {
	private $lastState = -1;

	/**
	 * @return int
	 */
	public function getNextState() {
		$this->lastState++;
		return $this->lastState;
	}
}
