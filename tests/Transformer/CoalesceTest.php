<?php

namespace Regen\Tests\Transformer;

use Regen\Transformer\Operators;

class CoalesceTest extends VisitorTest {

	public function comparisonDataProvider() {
		return [
			[1, 2, 1],
			[null, 2, 2],
			[1, null, 1],
			[null, null, null]
		];
	}

	/**
	 * @dataProvider comparisonDataProvider
	 */
	public function testSingleCoalesceCompare($a, $b, $expected) {
		$this->skipIfVersionLowerThan('7.0.0');
		$code = file_get_contents(__DIR__ . '/InputFiles/SingleCoalesce.php');
		$this->assertBeforeAndAfter(
			[$this->visitorFromTransformer(new Operators())],
			$code,
			[$a, $b]
		);
	}

	/**
	 * @dataProvider comparisonDataProvider
	 */
	public function testSingleCoalesceClass($a, $b, $expected) {
		$code = file_get_contents(__DIR__ . '/InputFiles/SingleCoalesce.php');
		$this->assertCodeResult(
			[$this->visitorFromTransformer(new Operators())],
			$code,
			[$a, $b],
			$expected
		);
	}

	public function multipleComparisonDataProvider() {
		return [
			[1, 2, 3, 1],
			[null, 2, 3, 2],
			[1, null, 3, 1],
			[null, null, 3, 3],
			[null, null, null, null]
		];
	}

	/**
	 * @dataProvider multipleComparisonDataProvider
	 */
	public function testMultipleCoalesceCompare($a, $b, $c, $expected) {
		$this->skipIfVersionLowerThan('7.0.0');
		$code = file_get_contents(__DIR__ . '/InputFiles/MultipleCoalesce.php');
		$this->assertBeforeAndAfter(
			[$this->visitorFromTransformer(new Operators())],
			$code,
			[$a, $b, $c]
		);
	}

	/**
	 * @dataProvider multipleComparisonDataProvider
	 */
	public function testMultipleCoalesce($a, $b, $c, $expected) {
		$code = file_get_contents(__DIR__ . '/InputFiles/MultipleCoalesce.php');
		$this->assertCodeResult(
			[$this->visitorFromTransformer(new Operators())],
			$code,
			[$a, $b, $c],
			$expected
		);
	}

	public function arrayComparisonDataProvider() {
		return [
			[[1, 2], 0, 5, 1],
			[[1, 2], 1, 5, 2],
			[[1, 2], 3, 5, 5],
			[[1, 2], -1, 5, 5],
			[[1, 2], 'a', 5, 5],
			[[], 0, 5, 5]
		];
	}

	/**
	 * @dataProvider arrayComparisonDataProvider
	 */
	public function testArrayCoalesceCompare($a, $b, $c, $expected) {
		$this->skipIfVersionLowerThan('7.0.0');
		$code = file_get_contents(__DIR__ . '/InputFiles/ArrayCoalesce.php');
		$this->assertBeforeAndAfter(
			[$this->visitorFromTransformer(new Operators())],
			$code,
			[$a, $b, $c]
		);
	}

	/**
	 * @dataProvider arrayComparisonDataProvider
	 */
	public function testArraySingleCoalesceClass($a, $b, $c, $expected) {
		$code = file_get_contents(__DIR__ . '/InputFiles/ArrayCoalesce.php');
		$this->assertCodeResult(
			[$this->visitorFromTransformer(new Operators())],
			$code,
			[$a, $b, $c],
			$expected
		);
	}
}
