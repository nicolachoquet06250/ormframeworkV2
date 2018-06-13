<?php

namespace ormframework\custom\annotations;

//require_once '../../core/annotation/interfaces/annotation_interface.php';

class route implements \ormframework\core\annotation\interfaces\annotation_interface
{
    public $comments;

    public function __construct(array $comments)
    {
        $this->comments = $comments;
    }

    public function get($id=0, $model='')
    {
    	if($model === '') {
			$routes = [];
			foreach ($this->comments as $comment) {
				foreach ($comment as $item => $value) {
					if (isset($value['@route'])) {
						$routes[$value['@model']][$value['@route']] = $value['@model'].'/'.$value['@method'].'/@args';
					}
				}
			}
			return $routes;
		}
		else {
			$i = 0;
			foreach ($this->comments['\\'.__CLASS__][$model] as $alias => $route) {
				if ($i === $id) {
					return [$route => $alias];
				}
				$i++;
			}
			return '';
		}
    }

	public function to_html(int $id, $model='') {
    	$routes = $this->get($id, $model);
		foreach ($routes as $route => $alias) {
			return "<div class='col-12'>
					    route => {$route}
						<br />
						equivalent => {$alias}
					</div>";
    	}
		return '';
	}
}