<?php

$fils = [

];

new Autoload($fils, $date, $module_name, $module_confs, 'custom', 'success', DEBUG);

//log_loading_module($date, 'module '.$module_name.'-custom chargé en version '.$module_confs->version);