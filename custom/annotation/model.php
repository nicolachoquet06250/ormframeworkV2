<?php

class model implements annotation_interface {

	private $comments = [];
	public function __construct(array $comments) {
		$this->comments = $comments;
	}

	public function get() {
		foreach ($this->comments as $comment) {
			foreach ($comment as $item => $value) {
				if (isset($value['@model'])) {
					return $value['@model'];
				}
			}
		}
		return '';
	}
}