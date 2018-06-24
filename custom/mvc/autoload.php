<?php

namespace ormframework;

	$dir = opendir('./custom/mvc/controllers');
	while (($file = readdir($dir)) !== false) {
	    if ($file !== '.' && $file !== '..') {
	        require_once './custom/mvc/controllers/'.$file;
        }
    }

    $dir = opendir('./custom/mvc/models');
    while (($file = readdir($dir)) !== false) {
        if ($file !== '.' && $file !== '..') {
            require_once './custom/mvc/models/'.$file;
        }
    }

    $dir = opendir('./custom/mvc/views');
    while (($file = readdir($dir)) !== false) {
        if ($file !== '.' && $file !== '..') {
            require_once './custom/mvc/views/'.$file;
        }
    }

if(DEBUG)
    Loading::log_loading_module($date, 'module '.$module_name.'-custom chargé en version '.$module_confs->version);