<?php

namespace ormframework\custom\setup;

class utils extends \ormframework\core\setup\utils {

    static function helloWorld() {
        return 'hello_world';
    }

    public function hello_world() {
        return self::helloWorld();
    }

}