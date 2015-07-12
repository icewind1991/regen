<?php

class Foo {
	public function hello(string $subject): string {
		if ($subject === 'world') {
			return 'Hello World';
		}
		return 'hello ' . $subject;
	}

	public function compare($a, $b) {
		return $a <=> $b;
	}

	public function getInArray($array, $key1, $key2, $default) {
		return $array[$key1] ?? $array[$key2] ?? $default;
	}

	public function getInner($foo){
		return new class($foo) extends Foo {
			public function asd(){
				return 'asd';
			}
		};
	}
}

class Bar extends Foo {

}

$a = new Foo();
