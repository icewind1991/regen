<?php

return function ($start, $end) {
	$gen = function ($start, $end) {
		$i = $start;
		do {
			yield $i;
			$i++;
		} while ($i < $end);
	};
	return iterator_to_array($gen($start, $end));
};
