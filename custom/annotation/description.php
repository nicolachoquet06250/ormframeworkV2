<?php

class description implements annotation_interface {

	private $comments = [];
	public function __construct(array $comments) {
		$this->comments = $comments;
	}

	public function get() {
		$descriptions = [];
		foreach ($this->comments as $comment) {
			foreach ($comment as $item => $value) {
				if (isset($value['@description'])) {
					$descriptions[] = $value['@description'];
				}
			}
		}
		return $descriptions;
	}
}