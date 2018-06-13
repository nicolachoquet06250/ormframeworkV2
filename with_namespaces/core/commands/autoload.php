<?php

namespace ormframework;

require_once 'interfaces/commande_interface.php';
require_once 'extended/command.php';

if(DEBUG)
log_loading_module($date, 'module '.$module_name.'-core chargé en version '.$module_confs->version);