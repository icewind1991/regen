<?php

namespace Regen\Tests\Transformer\Generator;

use Regen\Tests\Transformer\CompatibleVisitorTest;
use Regen\Transformer\Generator\ForVisitor;
use Regen\Transformer\Generator\GeneratorVisitor;

class GeneratorVisitorTest extends CompatibleVisitorTest {
	public function testBasicGenerator() {
		$this->skipIfVersionLowerThan('5.5.0');
		$code = file_get_contents(__DIR__ . '/BasicGenerator.php');
		$this->assertBeforeAndAfter(
			[new GeneratorVisitor()],
			$code,
			[true, 1, 2]
		);

		$this->assertCodeResult(
			[new ForVisitor(), new GeneratorVisitor()],
			$code,
			[true, 1, 2],
			[1, 2]
		);
	}

	public function testLoopingGenerator() {
		$this->skipIfVersionLowerThan('5.5.0');
		$code = file_get_contents(__DIR__ . '/LoopingGenerator.php');
		$this->assertBeforeAndAfter(
			[new ForVisitor(), new GeneratorVisitor()],
			$code,
			[1, 5, 2]
		);

		$this->assertCodeResult(
			[new ForVisitor(), new GeneratorVisitor()],
			$code,
			[1, 5, 2],
			[1, 3, 5]
		);
	}
}
