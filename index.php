<?php

define('DEBUG', false);

if(DEBUG)
    ini_set('display_errors', 'on');

require_once 'autoload.php';

try {
    (new router())
        ->route(utils::http_get('path'))
        ->get_html_doc();
}
catch (Exception $e) {
	exit($e->getMessage()."\n");
}