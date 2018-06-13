<?php

namespace ormframework\core\services\interfaces;

interface service {
	public function __call($name, $arguments);
}