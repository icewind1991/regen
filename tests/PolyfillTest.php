<?php

namespace Regen\Tests;

use Regen\Polyfill\Operators;

class PolyfillTest extends TestCase {
	public function spaceshipProvider() {
		return [
			[0, 0, 0],
			[1, 0, 1],
			[0, 1, -1],
			[1.1, 1, 1],
			['a', 'a', 0],
			['a', 'b', -1],
			['b', 'a', 1],
			['aa', 'a', 1],
			[[1], [1], 0],
			[[1], [0], 1],
			[[0], [1], -1],
			[[1, 1], [1, 0], 1],
			[[1, 1], [1, 1], 0],
			[[1, 0], [1, 1], -1],
			[[1, 0], [1], 1],
			[[1], [1, 1], -1],
		];
	}

	/**
	 * @param mixed $a
	 * @param mixed $b
	 * @param int $expected
	 * @dataProvider spaceshipProvider
	 */
	public function testSpaceship($a, $b, $expected) {
		$this->assertEquals($expected, Operators::spaceship($a, $b));
	}
}
