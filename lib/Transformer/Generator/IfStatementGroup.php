<?php

namespace Regen\Transformer\Generator;

use PhpParser\Node;

class IfStatementGroup extends StatementGroup {
	/**
	 * @param StateCounter $counter
	 * @return StatementGroupTransformer
	 */
	public function getTransformer(StateCounter $counter) {
		return new IfStatementGroupTransformer($counter);
	}
}
