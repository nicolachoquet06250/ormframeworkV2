<?php

require_once 'interfaces/Autoload_interface.php';

class Autoload implements Autoload_interface {

	public function __construct($fils=[], $date, $module_name, $module_confs, $type, $status='success', $debug=false, $message=null) {
		foreach ($fils as $file) {
			require_once $file;
		}
		$this->log($date, $module_name, $module_confs, $type, $status, $debug, $message);
	}

	function log($date, $module_name, $module_confs, $type, $status, $debug=false, $message=null) {
		if( $debug ) {
			$content = "module {$module_name}-{$type} chargé en version {$module_confs->version}";
			if($message) {
				$content = $message;
			}
			$status  = $status === 'success' ? 'SUCCESS' : 'ERROR';
			$logs    = file_get_contents("logs/{$date}.log");
			file_put_contents("logs/{$date}.log", "{$logs}{$date} [ {$status} ] => {$content}\n");
		}
	}
}