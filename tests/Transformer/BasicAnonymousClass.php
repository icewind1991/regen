<?php

return function ($var) {
	$instance = new class($var) {
		private $var;
		public function __construct($var){
			$this->var=$var;
		}
		public function get(){
			return $this->var;
		}
	};
	return $instance->get();
};
