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

	public function to_html(int $id, $farmework='bootstrap') {
		return "<div class='card-header text-center' style='background: white;'>
                    <h5 class='card-title'>{$this->get()[$id]}</h5>
                </div>";
	}
}