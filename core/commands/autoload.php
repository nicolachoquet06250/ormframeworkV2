<?php

require_once 'interfaces/commande_interface.php';
require_once 'extended/command.php';

$fils = [
	'interfaces/commande_interface.php',
	'extended/command.php'
];

new Autoload($fils, $date, $module_name, $module_confs, 'core', 'success', DEBUG);

//log_loading_module($date, 'module '.$module_name.'-core chargé en version '.$module_confs->version);