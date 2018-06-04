<?php

class params implements annotation_interface {

	private $comments = [];
	public function __construct(array $comments) {
		$this->comments = $comments;
	}

	public function get() {
		$params = [];
		foreach ($this->comments as $comment) {
			foreach ($comment as $item => $value) {
				if (isset($value['@param'])) {
					$params[] = $value['@param'];
				}
			}
		}
		return $params;
	}
}