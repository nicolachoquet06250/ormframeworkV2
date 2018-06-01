<?php

require_once 'utils.php';
require_once 'interfaces/manager.php';
require_once 'managers/error_manager.php';
require_once 'managers/services_manager.php';
require_once 'router.php';
require_once 'setup.php';
require_once 'main.php';
require_once 'global_manager.php';

if(DEBUG)
    log_loading_module($date, 'module '.$module_name.'-core chargé en version '.$module_confs->version);