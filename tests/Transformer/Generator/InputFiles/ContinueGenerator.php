<?php

return function ($start, $end) {
	$gen = function ($start, $end) {
		for ($i = $start; $i < $end; $i++) {
			if ($i % 2 == 0) {
				continue;
			}
			yield $i;
		}
	};
	return iterator_to_array($gen($start, $end));
};
