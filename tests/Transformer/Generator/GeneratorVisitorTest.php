<?php

namespace Regen\Tests\Transformer\Generator;

use Regen\Tests\Transformer\VisitorTest;
use Regen\Transformer\Generator\ForVisitor;
use Regen\Transformer\Generator\GeneratorVisitor;

class GeneratorVisitorTest extends VisitorTest {
	public function testBasicGenerator() {
		$this->skipIfVersionLowerThan('5.5.0');
		$code = file_get_contents(__DIR__ . '/InputFiles/BasicGenerator.php');
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
		$code = file_get_contents(__DIR__ . '/InputFiles/LoopingGenerator.php');
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

	public function testBreakLoopingGenerator() {
		$this->skipIfVersionLowerThan('5.5.0');
		$code = file_get_contents(__DIR__ . '/InputFiles/BreakLoopingGenerator.php');
		$this->assertBeforeAndAfter(
			[new ForVisitor(), new GeneratorVisitor()],
			$code,
			[4, 3]
		);

		$this->assertCodeResult(
			[new ForVisitor(), new GeneratorVisitor()],
			$code,
			[4, 3],
			[1, 2, 3, 99]
		);
	}

	public function testDeepBreakLoopingGenerator() {
		$this->skipIfVersionLowerThan('5.5.0');
		$code = file_get_contents(__DIR__ . '/InputFiles/DeepBreakLoopingGenerator.php');
		$this->assertBeforeAndAfter(
			[new ForVisitor(), new GeneratorVisitor()],
			$code,
			[15, 3]
		);

		$this->assertCodeResult(
			[new ForVisitor(), new GeneratorVisitor()],
			$code,
			[15, 3],
			[3, 6, 9, 12, 15, 99]
		);
	}

	public function testSwitchGenerator() {
		$this->skipIfVersionLowerThan('5.5.0');
		$code = file_get_contents(__DIR__ . '/InputFiles/SwitchGenerator.php');
		$this->assertBeforeAndAfter(
			[new GeneratorVisitor()],
			$code,
			[0, 12, 13]
		);

		$this->assertCodeResult(
			[new GeneratorVisitor()],
			$code,
			[0, 12, 13],
			[1, 'end']
		);
	}

	public function testSwitchGeneratorNoBreak() {
		$this->skipIfVersionLowerThan('5.5.0');
		$code = file_get_contents(__DIR__ . '/InputFiles/SwitchGenerator.php');
		$this->assertBeforeAndAfter(
			[new GeneratorVisitor()],
			$code,
			[1, 12, 13]
		);

		$this->assertCodeResult(
			[new GeneratorVisitor()],
			$code,
			[1, 12, 13],
			[2, 3, 'end']
		);
	}

	public function testSwitchGeneratorNonConst() {
		$this->skipIfVersionLowerThan('5.5.0');
		$code = file_get_contents(__DIR__ . '/InputFiles/SwitchGenerator.php');
		$this->assertBeforeAndAfter(
			[new GeneratorVisitor()],
			$code,
			[12, 12, 13]
		);

		$this->assertCodeResult(
			[new GeneratorVisitor()],
			$code,
			[12, 12, 13],
			[13, 'end']
		);
	}

	public function testSwitchGeneratorDefault() {
		$this->skipIfVersionLowerThan('5.5.0');
		$code = file_get_contents(__DIR__ . '/InputFiles/SwitchGenerator.php');
		$this->assertBeforeAndAfter(
			[new GeneratorVisitor()],
			$code,
			[99, 12, 13]
		);

		$this->assertCodeResult(
			[new GeneratorVisitor()],
			$code,
			[99, 12, 13],
			[-1, 'end']
		);
	}
}
