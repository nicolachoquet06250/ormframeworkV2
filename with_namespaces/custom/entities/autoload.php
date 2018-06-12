<?php

$dir = opendir('./custom/entities');
while (($file = readdir($dir)) !== false) {
    if($file !== '.' && $file !== '..') {
        require_once './custom/entities/'.$file;
    }
}

if(DEBUG)
log_loading_module($date, 'module '.$module_name.'-custom chargé en version '.$module_confs->version);