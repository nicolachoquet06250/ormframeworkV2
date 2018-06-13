<?php

namespace ormframework\custom\annotations;

class description implements \ormframework\core\annotation\interfaces\annotation_interface {

	private $comments = [];
	public function __construct(array $comments) {
		$this->comments = $comments;
	}

	public function get($id=0, $model='') {
		if($model === '') {
			$descriptions = [];
			foreach ($this->comments as $comment) {
				foreach ($comment as $item => $value) {
					if (isset($value['@description'])) {
						$descriptions[$value['@model']][] = $value['@description'];
					}
				}
			}
			return $descriptions;
		}
		return $this->comments['\\'.__CLASS__][$model][$id];
	}

	public function to_html(int $id, $model='') {
		return $this->get($id, $model) !== '?' ? "<div class='col-12'>
					<p class='card-text'>
						<i>
							{$this->get($id, $model)}
						</i>
					</p>
				</div>" : '';
	}
}