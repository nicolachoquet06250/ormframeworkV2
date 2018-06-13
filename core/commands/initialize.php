<?php

namespace ormframework\core\commands;


use ormframework\core\commands\command;

class initialize extends command
{
    public function __construct(array $args = [])
    {
        $this->argv = $args;
    }

    public function dependencies() {
        require_once 'initialize_dependences.php';

        \ormframework\initialize_dependences::go();
    }
}