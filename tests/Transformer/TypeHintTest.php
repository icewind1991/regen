<?php

namespace Regen\Tests\Transformer;

use Regen\Tests\Polyfill\OperatorTest;
use Regen\Transformer\ArgumentUnpackingVisitor;
use Regen\Transformer\Operators;
use Regen\Transformer\ReturnTypeVisitor;
use Regen\Transformer\TypeHint;
use Regen\Transformer\VariadicFunctionVisitor;

class TypeHintTest extends VisitorTest {
	public function testReturnTypeCompare() {
		$this->skipIfVersionLowerThan('7.0.0');
		$code = file_get_contents(__DIR__ . '/InputFiles/ReturnType.php');
		$this->assertBeforeAndAfter(
			[$this->visitorFromTransformer(new TypeHint())],
			$code,
			['a']
		);

		try {
			$this->assertBeforeAndAfter(
				[$this->visitorFromTransformer(new TypeHint())],
				$code,
				[1]
			);
			$this->fail('Expected return type error');
		} catch (\TypeError $e) {
			$this->assertTrue(true);
		}
	}

	public function testReturnType() {
		$code = file_get_contents(__DIR__ . '/InputFiles/ReturnType.php');
		$this->assertCodeResult(
			[$this->visitorFromTransformer(new TypeHint())],
			$code,
			['a'],
			'a'
		);

		try {
			$this->assertCodeResult(
				[$this->visitorFromTransformer(new TypeHint())],
				$code,
				[1],
				null
			);
			$this->fail('Expected return type error');
		} catch (\TypeError $e) {
			$this->assertTrue(true);
		}
	}

	public function testReturnTypeNoReturn() {
		$code = file_get_contents(__DIR__ . '/InputFiles/ReturnTypeNoReturn.php');
		$this->assertCodeResult(
			[$this->visitorFromTransformer(new TypeHint())],
			$code,
			['aaa'],
			'aaa'
		);

		try {
			$this->assertCodeResult(
				[$this->visitorFromTransformer(new TypeHint())],
				$code,
				['a'],
				null
			);
			$this->fail('Expected return type error');
		} catch (\TypeError $e) {
			$this->assertTrue(true);
		}
	}
}
