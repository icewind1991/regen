<?php

return function ($a, $b) {
	$gen = function ($a, $b) {
		$i = 0;
		while ($i < $a) {
			if ($i >= $b) {
				break;
			}
			$i++;
			yield $i;
		}
		yield 99;
	};
	return iterator_to_array($gen($a, $b));
};
