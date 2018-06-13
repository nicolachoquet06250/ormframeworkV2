<?php

namespace ormframework;

use ormframework\core\commands\command;
use \Exception;

define(
	'DEBUG',
	(strstr(implode('_', $argv), '--debug') || strstr(implode('_', $argv), '-d'))
);

if(DEBUG)
	ini_set('display_errors', 'on');

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
require_once 'core/commands/autoload.php';

try {
    command::instence(command::rm_file_name_of_args($argv));
} catch (Exception $e) {
    exit($e->getMessage()."\n");
}