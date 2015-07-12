<?php

namespace Regen\Tests\SourceClasses;

/**
 * Class with no php7 features
 */
class AnonymousClass {
	public function getHelloer($string) {
		return new class($string) {
			private $string;
			public function __construct($string) {
				$this->string = $string;
			}
			public function get(){
				return 'hello ' . $this->string;
			}
		};
	}
}
