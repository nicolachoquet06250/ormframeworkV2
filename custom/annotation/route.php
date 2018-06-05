<?php

//require_once '../../core/annotation/interfaces/annotation_interface.php';

class route implements annotation_interface
{
    public $comments;

    public function __construct(array $comments)
    {
        $this->comments = $comments;
    }

    public function get()
    {
        $routes = [];
        foreach ($this->comments as $comment) {
            foreach ($comment as $item => $value) {
                if (isset($value['@route'])) {
                    $routes[$value['@route']] = $value['@model'] . '/' . $value['@method'] . '/@args';
                }
            }
        }
        return $routes;
    }

	public function to_html(int $id, $farmework='bootstrap') {
    	$routes = $this->get();
    	$cmp = 0;
		foreach ($routes as $route => $alias) {
			if($cmp === $id) {
				return "<div class='col-12'>
						   route => {$route}
						   <br />
						   equivalent => {$alias}
						</div>";
			}
			$cmp++;
    	}
		return '';
	}
}