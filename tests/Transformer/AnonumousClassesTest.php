<?php

namespace Regen\Tests\Transformer\Generator;

use Regen\Tests\Transformer\CompatibleVisitorTest;
use Regen\Transformer\AnonymousClasses;

class AnonymousClassesTest extends CompatibleVisitorTest {
	public function testBasicClassCompare() {
		$this->skipIfVersionLowerThan('7.0.0');
		$code = file_get_contents(__DIR__ . '/BasicAnonymousClass.php');
		$this->assertBeforeAndAfter(
			[$this->visitorFromTransformer(new AnonymousClasses())],
			$code,
			[1]
		);
	}

	public function testBasicClass() {
		$code = file_get_contents(__DIR__ . '/BasicAnonymousClass.php');
		$this->assertCodeResult(
			[$this->visitorFromTransformer(new AnonymousClasses())],
			$code,
			[1],
			1
		);
	}
}
