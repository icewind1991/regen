<?php

namespace Regen\Tests\Transformer;

use Regen\Tests\Polyfill\OperatorTest;
use Regen\Transformer\Operators;
use Regen\Transformer\VariadicFunctionVisitor;

class VariadicFunctionVisitorTest extends VisitorTest {
	public function testVariadicFunctionCompare() {
		$this->skipIfVersionLowerThan('5.6.0');
		$code = file_get_contents(__DIR__ . '/InputFiles/VariadicFunction.php');
		$this->assertBeforeAndAfter(
			[new VariadicFunctionVisitor()],
			$code,
			[1, 2, 3, 4, 5]
		);

		$this->skipIfVersionLowerThan('5.6.0');
		$code = file_get_contents(__DIR__ . '/InputFiles/VariadicFunction.php');
		$this->assertBeforeAndAfter(
			[new VariadicFunctionVisitor()],
			$code,
			[1, 2]
		);
	}

	public function testVariadicFunction() {
		$code = file_get_contents(__DIR__ . '/InputFiles/VariadicFunction.php');
		$this->assertCodeResult(
			[new VariadicFunctionVisitor()],
			$code,
			[1, 2, 3, 4, 5],
			[1, 2, [3, 4, 5]]
		);

		$this->assertCodeResult(
			[new VariadicFunctionVisitor()],
			$code,
			[1, 2],
			[1, 2, []]
		);
	}
}
