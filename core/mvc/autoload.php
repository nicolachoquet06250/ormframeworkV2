<?php

namespace ormframework;

require_once 'interfaces/Controller_interface.php';
require_once 'interfaces/Model_interface.php';
require_once 'interfaces/view_interface.php';
require_once 'view.php';
require_once 'Controller.php';
require_once 'Model.php';

if(DEBUG)
    Loading::log_loading_module($date, 'module '.$module_name.'-core chargé en version '.$module_confs->version);