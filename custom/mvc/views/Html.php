<?php

namespace ormframework\custom\mvc\views;

class Html extends \ormframework\core\mvc\view {
	public function content_type()
	{
		return 'text/html';
	}

	public function display()
	{
		return $this->data;
	}
}