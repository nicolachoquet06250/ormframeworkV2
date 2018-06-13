<?php
namespace ormframework\core\commands;

class hello_world extends command
{
    public function __construct(array $args = [])
    {
        $this->argv = $args;
    }

    public function say() {
        echo 'Hello World !'."\n";
    }
}