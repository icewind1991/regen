<?php

namespace Regen\Tests\Transformer\Generator;

use Regen\Tests\TestCase;
use Regen\Transformer\Generator\StatementGroup;

class StatementGroupTest extends TestCase {
	public function testFindNextSiblingExists() {
		$sibling = new StatementGroup([], 0, null, null);
		$group = new StatementGroup([], 0, $sibling, null);
		$this->assertEquals($sibling, $group->findNextSibling());
	}

	public function testFindNextSiblingParent() {
		$sibling = new StatementGroup([], 0, null, null);
		$parent = new StatementGroup([], 0, $sibling, null);
		$group = new StatementGroup([], 0, null, $parent);
		$this->assertEquals($sibling, $group->findNextSibling());
	}

	public function testFindNextSiblingParentAndSibling() {
		$sibling = new StatementGroup([], 0, null, null);
		$parentSibling = new StatementGroup([], 0, null, null);
		$parent = new StatementGroup([], 0, $parentSibling, null);
		$group = new StatementGroup([], 0, $sibling, $parent);
		$this->assertEquals($sibling, $group->findNextSibling());
	}

	public function testFindNextSiblingNestedParent() {
		$sibling = new StatementGroup([], 0, null, null);
		$parent1 = new StatementGroup([], 0, $sibling, null);
		$parent2 = new StatementGroup([], 0, null, $parent1);
		$group = new StatementGroup([], 0, null, $parent2);
		$this->assertEquals($sibling, $group->findNextSibling());
	}

	public function testFindNextSiblingNone() {
		$group = new StatementGroup([], 0, null, null);
		$this->assertEquals(null, $group->findNextSibling());
	}
}
