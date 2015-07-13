<?php

namespace Regen\Tests\Transformer;

use Regen\Tests\Polyfill\OperatorTest;
use Regen\Transformer\Operators;

class SpaceshipTest extends VisitorTest {

	public function comparisonDataProvider() {
		$polyfillTest = new OperatorTest();
		return $polyfillTest->spaceshipProvider();
	}

	/**
	 * @dataProvider comparisonDataProvider
	 */
	public function testBasicSpaceshipCompare($a, $b, $expected) {
		$this->skipIfVersionLowerThan('7.0.0');
		$code = file_get_contents(__DIR__ . '/InputFiles/Spaceship.php');
		$this->assertBeforeAndAfter(
			[$this->visitorFromTransformer(new Operators())],
			$code,
			[$a, $b]
		);
	}

	/**
	 * @dataProvider comparisonDataProvider
	 */
	public function testBasicSpaceship($a, $b, $expected) {
		$code = file_get_contents(__DIR__ . '/InputFiles/Spaceship.php');
		$this->assertCodeResult(
			[$this->visitorFromTransformer(new Operators())],
			$code,
			[$a, $b],
			$expected
		);
	}
}
