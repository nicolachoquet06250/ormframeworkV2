<?php

$fils = [];

new Autoload($fils, $date, $module_name, $module_confs, 'core', 'success', DEBUG);

//	log_loading_module($date, 'module '.$module_name.'-core chargé en version '.$module_confs->version);