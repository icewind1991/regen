<?php

namespace Regen\Tests\Transformer\Generator;

use Regen\Tests\Transformer\VisitorTest;
use Regen\Transformer\Generator\ForVisitor;

class ForVisitorTest extends VisitorTest {
	public function testBasicLoop() {
		$code = file_get_contents(__DIR__ . '/InputFiles/BasicForLoop.php');
		$this->assertBeforeAndAfter(
			[new ForVisitor()],
			$code,
			[1, 5, 2]
		);

		$this->assertCodeResult(
			[new ForVisitor()],
			$code,
			[1, 5, 2],
			[1, 3, 5]
		);
	}

	public function testEmptyLoop() {
		$code = file_get_contents(__DIR__ . '/InputFiles/EmptyForLoop.php');
		$this->assertBeforeAndAfter(
			[new ForVisitor()],
			$code,
			[1, 5, 2]
		);

		$this->assertCodeResult(
			[new ForVisitor()],
			$code,
			[1, 5, 2],
			[1, 3, 5]
		);
	}
}
