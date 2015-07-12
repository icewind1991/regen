<?php

namespace Regen\Tests\Transformer\Generator;

use PhpParser\Node\Stmt\Return_;
use Regen\Tests\TestCase;
use Regen\Transformer\Generator\AssignmentFinder;

class AssignmentFinderTest extends TestCase {
	/**
	 * @var AssignmentFinder
	 */
	private $finder;

	public function setUp() {
		parent::setUp();
		$this->finder = new AssignmentFinder();
	}

	public function testSingleAssignment() {
		$code = '<?php function test(){$i=0;}';
		list($function) = $this->parser->parse($code);
		$this->assertEquals(['i'], $this->finder->getNames($function));
	}

	public function testOverwrite() {
		$code = '<?php function test(){$i=0;$i=1;$i=2;}';
		list($function) = $this->parser->parse($code);
		$this->assertEquals(['i'], $this->finder->getNames($function));
	}

	public function testMultipleAssignment() {
		$code = '<?php function test(){$i=0;$a=1;}';
		list($function) = $this->parser->parse($code);
		$this->assertEquals(['i', 'a'], $this->finder->getNames($function));
	}

	public function testConditionalAssignment() {
		$code = '<?php function test(){if(false){$i=0;}}';
		list($function) = $this->parser->parse($code);
		$this->assertEquals(['i'], $this->finder->getNames($function));
	}

	/**
	 * Test that variables declared in nested callbacks are not returned
	 */
	public function testNestedFunctions() {
		$code = '<?php function test(){$i=0;$cb=function(){$a=1;};}';
		list($function) = $this->parser->parse($code);
		$this->assertEquals(['i', 'cb'], $this->finder->getNames($function));
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testOnNonFunction() {
		$this->finder->getNames(new Return_());
	}
}
