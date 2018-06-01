<?php

define('DEBUG', json_decode(file_get_contents('core/ormf-modules-conf.json'))->debug);

if(DEBUG)
    ini_set('display_errors', 'on');

require_once 'autoload.php';

try {
    (new router())
        ->route(utils::http_get('path'));
}
catch (Exception $e) {
	exit($e->getMessage()."\n");
}