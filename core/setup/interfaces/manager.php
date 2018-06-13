<?php

namespace ormframework\core\setup\interfaces;

interface manager {

	public static function instence();

	public function __call($name, $arguments);
}