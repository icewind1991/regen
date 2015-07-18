<?php

namespace Regen\Transformer\Generator;

use PhpParser\Node;

class ContinueStatementGroup extends StatementGroup {
	/**
	 * @param StateCounter $counter
	 * @return StatementGroupTransformer
	 */
	public function getTransformer(StateCounter $counter) {
		return new ContinueStatementGroupTransformer($counter);
	}
}
