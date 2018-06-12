<?php

namespace ormframework;

use \Exception;
use \ormframework\core\setup\router;
use \ormframework\core\setup\core_utils;

define('DEBUG', json_decode(file_get_contents('core/ormf-modules-conf.json'))->debug);

//if(DEBUG)
    ini_set('display_errors', 'on');

require_once 'autoload.php';

try {
	\ormframework\core\annotation\PhpDocParser::instence('custom/mvc/models', true);
	\ormframework\core\annotation\PhpDocParser::instence()->method()->description()->httpVerb()->params()->return()->route()
							->to_html('./custom/website/doc/index.html');

	if(!core_utils::http_get('path')) {
        if (strstr($_SERVER['REQUEST_URI'], (new core_utils())->get_manager('services')->conf()->get_modules_conf()->project_directory)) {
            $_SERVER['REQUEST_URI'] = str_replace((new core_utils())->get_manager('services')->conf()->get_modules_conf()->project_directory . '/', '', $_SERVER['REQUEST_URI']);
        }
		$_GET['path'] = htmlentities(str_replace('/?', '', $_SERVER['REQUEST_URI']));
	}
    router::instence()->get_defaults_routes()->route(core_utils::http_get('path'));
}
catch (Exception $e) {
	exit($e->getMessage()."\n");
}