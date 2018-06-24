<?php

namespace ormframework;


require_once 'http_error.php';
require_once 'error_500.php';
require_once 'error_404.php';
require_once 'code_200.php';

if(DEBUG)
    Loading::log_loading_module($date, 'module '.$module_name.'-core chargé en version '.$module_confs->version);