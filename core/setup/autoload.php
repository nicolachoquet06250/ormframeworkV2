<?php

$fils = [
	'interfaces/manager.php',
	'router.php',
	'utils_core.php',
	'./custom/setup/utils.php',
	'error_manager.php',
	'services_manager.php',
	'setup.php',
	'main.php'
];

new Autoload($fils, $date, $module_name, $module_confs, 'core', 'success', DEBUG);

//// Load interfaces
//require_once 'interfaces/manager.php';
//
//// Load classes
//require_once 'router.php';
//require_once 'utils_core.php';
//require_once './custom/setup/utils.php';
//require_once 'error_manager.php';
//require_once 'services_manager.php';
//require_once 'setup.php';
//require_once 'main.php';
//
//log_loading_module($date, 'module '.$module_name.'-core chargé en version '.$module_confs->version);