<?php

namespace ormframework;

require_once 'utils.php';

if($date) {
    if(DEBUG)
        log_loading_module($date, 'module '.$module_name.'-custom chargé en version '.$module_confs->version);
}