<?php

return function ($start, $limit, $step) {
	$gen = function ($start, $limit, $step = 1) {
		if ($start < $limit) {
			for ($i = $start; $i <= $limit; $i += $step) {
				yield $i;
			}
		} else {
			for ($i = $start; $i >= $limit; $i += $step) {
				yield $i;
			}
		}
	};
	return iterator_to_array($gen($start, $limit, $step));
};
