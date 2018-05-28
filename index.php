<?php

require_once 'autoload.php';

// echo 'tout à été chargé !!<br><a href="logs/'.$date.'.log" target="_blank">Voir les logs</a>';

try {
	services_manager::instence()->get_test();//->my_first_service('test');
}
catch (Exception $e) {
	exit($e->getMessage()."\n");
}