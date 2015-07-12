<?php

namespace Regen\Tests\SourceClasses;

/**
 * Class with no php7 features
 */
class BasicClass {
	public function test($object) {
		return 'hello ' . $object;
	}
}
