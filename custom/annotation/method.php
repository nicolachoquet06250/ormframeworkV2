<?php

namespace ormframework\custom\annotations;

class method implements \ormframework\core\annotation\interfaces\annotation_interface {

	private $comments = [];
	public function __construct(array $comments) {
		$this->comments = $comments;
	}

	public function get($id=0, $model='') {
		if($model === '') {
			$methods = [];
			foreach ($this->comments as $comment) {
				foreach ($comment as $item => $value) {
					if (isset($value['@method'])) {
						$methods[$value['@model']][] = $value['@method'];
					}
				}
			}
			return $methods;
		}
		return $this->comments['\ormframework\custom\annotations\method'][$model][$id];
	}

	public function to_html(int $id, $model='') {
		return "<div class='card-header text-center' style='background: white;'>
                    <h5 class='card-title'>{$this->get($id, $model)}</h5>
                </div>";
	}
}