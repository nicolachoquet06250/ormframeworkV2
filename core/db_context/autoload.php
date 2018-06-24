<?php

namespace ormframework;

require_once 'entity.php';
require_once 'db_context.php';

if(DEBUG)
Loading::log_loading_module($date, 'module '.$module_name.'-core chargé en version '.$module_confs->version);