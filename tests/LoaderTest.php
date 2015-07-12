<?php

namespace Regen\Tests;

use Regen\Loader;
use Regen\Regen;
use Regen\Tests\SourceClasses\BasicClass;
use Regen\Tests\SourceClasses\TypeHintClass;
use Regen\Transformer\Operators;
use Regen\Transformer\TypeHint;

class LoaderTest extends TestCase {
	private function getLoader() {
		$regen = new Regen(Loader::getTarget());
		$composer = require __DIR__ . '/../vendor/autoload.php';
		$loader = new Loader($regen, $composer);
		$loader->addPrefix('\\Regen\\Tests\\SourceClasses');
		return $loader;
	}

	public function testLoadBasic() {
		$loader = $this->getLoader();
		$this->assertTrue($loader->loadClass('\\Regen\\Tests\\SourceClasses\\BasicClass'));
		$instance = new BasicClass();
		$this->assertEquals('hello world', $instance->test('world'));
	}

	public function testLoadTypeHintClass() {
		$loader = $this->getLoader();
		$loader->loadClass('\\Regen\\Tests\\SourceClasses\\TypeHintClass');
		$instance = new TypeHintClass();
		$this->assertEquals('hello world', $instance->test('world'));
	}

	public function testLoadTypeHintClassInvalidReturn() {
		$loader = $this->getLoader();
		$loader->loadClass('\\Regen\\Tests\\SourceClasses\\TypeHintClass');
		$instance = new TypeHintClass();
		try {
			$this->assertEquals('hello world', $instance->testInvalidReturn('world'));
			$this->fail('Expected TypeError');
		} catch (\TypeError $e) {
			$this->assertTrue(true);
		}
	}

	public function testLoadTypeHintClassInvalidArgument() {
		$loader = $this->getLoader();
		$loader->loadClass('\\Regen\\Tests\\SourceClasses\\TypeHintClass');
		$instance = new TypeHintClass();
		try {
			$this->assertEquals('hello world', $instance->test(1));
			$this->fail('Expected TypeError');
		} catch (\TypeError $e) {
			$this->assertTrue(true);
		}
	}
}
