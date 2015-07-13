<?php

return function ($from, $to, $step) {
	$result = [];
	$i = $from;
	for (; ; $i += $step) {
		if ($i > $to) {
			return $result;
		}
		$result[] = $i;
	}
	return $result;
};
