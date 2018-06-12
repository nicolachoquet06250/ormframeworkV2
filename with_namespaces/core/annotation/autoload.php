<?php

require_once 'interfaces/annotation_interface.php';
require_once 'PhpDocParser.php';

if(DEBUG)
log_loading_module($date, 'module '.$module_name.'-core chargé en version '.$module_confs->version);