<?php

class router
{
	private $tmp_routes = [];
    private $routes = [
        'index.htm' => 'index.html',
        'voici_ma_doc.htm'   => 'doc/index.html'
    ];

    private function parse_file($file) {
    	$regex_class_model = '`[\<\?a-zA-Z\t\n]+class\ ([a-zA-Z0-9\_]+)[\.\ a-zA-Z0-9\{\n\t\/\*\@é\\\'\$\_\(\)\ =\;\-\>\}]+`';
    	$regex_1 = '`\/\*\*[\n\t\ ]+([\n\t\ \*\@a-zA-Zé\\\'\$\-\_\.\/ 0-9]+)[\n\t\ ]+ \*\*\/`';
    	$regex_2 = '`[\t\ \*\+]([@a-zA-Zé\\\'\$\-\_\.\/\] +)`';

    	$class = '';
    	$commentaires = [];

		$content = file_get_contents('./custom/mvc/models/'.$file);

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
			$commentaires[$key][0] = '@description '.$commentaires[$key][0];
			foreach ($commentaires[$key] as $key2 => $commentaire2) {
				$commentaires[$key][$key2] = str_replace(" * ", '', $commentaire2);
				$commentaires[$key][$key2] = str_replace('* ', '', $commentaire2);

				//enlèvement des espaces en surplus.
				preg_replace_callback('`[\ ]+([\@a-zA-Z0-9\ \$\-\_\.é\\\'àùè]+)`', function ($matches) use (&$commentaires, $key2, $key) {
					$commentaires[$key][$key2] = $matches[1];
				}, $commentaires[$key][$key2]);
				if($commentaire2 === ' *') {
					unset($commentaires[$key][$key2]);
				}
			}
			$commentaires[$key][] = '@model '.$class;
		}

		return $commentaires;
	}

    public function route($url) {

    	$dir = opendir('./custom/mvc/models');
    	while (($file = readdir($dir)) !== false) {
    		if($file !== '.' && $file !== '..') {
    			$result = $this->parse_file($file);
    			var_dump($result);
			}
		}

		exit();

        if(isset($this->routes[$url]) || count(explode('.', $url)) > 1) {
            if(isset($this->routes[$url])) {
                if (is_file('custom/website/' . $this->routes[$url])) {
                    $type = 'custom';
                    echo file_get_contents($type.'/website/'.$this->routes[$url]);
                } elseif (is_file('core/website/' . $this->routes[$url])) {
                    $type = 'core';
                    echo file_get_contents($type.'/website/'.$this->routes[$url]);
                } else {
                    $type = 'core';

                    ${404} = error_manager::instence()->http_error();
                    ${404}->code = 404;
                    ${404}->header();

                    if(is_file('custom/website/errors/404.html')) {
                        $type = 'custom';
                    }
                    elseif (is_file('core/website/errors/404_.php')) {
                        $type = 'core';
                    }
                    echo file_get_contents($type.'/website/errors/404.html');
                }
            }
            else {
                $type = 'core';

                ${404} = error_manager::instence()->http_error();
                ${404}->code = 404;
                ${404}->header();

                if(is_file('./custom/website/errors/404.html')) {
                    $type = 'custom';
                }
                elseif (is_file('./core/website/errors/404.html')) {
                    $type = 'core';
                }

                echo file_get_contents('./'.$type.'/website/errors/404.html');
            }
        }
        else {
            Main::instence(utils::http_get('path'));
        }
    }
}