<?php

class httpVerb implements annotation_interface
{

    public $comments;

    public function __construct(array $comments)
    {
        $this->comments = $comments;
    }

    public function get()
    {
        $httpVerb = [];
        foreach ($this->comments as $comment) {
            foreach ($comment as $item => $value) {
                if (isset($value['@httpVerb'])) {
                	$httpVerb[$value['@model'] . '/' . $value['@method'] . '/@args'] = $value['@httpVerb'];
                }
                else {
					$httpVerb[$value['@model'] . '/' . $value['@method'] . '/@args'] = 'get';
				}
            }
        }
        return $httpVerb;
    }

	public function to_html(int $id, $farmework='bootstrap') {
    	$httpVerbs = $this->get();
		$cmp = 0;
    	foreach ($httpVerbs as $route => $httpVerb) {
    		$httpVerb = strtoupper($httpVerb);
			if($cmp === $id) {
				return "<div class='col-12'>
							Verb HTTP : {$httpVerb}
						</div>";
			}
			$cmp++;
    	}
		return '';
	}
}