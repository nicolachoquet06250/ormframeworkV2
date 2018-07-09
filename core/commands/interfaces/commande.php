<?php

namespace ormframework\core\commands\interfaces;

interface commande
{
    public function __call($name, $arguments);

    public static function rm_file_name_of_args($args);

    public static function instence();

    public function get_from_name($name);
}