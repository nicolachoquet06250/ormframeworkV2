<?php

$fils = [
	'http_error.php',
	'error_500.php',
	'error_404.php',
	'code_200.php'
];

new Autoload($fils, $date, $module_name, $module_confs, 'core', 'success', DEBUG);

//require_once 'http_error.php';
//require_once 'error_500.php';
//require_once 'error_404.php';
//require_once 'code_200.php';