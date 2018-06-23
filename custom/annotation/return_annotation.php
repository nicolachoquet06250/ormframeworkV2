<?php

namespace ormframework\custom\annotations;

class return_annotation implements \ormframework\core\annotation\interfaces\annotation_interface {

	private $comments = [];
	public function __construct(array $comments) {
		$this->comments = $comments;
	}

	public function get($id=0, $model='') {
		if($model === '') {
			$returns = [];
			foreach ($this->comments as $comment) {
				foreach ($comment as $item => $value) {
					if (isset($value['@return'])) {
						$returns[$value['@model']][] = $value['@return'];
					}
				}
			}
			return $returns;
		}
		return $this->comments['\\'.__CLASS__][$model][$id];
	}

	public function to_html(int $id, $model='') {
		$return = $this->get($id, $model);
		return "<div class='col-12'>
                    type de retour : {$return}
                </div>";
	}
}