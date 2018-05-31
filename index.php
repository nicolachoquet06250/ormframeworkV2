<?php

ini_set('display_errors', 'on');
define('DEBUG', true);

require_once 'autoload.php';

try {
    (new router())->route(utils::http_get('path'));
}
catch (Exception $e) {
	exit($e->getMessage()."\n");
}