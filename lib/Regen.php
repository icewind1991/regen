<?php

namespace Regen;

use PhpParser\Lexer\Emulative;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard;
use Regen\Transformer\AnonymousClasses;
use Regen\Transformer\ArgumentUnpackingVisitor;
use Regen\Transformer\Generator\GeneratorVisitor;
use Regen\Transformer\Operators;
use Regen\Transformer\TransformerInterface;
use Regen\Transformer\TypeHint;
use Regen\Transformer\VariadicFunctionVisitor;
use Regen\Transformer\Visitor;

class Regen {
	const TARGET_70 = 70;
	const TARGET_56 = 56;
	const TARGET_55 = 55;
	const TARGET_54 = 54;

	/**
	 * @var \PhpParser\Parser
	 */
	private $parser;

	/**
	 * @var \PhpParser\PrettyPrinter\Standard
	 */
	private $printer;

	/**
	 * @var Visitor
	 */
	private $visitor;

	/**
	 * @var NodeTraverser
	 */
	private $traverser;

	/**
	 * @var int any of the self::TARGET_* constants
	 */
	private $target;

	/**
	 * @param int $target any of the self::TARGET_* constants
	 */
	public function  __construct($target) {
		$this->target = $target;
		$this->parser = new Parser(new Emulative);
		$this->printer = new Standard();
		$this->traverser = new NodeTraverser(true);
		$this->visitor = new Visitor();
		$this->traverser->addVisitor($this->visitor);
		$this->addTransformers($target);
	}

	/**
	 * @param int $target
	 */
	private function addTransformers($target) {
		if ($target < self::TARGET_70) {
			$this->addTransformer(new TypeHint());
			$this->addTransformer(new Operators($target));
			$this->addTransformer(new AnonymousClasses());
		}
		if ($target < self::TARGET_56) {
			$this->traverser->addVisitor(new VariadicFunctionVisitor());
			$this->traverser->addVisitor(new ArgumentUnpackingVisitor());
		}
		if ($target < self::TARGET_55) {
			$this->traverser->addVisitor(new GeneratorVisitor());
		}
	}

	public function addTransformer(TransformerInterface $transformer) {
		$this->visitor->addTransformer($transformer);
	}

	public function regen($code) {
		$nodes = $this->parser->parse($code);
		if (is_null($nodes)) {
			throw new \Exception('Failed parsing code');
		}
		$nodes = $this->traverser->traverse($nodes);
		$extraNodes = array_reduce($this->visitor->getTransformers(),
			function ($nodes, TransformerInterface $transformer) {
				return array_merge($nodes, $transformer->getExtraNodes());
			}, []);
		$nodes = array_merge($nodes, $extraNodes);
		return $this->printer->prettyPrintFile($nodes);
	}
}
