<?php

class my_first_service implements service {

	public function __call($name, $arguments) {
		var_dump($name);
	}
}