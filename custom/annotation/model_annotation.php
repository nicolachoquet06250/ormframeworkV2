<?php

class model_annotation implements annotation_interface {

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

	public function to_html(int $id, $farmework='bootstrap') {
		return "<div class='card-header text-center' style='cursor: pointer;'>
                <h5 class='card-title'>{$this->get()}</h5>
              </div>";
	}
}