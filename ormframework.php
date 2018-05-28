<?php

require_once 'autoload.php';

try {
    command::instence(command::rm_file_name_of_args($argv));
} catch (Exception $e) {
    exit($e->getMessage()."\n");
}