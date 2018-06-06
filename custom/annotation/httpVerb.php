<?php

class httpVerb implements annotation_interface
{

    public $comments;

    public function __construct(array $comments)
    {
        $this->comments = $comments;
    }

    public function get($id=0, $model='')
    {
    	if($model === '') {
			$httpVerb = [];
			foreach ($this->comments as $comment) {
				foreach ($comment as $item => $value) {
					if (isset($value['@httpVerb'])) {
						$httpVerb[$value['@model']][$value['@model'].'/'.$value['@method'].'/@args'] = $value['@httpVerb'];
					} else {
						$httpVerb[$value['@model']][$value['@model'].'/'.$value['@method'].'/@args'] = 'get';
					}
				}
			}
			return $httpVerb;
		}
		else {
			foreach ($this->comments['httpVerb'] as $local_model => $comment) {
				if($local_model === $model) {
					$i = 0;
					foreach ($comment as $route => $verb) {
						if($i === $id) {
							return $verb;
						}
						$i++;
					}
				}
			}
		}
    }

	public function to_html(int $id, $model='') {
    	return "<div class='col-12'>
					Verb HTTP : {$this->get($id, $model)}
				</div>";
	}
}