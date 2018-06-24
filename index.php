<?php

namespace ormframework;

use \Exception;
use \ormframework\core\annotation\PhpDocParser;
use \ormframework\core\setup\router;
use \ormframework\custom\setup\utils;


$path_prefix = '';

define('DEBUG', json_decode(file_get_contents($path_prefix.'core/ormf-modules-conf.json'))->debug);

if(DEBUG)
    ini_set('display_errors', 'on');

require_once 'Loading.php';
require_once 'autoload.php';

try {

	PhpDocParser::instence('custom/mvc/models', true);
	PhpDocParser::instence()->method()->description()->httpVerb()->params()->return()->route()
							->to_html('./custom/website/doc/index.html');

    if(!utils::http_get('path')) {
        if (strstr($_SERVER['REQUEST_URI'], (new utils())->get_manager('services')->conf()->get_modules_conf()->project_directory)) {
            $_SERVER['REQUEST_URI'] = str_replace((new utils())->get_manager('services')->conf()->get_modules_conf()->project_directory . '/', '', $_SERVER['REQUEST_URI']);
        }
		$_GET['path'] = htmlentities(str_replace('/?', '', $_SERVER['REQUEST_URI']));
	}
    router::instence()->get_defaults_routes()->route(utils::http_get('path'));
}
catch (Exception $e) {
	exit($e->getMessage()."\n");
}