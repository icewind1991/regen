<?php

namespace Regen\Transformer\Generator;

use PhpParser\Node;

class BreakStatementGroup extends StatementGroup {
	/**
	 * @param StateCounter $counter
	 * @return StatementGroupTransformer
	 */
	public function getTransformer(StateCounter $counter) {
		return new BreakStatementGroupTransformer($counter);
	}
}
