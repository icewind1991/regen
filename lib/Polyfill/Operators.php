<?php

namespace Regen\Polyfill;

class Operators {
	/**
	 * @param mixed $a
	 * @param mixed $b
	 * @return int
	 */
	public static function spaceship($a, $b) {
		if (is_string($a) && is_string($b)) {
			return strcmp($a, $b);
		} elseif (is_array($a) && is_array($b)) {
			$a = array_values($a);
			$b = array_values($b);
			for ($i = 0; $i < min(count($a), count($b)); $i++) {
				$result = self::spaceship($a[$i], $b[$i]);
				if ($result !== 0) {
					return $result;
				}
			}
			return self::spaceship(count($a), count($b));
		} else {
			if ($a < $b) {
				return -1;
			} elseif ($a > $b) {
				return 1;
			} else {
				return 0;
			}
		}
	}
}
