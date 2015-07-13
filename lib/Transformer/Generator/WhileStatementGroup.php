<?php

namespace Regen\Transformer\Generator;

use PhpParser\Node;

class WhileStatementGroup extends StatementGroup {
	/**
	 * @param StateCounter $counter
	 * @return StatementGroupTransformer
	 */
	public function getTransformer(StateCounter $counter) {
		return new WhileStatementGroupTransformer($counter);
	}
}
