<?php

require_once 'autoload.php';

try {
	Main::instence(utils::http_get('path'));
}
catch (Exception $e) {
	exit($e->getMessage()."\n");
}