<?php

require_once '../services/interfaces/service.php';
require_once '../setup/utils.php';
require_once 'interfaces/annotation_interface.php';

class PhpDocParser extends utils implements service
{
    private $commentaires = [], $parsing = [], $model = '';

    public function __construct($file_or_dir, $directory=false)
    {
    	function parse_file($file) {
			$regex_class_model = '`[\<\?a-zA-Z\t\n]+class\ ([a-zA-Z0-9\_]+)[\.\ a-zA-Z0-9\{\n\t\/\*\@é\\\'\$\_\(\)\ =\;\-\>\}]+`';
			$regex_1           = '`\/\*\*[\n\t\ ]+([\n\t\ \*\@a-zA-Zé\\\'\$\-\?\_\.\/ 0-9]+)[\n\t\ ]+ \*\*\/`';

			$class        = '';
			$commentaires = [];

			$content = file_get_contents($file);

			//récupération du nom de model et controller
			preg_replace_callback($regex_class_model, function ($matches) use (&$class) {
				$class = str_replace('_model', '', $matches[1]);
			}, $content);

			//récupération d'un tableau d'information sur chaque méthode
			preg_replace_callback($regex_1, function ($matches) use (&$commentaires) {
				$commentaires[] = $matches[1];
			}, $content);

			// nétoyage du tableau récupéré
			foreach ($commentaires as $key => $commentaire) {
				$commentaires[$key] = explode("\n\t", $commentaire);

				if (!strstr($commentaires[$key][0], '@description ')) {
					$commentaires[$key][0] = '@description '.$commentaires[$key][0];
				}
				if (count($commentaires[$key]) < 2) {
					$commentaires[$key] = $commentaires[$key][0];
				}
				if(gettype($commentaires[$key]) === 'array') {
					foreach ($commentaires[$key] as $id => $commentaire) {
						$commentaires[$key][$id] = explode("\n", $commentaire);
					}
				}
				else {
					$commentaires[$key] = explode("\n", $commentaires[$key]);
				}


				foreach ($commentaires[$key] as $key2 => $commentaire2) {
					$commentaires[$key][$key2] = str_replace(" * ", '', $commentaire2);
					$commentaires[$key][$key2] = str_replace('* ', '', $commentaire2);

					//enlèvement des espaces en surplus.
					preg_replace_callback('`[\ ]+([\@a-zA-Z0-9\ \$\?\-\_\/\:\.é\\\'àùè]+)`', function ($matches) use (&$commentaires, $key2, $key) {
						$commentaires[$key][$key2] = $matches[1];
					}, $commentaires[$key][$key2]);
					if ($commentaire2 === ' *') {
						unset($commentaires[$key][$key2]);
					}
				}
				$commentaires[$key][] = '@model '.$class;
			}

			foreach ($commentaires as $key => $commentaire) {
				$commentaires[$key][0] = '@description '.ucfirst($commentaire[0]);
			}

			foreach ($commentaires as $key => $commentaire) {
				foreach ($commentaire as $k => $v) {
					$tmp = [];
					preg_replace_callback('`(\@[&-zA-Z\_\-\/\:]+)\ ([^µ]+)`', function ($matches) use (&$tmp) {
						$tmp = [$matches[1], $matches[2]];
					}, $v);
					$commentaires[$key][$k] = $tmp;
				}
			}

			foreach ($commentaires as $key => $commentaire) {
				foreach ($commentaire as $k => $v) {
					if (!empty($v)) {
						$commentaires[$key][$v[0]] = $v[1];
						if ($v[0] === '@param') {
							if (strstr($commentaires[$key]['@param'], ' ')) {
								$var = explode(' ', $commentaires[$key]['@param']);
								if (isset($var[1])) {
									$name  = $var[1];
									$value = $var[0];
									if (gettype($commentaires[$key]['@param']) === 'array') {
										$commentaires[$key]['@param'][$name] = $value;
									} else {
										$commentaires[$key]['@param'] = [$name => $value];
									}
								} else {
									$value = $var[0];
									if (gettype($commentaires[$key]['@param']) === 'array') {
										$commentaires[$key]['@param'][] = $value;
									} else {
										$commentaires[$key]['@param'] = [$value];
									}
								}
							}
						}
					}
					unset($commentaires[$key][$k]);
				}
			}

			return $commentaires;
		}

		if($directory) {
    		$dir = opendir($file_or_dir);
    		while (($file = readdir($dir)) !== false) {
    			if($file !== '.' && $file !== '..') {
    				$this->commentaires[] = parse_file($file_or_dir.'/'.$file);
				}
			}
		}
		else {
			$this->commentaires[] = parse_file($file_or_dir);
		}
    }

	/**
	 * @param $name
	 * @param $arguments
	 * @return PhpDocParser
	 */
    public function __call($name, $arguments)
    {
        if (is_file('../../custom/annotation/' . $name . '.php')) {
            require_once '../../custom/annotation/' . $name . '.php';
            if($name == 'model') {
            	$this->model = (new $name($this->commentaires))->get();
            	$this->parsing[$this->model] = [];
			}
			else {
				$this->parsing[$this->model][$name] = (new $name($this->commentaires))->get();
			}
        }
        return $this;
    }

    public function to_html()
    {

		foreach ($this->parsing as $item => $datas) {
			var_dump($datas);
    	}

		/*$str_html = "<!DOCTYPE html>
<html>
    <head>
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1, shrink-to-fit=no\">
        <meta charset='utf-8'>
        <title>Documentation API Rest</title>
        <link   rel=\"stylesheet\"
                href=\"https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css\" 
                integrity=\"sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm\" 
                crossorigin=\"anonymous\">
    </head>
    <body>";
		$str_html .= "<div class='container'>
        <div class='row' style='height: 15px;'></div>
        <div class='card'>";
//		foreach ($this->commentaires as $model) {
//
//			$str_html .= "<div class='card-header text-center' style='cursor: pointer;'>
//                <h5 class='card-title'>{$model[0]['@model']}</h5>
//              </div>";
//
//			$str_html .= "<div class='card-body'>
//            <div class='row'>
//            <div class='col-12'>";
//			foreach ($model as $key => $method) {
//				if(count($method) > 1) {
//					if($key > 0) {
//						$str_html .= "
//                      <div class='row'>
//                        <div class='col-12'>
//                            <hr>
//                        </div>
//                      </div>";
//					}
//					$type_retour = explode('_', $method['@return'])[0];
//					$str_html .= "
//                    <div class='row'>
//                        <div class='col-12' style='border: 1px solid lightgray; -webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px;'>
//                            <div class='card-header text-center' style='background: white;'>
//                                <h5 class='card-title'>{$method['@method']}</h5>
//                            </div>
//                            <div class='card-body'>
//                                <div class='row'>
//                                    <div class='col-12'>
//                                        <p class='card-text'>
//                                            {$method['@description']}
//                                        </p>
//                                    </div>
//                                    <div class='col-12'>
//                                        <div class='list-group'>
//                                            <b>params => </b><br />
//                                            <div class='list-group-item'>";
//					if (gettype($method['@param']) === 'string') {
//						$str_html .= "
//                                                variable : {$method['@param']}
//                                                <br />
//                                                type : mixed
//                        ";
//					}
//					else {
//						foreach ($method['@param'] as $name => $type) {
//							$str_html .= "
//                                                variable : {$name}
//                                                <br />
//                                                type : {$type}
//                        ";
//						}
//					}
//					$str_html .= "
//                                            </div>
//                                        </div>
//                                    </div>
//                                    <div class='col-12'>
//                                        type de retour : {$type_retour}
//                                    </div>
//                                    <div class='col-12'>
//                                        route => {$method['@route']}
//                                        <br />
//                                        equivalent => {$method['@model']}/{$method['@method']}/@args
//                                    </div>
//                                 </div>
//                              </div>
//                          </div>
//                      </div>";
//				}
//			}
//			$str_html .= "</div>
//                      </div>
//                  </div>";
//		}
		$str_html .= "</div>
                </div>
                <script src=\"https://code.jquery.com/jquery-3.2.1.slim.min.js\" 
                        integrity=\"sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN\" 
                        crossorigin=\"anonymous\"></script>
                <script src=\"https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js\" 
                        integrity=\"sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q\" 
                        crossorigin=\"anonymous\"></script>
                <script src=\"https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js\" 
                        integrity=\"sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl\" 
                        crossorigin=\"anonymous\"></script>";
//		if(DEBUG)
//			$str_html .= "<pre>".self::var_dump($this->commentaires)."</pre>";
		$str_html .= "</body>
                </html>";
//		if(!is_dir('./custom/website/doc/')) {
//			mkdir('./custom/website/doc/', 0777, true);
//		}
//		file_put_contents('./custom/website/doc/index.html', $str_html);
		//var_dump($this->commentaires);
    	return $str_html;*/
    }

    public function reset()
    {
        $this->commentaires = [];
    }
}


var_dump(
	(new PhpDocParser('../../custom/mvc/models', true))
		->model()
		->method()
		->route()
		->httpVerb()
		->description()
		->params()
		->return_annotation()
		->to_html()
);