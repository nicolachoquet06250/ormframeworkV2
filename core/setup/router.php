<?php

class router
{
	private $tmp_routes = [];
    private $routes = [
        'index.htm' => 'index.html',
        'voici_ma_doc.htm'   => 'doc/index.html'
    ];

    private function parse_file($file) {
		$content = file_get_contents('./custom/mvc/models/'.$file);
		preg_replace_callback('`\/\*\*[\n\t\ ]+([\n\t\ \*\@a-zA-Zé\\\'\$\-\_\.\/ 0-9]+)[\n\t\ ]+ \*\*\/`', function ($matches) use (&$content) {
			if(strstr($content, '<?php')) {
				$content = $matches[1];
			}
			else {
				$content .= $matches[1];
			}
			var_dump($content);
		}, $content);
		$content_tmp = [];
		preg_replace_callback('`[\t\ \*\+]([@a-zA-Zé\\\'\$\-\_\.\/\ ]+)`', function ($matches) use (&$content_tmp) {
			if ($matches[0] !== "	") {
				foreach ($matches as $id => $match) {

					$matches[$id] = str_replace('* ', '', $match);
				}
				$matches = $matches[0];
				$content_tmp[] = $matches;
			}
		}, $content);
		$tmp = [];

		foreach ($content_tmp as $item => $value) {
			if(strstr($value, '@')) {
				$tmp[] = $value;
			}
		}
		$content_tmp = $tmp;
		foreach ($content_tmp as $item => $value) {

			$ligne = explode(' ', $value);
			if($ligne[0] === '@description') {
				$ligne_tmp = [];
				for($i=1,$max=count($ligne); $i<$max; $i++) {
					$ligne_tmp[] = $ligne[$i];
					unset($ligne[$i]);
				}
				$ligne[1] = implode(' ', $ligne_tmp);
			}
			$content_tmp[$item] = $ligne;
		}
		$content_tmp['actual'] = $content_tmp;
		$content_tmp['tmp'] = [];
		foreach ($content_tmp['actual'] as $item => $value) {
			$key = $value[0];
			switch ($value[0]) {
				case '@description':
					$val = [
						'content' => $value[1]
					];
					break;
				case '@method':
					$val = [
						'name' => $value[1]
					];
					break;
				case '@param':
					$val = [
						'type' => $value[1],
						'name' => $value[2]
					];
					break;
				case '@return':
					$val = [
						'type' => $value[1]
					];
					break;
				case '@route':
					$val = [
						'name' => $value[1]
					];
					break;
				default:
					$val = [];
					break;
			}

			$content_tmp['tmp'][$key][$item] = $val;
		}
		$content_tmp = $content_tmp['tmp'];
		$content = $content_tmp;

		return $content;
	}

    public function route($url) {

    	$dir = opendir('./custom/mvc/models');
    	while (($file = readdir($dir)) !== false) {
    		if($file !== '.' && $file !== '..') {
    			$this->parse_file($file);
			}
		}

//        if(isset($this->routes[$url]) || count(explode('.', $url)) > 1) {
//            if(isset($this->routes[$url])) {
//                if (is_file('custom/website/' . $this->routes[$url])) {
//                    $type = 'custom';
//                    echo file_get_contents($type.'/website/'.$this->routes[$url]);
//                } elseif (is_file('core/website/' . $this->routes[$url])) {
//                    $type = 'core';
//                    echo file_get_contents($type.'/website/'.$this->routes[$url]);
//                } else {
//                    $type = 'core';
//
//                    ${404} = error_manager::instence()->http_error();
//                    ${404}->code = 404;
//                    ${404}->header();
//
//                    if(is_file('custom/website/errors/404.html')) {
//                        $type = 'custom';
//                    }
//                    elseif (is_file('core/website/errors/404_.php')) {
//                        $type = 'core';
//                    }
//                    echo file_get_contents($type.'/website/errors/404.html');
//                }
//            }
//            else {
//                $type = 'core';
//
//                ${404} = error_manager::instence()->http_error();
//                ${404}->code = 404;
//                ${404}->header();
//
//                if(is_file('./custom/website/errors/404.html')) {
//                    $type = 'custom';
//                }
//                elseif (is_file('./core/website/errors/404.html')) {
//                    $type = 'core';
//                }
//
//                echo file_get_contents('./'.$type.'/website/errors/404.html');
//            }
//        }
//        else {
//            Main::instence(utils::http_get('path'));
//        }
    }
}