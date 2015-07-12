<?php

namespace Regen\Tests\Transformer\Generator;

use Regen\Tests\Transformer\CompatibleVisitorTest;
use Regen\Transformer\Generator\ForVisitor;

class ForTest extends CompatibleVisitorTest {
	public function testBasicLoop() {
		$code = file_get_contents(__DIR__ . '/BasicForLoop.php');
		$this->assertBeforeAndAfter(
			[new ForVisitor()],
			$code,
			[0, 10, 2]
		);
	}
}
