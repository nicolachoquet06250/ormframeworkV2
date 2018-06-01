<?php

class hello_world extends command
{
    public function __construct(array $args = []) {}

    public function say() {
        echo 'Hello World !'."\n";
    }
}