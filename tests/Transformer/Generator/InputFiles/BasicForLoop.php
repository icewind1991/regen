<?php

return function ($from, $to, $step) {
	$result = [];
	for ($i = $from; $i <= $to; $i += $step) {
		$result[] = $i;
	}
	return $result;
};
