<?php

define('DEBUG', json_decode(file_get_contents('core/ormf-modules-conf.json'))->debug);

if(DEBUG)
    ini_set('display_errors', 'on');

require_once 'autoload.php';

try {
	PhpDocParser::instence('custom/mvc/models', true)->model();
	PhpDocParser::instence()->method();
	PhpDocParser::instence()->description();
	PhpDocParser::instence()->httpVerb();
	PhpDocParser::instence()->params();
	PhpDocParser::instence()->return();
	PhpDocParser::instence()->route();
	PhpDocParser::instence()->to_html('./custom/website/doc/index.html');
    (new router())
        ->route(utils::http_get('path'));
}
catch (Exception $e) {
	exit($e->getMessage()."\n");
}