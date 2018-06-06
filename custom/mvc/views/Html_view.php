<?php

class Html_view extends view {
	public function content_type() {
		return 'text/html';
	}

	public function display()
	{
		return $this->data;
	}
}