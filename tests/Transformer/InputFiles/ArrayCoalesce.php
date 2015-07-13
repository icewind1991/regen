<?php

return function ($a, $b, $c) {
	return $a[$b] ?? $c;
};
