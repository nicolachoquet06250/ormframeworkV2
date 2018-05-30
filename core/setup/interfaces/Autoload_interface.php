<?php

interface Autoload_interface {
	public function __construct($fils=[], $date, $module_name, $module_confs, $type, $status='success', $debug=false);

	function log($date, $module_name, $module_conf, $type, $status);
}