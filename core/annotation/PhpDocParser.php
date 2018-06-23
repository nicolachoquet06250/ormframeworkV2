<?php

namespace ormframework\core\annotation;

use ormframework\core\setup\utils;

/**
 * Class PhpDocParser
 *
 * @method PhpDocParser|string method($type, $id)
 * @method PhpDocParser|string model($type, $id)
 */

class PhpDocParser extends utils
{
	private static $instence = null;

    private $commentaires = [], $html = '<!DOCTYPE html>';
    public $parsing = [];
    public $model = '';

	/**
	 * PhpDocParser constructor.
	 *
	 * @param string $file_or_dir
	 * @param bool 	 $directory
	 */
    public function __construct($file_or_dir, $directory=false)
    {
		if($directory) {
    		$dir = opendir($file_or_dir);
    		while (($file = readdir($dir)) !== false) {
    			if($file !== '.' && $file !== '..') {
    			    $this->commentaires[] = $this->parse_file($file_or_dir.'/'.$file);
				}
			}
		}
		else {
			$this->commentaires[] = $this->parse_file($file_or_dir);
		}
    }

	/**
	 * @param  string $file
	 * @return array
	 */
    private function parse_file($file) {
		$regex_class_model = '`[\<\?a-zA-Z\t\n]+class\ ([a-zA-Z0-9\_]+)[\.\ a-zA-Z0-9\{\n\t\/\*\@é\\\'\$\_\(\)\ =\;\-\>\}]+`';
		$regex_1           = '`\/\*\*[\n\t\ ]+([\n\t\ \*\@a-zA-Zé\\\'\$\-\?\_\.\/ 0-9]+)[\n\t\ ]+ \*\*\/`';

		$class        = '';
		$commentaires = [];

		$content = file_get_contents($file);

		//récupération du nom de model et controller
		preg_replace_callback($regex_class_model, function ($matches) use (&$class) {
			$class = $matches[1];
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

	/**
	 * @param null|string $file_or_dir
	 * @param bool|string $directory
	 * @return PhpDocParser
	 */
    public static function instence($file_or_dir = null, $directory = false) {
    	if(self::$instence === null) {
    		self::$instence = new PhpDocParser($file_or_dir, $directory);
		}
		return self::$instence;
	}

	/**
	 * @param $name
	 * @param $arguments
	 * @return PhpDocParser|string
	 */
    public function __call($name, $arguments)
    {
    	if(is_file('custom/annotation/' . $name . '_annotation.php')) {
    		$path = 'custom/annotation/' . $name . '_annotation.php';
    		$class = $name.'_annotation';
		}
        elseif (is_file('custom/annotation/' . $name . '.php')) {
            $path = 'custom/annotation/' . $name . '.php';
            $class = $name;
        }
        else {
    		$path = null;
    		$class = null;
		}
		if($path) {
    		if(empty($arguments)) {
    			$arguments[0] = null;
			}
    		$method = $arguments[0];
    		if(!$method) {
    			$method = 'get';
			}
			else {
    			if(!isset($arguments[1])) {
    				$arguments[1] = 0;
				}
    			$id = $arguments[1];
			}
			require_once $path;

			if($class) {
			    $class = "\ormframework\custom\annotations\\$class";
            }

    		if(strstr($name, 'model')) {
				if(strstr($method, 'html')) {
					$html = (new $class($this->parsing))->$method($id, $this);
					$this->html .= $html;
					return $html;
				}
				else {
					$this->parsing[$class] = (new $class($this->commentaires))->$method();
				}
			}
			 else {
				 if (strstr($method, 'html')) {
				 	$html       = (new $class($this->parsing))->$method($id, $this->model);
				 	$this->html .= $html;
					 return $html;
				 } else {
				 	$this->parsing[$class] = (new $class($this->commentaires))->$method();
				 }
			 }
		}
		return $this;
    }

	/**
	 * @param string $path
	 * @return string
	 */
    public function to_html($path)
    {

    	$this->html = "<html>
    <head>
        <meta name='viewport' 
        	  content='width=device-width, initial-scale=1, shrink-to-fit=no'>
        <meta charset='utf-8'>
        <title> Documentation API Rest </title>
        <link rel='stylesheet'
              href='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css' 
              integrity='sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm' 
              crossorigin='anonymous'>
    </head>
    <body>";

		$this->html .= "
<div class='container'>
	<div class='row' style='height: 15px;'></div>
    <div class='card'>";
		$i = 0;
		foreach ($this->parsing['\ormframework\custom\annotations\method'] as $model => $method) {
			$this->model('to_html', $i);
			$i++;
			foreach ($method as $id => $name) {
				if($id > 0) {
					$this->html .= "
		<div class='row'>
			<div class='col-12'>
				<hr/>
			</div>
		</div>";
				}
				$this->method('to_html', $id);
				$this->html .= "
		<div class='card-body'>
			<div class='row'>
				<div class='col-12'>";
				foreach ($this->parsing as $annotation => $value) {
                    $annotation = str_replace('\ormframework\custom\annotations\\', '', $annotation);
					$this->html .= "<div class='row'>";
					if($annotation !== 'method') {
						$this->$annotation('to_html', $id);
					}
					$this->html .= "</div>";
				}
				$this->html .= "
				</div>
			</div>
		</div>";

			}
    	}
    	$last_update = date('d/m/Y H:i:s');
		$this->html .= "
	</div>
	<footer class='card-footer'>
		derniere modification : <b>{$last_update}</b>
	</footer>
</div>";
		if(DEBUG)
			$this->html .= "
<div class='container'>
	<div class='row'>
		<div class='col-12'>
			<pre>".self::var_dump($this->commentaires)."</pre>
		</div>
	</div>
</div>";
$this->html .= "<script src='https://code.jquery.com/jquery-3.2.1.slim.min.js' 
        integrity='sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN' 
        crossorigin='anonymous'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js' 
        integrity='sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q' 
        crossorigin='anonymous'></script>
<script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js' 
        integrity='sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl' 
        crossorigin='anonymous'></script>
</body>
</html>";

		if(!is_dir(str_replace(basename($path), '', $path))) {
			mkdir(str_replace(basename($path), '', $path), 0777, true);
		}
		$tmp = file_get_contents($path);
		$tmp = preg_replace('`\<footer[^\>]+\>[^µ]+<\/html\>`', '', $tmp);

		if(!strstr($this->html, $tmp)) {
			file_put_contents($path, $this->html);
		}
		return $this->html;
    }

	public function reset()
    {
        $this->commentaires = [];
    }
}