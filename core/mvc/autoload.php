<?php

//	require_once 'interfaces/Controller_interface.php';
//	require_once 'interfaces/Model_interface.php';
//	require_once 'interfaces/view_interface.php';
//	require_once 'view.php';
//	require_once 'Controller.php';
//	require_once 'Model.php';

	$fils = [
		'interfaces/Controller_interface.php',
		'interfaces/Model_interface.php',
		'interfaces/view_interface.php',
		'view.php',
		'Controller.php',
		'Model.php'
	];

new Autoload($fils, $date, $module_name, $module_confs, 'core', 'success', DEBUG);

//	log_loading_module($date, 'module '.$module_name.'-core chargé en version '.$module_confs->version);