<?php

namespace ormframework;

use ormframework\core\setup\utils;

require_once 'core/setup/utils.php';
require_once 'custom/setup/utils.php';

require_once 'core/setup/autoload.php';
require_once 'core/services/interfaces/service.php';
require_once 'core/services/autoload.php';
require_once 'custom/services/autoload.php';

$conf = (new utils())->get_manager('services')->conf()->get_modules_conf();

$date = date('Y-m-d_H-i-s');
if (DEBUG) {
	if (!is_dir('./logs')) {
		mkdir('logs', 0777, true);
	}

	file_put_contents("logs/{$date}.log", "\n");
}

foreach ($conf->modules as $module_name => $module_confs) {
	if (isset($module_confs->disabled) && $module_confs->disabled === true) {
		if ($module_confs->enable === true) {
		    if(Loading::module_exists($module_confs)) {
                Loading::load_module($module_name, $module_confs, $date);
            }
		}
	} else {
	    if(Loading::module_exists($module_confs)) {
            Loading::load_module($module_name, $module_confs, $date);
        }
	}
}