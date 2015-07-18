<?php

namespace Regen\Tests\Transformer;

use Regen\Tests\Polyfill\OperatorTest;
use Regen\Transformer\ArgumentUnpackingVisitor;
use Regen\Transformer\Operators;
use Regen\Transformer\VariadicFunctionVisitor;

class ArgumentUnpackingVisitorTest extends VisitorTest {
	public function testVariadicFunctionCompare() {
		$this->skipIfVersionLowerThan('5.6.0');
		$code = file_get_contents(__DIR__ . '/InputFiles/ArgumentUnpacking.php');
		$this->assertBeforeAndAfter(
			[new ArgumentUnpackingVisitor()],
			$code,
			[[1], [2], [[3], [4], [5]]]
		);

		$this->skipIfVersionLowerThan('5.6.0');
		$code = file_get_contents(__DIR__ . '/InputFiles/ArgumentUnpacking.php');
		$this->assertBeforeAndAfter(
			[new ArgumentUnpackingVisitor()],
			$code,
			[[1], [2], []]
		);
	}

	public function testVariadicFunction() {
		$code = file_get_contents(__DIR__ . '/InputFiles/ArgumentUnpacking.php');
		$this->assertCodeResult(
			[new ArgumentUnpackingVisitor()],
			$code,
			[[1], [2], [[3], [4], [5]]],
			[1, 2, 3, 4, 5]
		);

		$this->assertCodeResult(
			[new ArgumentUnpackingVisitor()],
			$code,
			[[1], [2], []],
			[1, 2]
		);
	}
}
