<?php

//// Load interfaces
//require_once 'interfaces/service.php';
//
//// Load classes
//require_once 'my_first_service.php';

$fils = [
	'interfaces/service.php',
	'my_first_service.php'
];

new Autoload($fils, $date, $module_name, $module_confs, 'core', 'success', DEBUG, 'chargement des services-core');

//log_loading_module($date, 'chargement des services-core');