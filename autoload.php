<?php

	ini_set('display_errors', 'on');

	function log_loading_module($date, $content, $type='success') {
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

	$conf_core = json_decode(file_get_contents('core/ormf-modules-conf.json'));
	$conf_custom = json_decode(file_get_contents('custom/ormf-modules-conf.json'));

	$conf = $conf_core;

	foreach ($conf_custom as $cnf => $value_cnf) {
		if(gettype($value_cnf) === 'object') {
			foreach ($value_cnf as $sous_cnf => $sous_value_cnf) {
				foreach ($sous_value_cnf as $sous_sous_cnf => $sous_sous_value_cnf) {
					if($sous_sous_cnf === 'location') {
						$conf->$cnf->$sous_cnf->$sous_sous_cnf = [
							'core' => 'core/'.$conf->$cnf->$sous_cnf->$sous_sous_cnf
						];

						if($sous_sous_value_cnf) {
							$conf->$cnf->$sous_cnf->$sous_sous_cnf['custom'] = 'custom/'.$sous_sous_value_cnf;
						}

					}
					elseif ($sous_sous_cnf === 'autoload') {
						if($conf->$cnf->$sous_cnf->$sous_sous_cnf !== $sous_sous_value_cnf) {
							$conf->$cnf->$sous_cnf->$sous_sous_cnf = [
								'core' => $conf->$cnf->$sous_cnf->$sous_sous_cnf,
								'custom' => $sous_sous_value_cnf
							];
						}

					}
				}

			}
		}
		else {
			$conf->$cnf = $value_cnf;
		}
	}


	$date = date('Y-m-d_H-i-s');
	if(!is_dir('./logs')) {
		mkdir('logs', 0777, true);
	}

	file_put_contents("logs/{$date}.log", "\n");
	foreach ($conf->modules as $module_name => $module_confs) {
		if($module_confs->disabled) {
			if($module_confs->enable) {
				load_module($module_name, $module_confs, $date);
			}
		}
		else {
			load_module($module_name, $module_confs, $date);
		}
	}

	require_once 'core/services/autoload.php';
	require_once 'custom/services/autoload.php';