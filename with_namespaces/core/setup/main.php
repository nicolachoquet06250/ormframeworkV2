<?php

namespace ormframework\core\setup;

class main
{
    private static $instence;

    /**
     * main constructor.
     * @param $argv
     */
    private function __construct($argv) {
        new setup($argv);
    }

    /**
     * @param array $argv
     * @return main
     */
    public static function instence($argv) {
        if(self::$instence == null) {
            self::$instence = new main($argv);
        }

        return self::$instence;
    }
}