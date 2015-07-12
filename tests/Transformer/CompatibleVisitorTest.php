<?php

namespace Regen\Tests\Transformer;

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use Regen\Tests\TestCase;
use Regen\Transformer\TransformerInterface;
use Regen\Transformer\Visitor;

/**
 * Test cases for transformations for code where both the source and result code work in the current php version
 */
abstract class CompatibleVisitorTest extends TestCase {

	/**
	 * @param NodeVisitor[] $visitors
	 * @return NodeTraverser
	 */
	protected function getTraverser(array $visitors) {
		$traverser = new NodeTraverser();
		foreach ($visitors as $visitor) {
			$traverser->addVisitor($visitor);
		}
		return $traverser;
	}

	protected function visitorFromTransformer(TransformerInterface $transformer) {
		$visitor = new Visitor();
		$visitor->addTransformer($transformer);
		return $visitor;
	}

	protected function assertBeforeAndAfter(array $visitors, $code, $arguments = []) {
		$newCode = $this->transformCode($code, $visitors);
		$this->assertNotCodeEquals($code, $newCode, 'Failed asserting that visitor transformed code');
		$before = $this->getResultFromCode($code, $arguments);
		$after = $this->getResultFromCode($newCode, $arguments);
		$this->assertEquals($before, $after);
	}

	protected function transformCode($code, array $visitors) {
		$stmts = $this->parser->parse($code);
		$traverser = $this->getTraverser($visitors);
		$stmts = $traverser->traverse($stmts);
		return $this->printer->prettyPrintFile($stmts);
	}

	/**
	 * @param string $code
	 * @param array $arguments
	 * @return mixed
	 */
	protected function getResultFromCode($code, $arguments) {
		$function = $this->loadCode($code);
		return call_user_func_array($function, $arguments);
	}
}
