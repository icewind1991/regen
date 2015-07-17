<?php

return function ($a, $b, $c) {
	$gen = function ($a, $b, $c) {
		switch ($a) {
			case 0:
				yield 1;
				break;
			case 1:
				yield 2;
			case 2:
				yield 3;
				break;
			case $b:
				yield $c;
				break;
			default:
				yield -1;
		}
		yield 'end';
	};
	return iterator_to_array($gen($a, $b, $c));
};
