<?php

class Main
{
    private static $instence;

    /**
     * Main constructor.
     * @param $argv
     */
    private function __construct($argv) {
        new setup($argv);
    }

    /**
     * @param array $argv
     * @return Main
     */
    public static function instence($argv) {
        if(self::$instence == null) {
            self::$instence = new Main($argv);
        }

        return self::$instence;
    }
}