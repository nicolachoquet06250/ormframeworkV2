<?php

namespace ormframework\custom\annotations;

class params implements \ormframework\core\annotation\interfaces\annotation_interface {

	private $comments = [];
	public function __construct(array $comments) {
		$this->comments = $comments;
	}

	public function get($id=0, $model='') {
		if($model==='') {
			$params = [];
			foreach ($this->comments as $comment) {
				foreach ($comment as $item => $value) {
					if (isset($value['@param'])) {
						$params[$value['@model']][] = $value['@param'];
					}
				}
			}
			return $params;
		}
		return $this->comments['\ormframework\custom\annotations\params'][$model][$id];
	}

	public function to_html(int $id, $model='') {
		$params = $this->get($id, $model);
		$str = "<div class='col-12'><div class='list-group'>
                   <b>params => </b>
                   <div class='list-group-item'>";
		if (gettype($params) === 'string') {
			if(substr($params, 0, 1) === '$') {
				$str .= "
                        variable : {$params}
                        <br />
                        type : mixed
            ";
			}
			else {
				$str .= "
                        type : {$params}
            ";
			}
		}
		else {
			foreach ($params as $name => $type) {
				$str .= "
                         variable : {$name}
                         <br />
                         type : {$type}
                ";

			}
		}
		$str .= "
             </div>
         </div></div>";
		return $str;
	}
}