<?php

// polyfill for php7 TypeError
if (!class_exists('\TypeError')) {
	class TypeError extends \Exception {

	}
}
