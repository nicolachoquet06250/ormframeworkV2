<?php

class hello extends command
{
    public function __construct(array $args = []) {}

    public function say($args) {
        var_dump('SAY');
        var_dump($args);
    }

    public function world() {
        var_dump('WORLD');
    }
}