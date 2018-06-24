<?php

namespace ormframework;

use ormframework\core\commands\command;
use \Exception;
use ormframework\core\setup\ListOf;
use ormframework\custom\db_context\entity_test;
use ormframework\custom\db_context\user;
use sql_links\factories\Request;
use sql_links\factories\RequestConnexion;
use sql_links\interfaces\IRequest;

define(
	'DEBUG',
	(strstr(implode('_', $argv), '--debug') || strstr(implode('_', $argv), '-d'))
);

if(DEBUG)
	ini_set('display_errors', 'on');

require_once 'Loading.php';
require_once 'autoload.php';

try {
    command::instence(command::rm_file_name_of_args($argv));
} catch (Exception $e) {
    exit($e->getMessage()."\n");
}