<?php

class return_annotation implements annotation_interface {

	private $comments = [];
	public function __construct(array $comments) {
		$this->comments = $comments;
	}

	public function get() {
		$returns = [];
		foreach ($this->comments as $comment) {
			foreach ($comment as $item => $value) {
				if (isset($value['@return'])) {
					$returns[] = $value['@return'];
				}
			}
		}
		return $returns;
	}

	public function to_html(int $id, $farmework='bootstrap') {
		$returns = $this->get();
		$return = str_replace('_view', '', $returns[$id]);
		return "<div class='col-12'>
                    type de retour : {$return}
                </div>";
	}
}