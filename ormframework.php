<?php

if(strstr(implode('_', $argv), '--debug') || strstr(implode('_', $argv), '-d')) {
    define('DEBUG', true);
}
else {
    define('DEBUG', false);
}

require_once 'core/setup/autoload.php';
require_once 'core/commands/autoload.php';

try {
    command::instence(command::rm_file_name_of_args($argv));
} catch (Exception $e) {
    exit($e->getMessage()."\n");
}