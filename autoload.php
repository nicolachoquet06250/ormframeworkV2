<?php

function log_loading_module($date, $content, $type = 'success') {
	$type = $type === 'success' ? 'SUCCESS' : 'ERROR';
	$logs = file_get_contents("logs/{$date}.log");
	file_put_contents("logs/{$date}.log", $logs.$date.' [ '.$type.' ] => '.$content.''."\n");
}

function load_module($module_name, $module_confs, $date) {
	if ($module_confs->autoload === true) {
		require_once $module_confs->location['core'].'/autoload.php';
		require_once $module_confs->location['custom'].'/autoload.php';
	} else {
		if ($module_confs->autoload['core']) {
			require_once $module_confs->location['core'].'/autoload.php';
		}
		if ($module_confs->autoload['custom']) {
			require_once $module_confs->location['custom'].'/autoload.php';
		}
	}
}

require_once 'core/setup/autoload.php';
require_once 'core/services/interfaces/service.php';
require_once 'core/services/autoload.php';
require_once 'custom/services/autoload.php';

$conf = (new utils())->get_manager('services')->conf()->get_modules_conf();

if (DEBUG) {
	$date = date('Y-m-d_H-i-s');
	if (!is_dir('./logs')) {
		mkdir('logs', 0777, true);
	}

	file_put_contents("logs/{$date}.log", "\n");
}

foreach ($conf->modules as $module_name => $module_confs) {
	if ($module_confs->disabled) {
		if ($module_confs->enable) {
			load_module($module_name, $module_confs, $date);
		}
	} else {
		load_module($module_name, $module_confs, $date);
	}
}