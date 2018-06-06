<?php

class model_annotation implements annotation_interface {

	private $comments = [];
	public function __construct(array $comments) {
		$this->comments = $comments;
	}

	public function get($id=0) {
		$i=0;
		foreach ($this->comments['method'] as $model => $comment) {
			if($i === $id) {
				return $model;
			}
			$i++;
		}
		return '';
	}

	public function to_html(int $id, &$farmework=null) {
		if($farmework)
			$farmework->model = $this->get($id);

		return "<div class='card-header text-center' style='cursor: pointer;'>
                <h5 class='card-title'>{$this->get($id)}</h5>
              </div>";
	}
}