<?php

class params implements annotation_interface {

	private $comments = [];
	public function __construct(array $comments) {
		$this->comments = $comments;
	}

	public function get() {
		$params = [];
		foreach ($this->comments as $comment) {
			foreach ($comment as $item => $value) {
				if (isset($value['@param'])) {
					$params[] = $value['@param'];
				}
			}
		}
		return $params;
	}

	public function to_html(int $id, $farmework='bootstrap') {
		$params = $this->get();
		$str = "<div class='col-12'><div class='list-group'>
                   <b>params => </b>
                   <div class='list-group-item'>";
		if (gettype($params[$id]) === 'string') {
			$str .= "
                        variable : {$params}
                        <br />
                        type : mixed
            ";
		}
		else {
			foreach ($params[$id] as $name => $type) {
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