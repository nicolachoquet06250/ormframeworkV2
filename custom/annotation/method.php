<?php

class method implements annotation_interface {

	private $comments = [];
	public function __construct(array $comments) {
		$this->comments = $comments;
	}

	public function get() {
		$methods = [];
		foreach ($this->comments as $comment) {
			foreach ($comment as $item => $value) {
				if (isset($value['@method'])) {
					$methods[] = $value['@method'];
				}
			}
		}
		return $methods;
	}
}