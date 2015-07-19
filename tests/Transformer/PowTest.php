<?php

namespace Regen\Tests\Transformer;

use Regen\Regen;
use Regen\Transformer\Operators;

class PowTest extends VisitorTest {

	public function powDataProvider() {
		return [
			[1, 1, 1],
			[1, 5, 1],
			[5, 2, 25]
		];
	}

	/**
	 * @dataProvider powDataProvider
	 */
	public function testBasicSpaceshipCompare($a, $b) {
		$this->skipIfVersionLowerThan('5.6.0');
		$code = file_get_contents(__DIR__ . '/InputFiles/PowOperator.php');
		$this->assertBeforeAndAfter(
			[$this->visitorFromTransformer(new Operators(Regen::TARGET_54))],
			$code,
			[$a, $b]
		);
	}

	/**
	 * @dataProvider powDataProvider
	 */
	public function testBasicSpaceship($a, $b, $expected) {
		$code = file_get_contents(__DIR__ . '/InputFiles/PowOperator.php');
		$this->assertCodeResult(
			[$this->visitorFromTransformer(new Operators(Regen::TARGET_54))],
			$code,
			[$a, $b],
			$expected
		);
	}
}
