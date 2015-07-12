<?php

namespace Regen\Tests;

use PhpParser\Lexer\Emulative;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard;
use Temping\Temping;

abstract class TestCase extends \PHPUnit_Framework_TestCase {
	/**
	 * @var Temping
	 */
	protected $temp;

	/**
	 * @var Parser
	 */
	protected $parser;

	/**
	 * @var Standard
	 */
	protected $printer;

	public function setUp() {
		parent::setUp();
		$this->temp = new Temping();
		$this->parser = new Parser(new Emulative());
		$this->printer = new Standard();
	}

	protected function assertCodeEquals($expected, $result, $message) {
		$expected = $this->reformatCode($expected);
		$result = $this->reformatCode($result);
		$this->assertEquals($expected, $result, $message);
	}

	protected function assertNotCodeEquals($expected, $result, $message) {
		$expected = $this->reformatCode($expected);
		$result = $this->reformatCode($result);
		$this->assertNotEquals($expected, $result, $message);
	}

	protected function reformatCode($code) {
		$stmts = $this->parser->parse($code);
		return $this->printer->prettyPrintFile($stmts);
	}

	protected function loadCode($code) {
		$id = uniqid() . '.php';
		$this->temp->create($id, $code);
		return require $this->temp->getPathname($id);
	}

	protected function skipIfVersionLowerThan($version) {
		if (version_compare(PHP_VERSION, $version) < 0) {
			$this->markTestSkipped('PHP version not high enough to run test, php ' . $version . ' required');
		}
	}
}
