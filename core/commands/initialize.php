<?php

namespace ormframework\core\commands;


use ormframework\initialize_dependences;

class initialize extends command
{
    public function __construct(array $args = [])
    {
        $this->argv = $args;
    }

    public function dependencies() {
        require_once 'initialize_dependences.php';

        initialize_dependences::go();
    }
}