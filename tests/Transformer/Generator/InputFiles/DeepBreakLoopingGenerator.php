<?php

return function ($a, $b) {
	$gen = function ($a, $b) {
		$i = 0;
		while ($i < 100) {
			$j = 0;
			while ($j < $b) {
				$j++;
				if ($i < $a) {
					$i++;
				} else {
					break 2;
				}
			}
			yield $i;
		}
		yield 99;
	};
	return iterator_to_array($gen($a, $b));
};
