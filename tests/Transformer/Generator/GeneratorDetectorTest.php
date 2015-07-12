<?php

namespace Regen\Tests\Transformer\Generator;

use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\Yield_;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Return_;
use Regen\Tests\TestCase;
use Regen\Transformer\Generator\GeneratorDetector;

class GeneratorDetectorTest extends TestCase {
	/**
	 * @var GeneratorDetector
	 */
	private $detector;

	public function setUp() {
		parent::setUp();
		$this->detector = new GeneratorDetector();
	}

	public function testOnGenerator() {
		$generator = new Function_('foo', [
			'stmts' => [
				new Yield_()
			]
		]);
		$this->assertTrue($this->detector->isGenerator($generator));
	}

	public function testOnNormalFunction() {
		$generator = new Function_('foo', [
			'stmts' => [
				new Return_()
			]
		]);
		$this->assertFalse($this->detector->isGenerator($generator));
	}

	public function testOnNormalClosure() {
		$generator = new Closure([
			'stmts' => [
				new Return_()
			]
		]);
		$this->assertFalse($this->detector->isGenerator($generator));
	}

	public function testOnGeneratorClosure() {
		$generator = new Closure([
			'stmts' => [
				new Yield_()
			]
		]);
		$this->assertTrue($this->detector->isGenerator($generator));
	}

	public function testOnNestedGenerator() {
		$generator = new Closure([
			'stmts' => [
				new Return_(new Closure([
					'stmts' => [
						new Yield_()
					]
				]))
			]
		]);
		$this->assertFalse($this->detector->isGenerator($generator));
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testOnNonFunction() {
		$this->assertFalse($this->detector->isGenerator(new Return_()));
	}
}
