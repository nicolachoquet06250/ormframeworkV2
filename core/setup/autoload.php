<?php

// Load interfaces
require_once 'interfaces/manager.php';

// Load classes
require_once 'utils_core.php';
require_once './custom/setup/utils.php';
require_once 'services_manager.php';

log_loading_module($date, 'module '.$module_name.'-core chargé en version '.$module_confs->version);