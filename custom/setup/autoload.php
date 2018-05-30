<?php

require_once 'utils.php';
$fils = ['utils.php'];

if($date) {
	new Autoload($fils, $date, $module_name, $module_confs, 'custom', 'success', DEBUG);
//	log_loading_module($date, 'module '.$module_name.'-custom chargé en version '.$module_confs->version);
}