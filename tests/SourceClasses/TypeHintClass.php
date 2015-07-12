<?php

namespace Regen\Tests\SourceClasses;

/**
 * Class with no php7 type hints
 */
class TypeHintClass {
	public function test(string $object): string {
		return 'hello ' . $object;
	}

	public function testInvalidReturn(string $object): string {
		return 'hello ' + $object;
	}
}
