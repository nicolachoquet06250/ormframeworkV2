<?php

namespace ormframework\core\services;

class my_first_service implements \ormframework\core\services\interfaces\service {

	public function __call($name, $arguments) {
		var_dump($name);
	}
}