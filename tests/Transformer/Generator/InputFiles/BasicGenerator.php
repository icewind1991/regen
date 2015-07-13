<?php

return function ($a, $b, $c) {
	$gen = function ($a, $b, $c) {
		if ($a) {
			yield $b;
		}
		yield $c;
	};
	return iterator_to_array($gen($a, $b, $c));
};
