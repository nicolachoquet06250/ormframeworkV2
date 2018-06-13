<?php

namespace ormframework;

// Load interfaces
require_once 'interfaces/service.php';

// Load classes
require_once 'my_first_service.php';

if(DEBUG)
    log_loading_module($date, 'chargement des services-core');