<?php

return function ($a, $b, $c) {
	return array_merge($a, $b, ...$c);
};
