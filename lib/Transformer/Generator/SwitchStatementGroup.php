<?php

namespace Regen\Transformer\Generator;

use PhpParser\Node;

class SwitchStatementGroup extends StatementGroup {
	/**
	 * @param StateCounter $counter
	 * @return StatementGroupTransformer
	 */
	public function getTransformer(StateCounter $counter) {
		return new SwitchStatementGroupTransformer($counter);
	}
}
