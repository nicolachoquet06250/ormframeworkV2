<?php

namespace ormframework\core\setup;

class utils {
    const BLACK_TEXT = '0;37';
    const BLUE_TEXT = '0;34';
    const GREEN_TEXT = '0;32';
    const CYAN_TEXT = '0;36';
    const RED_TEXT = '0;31';
    const PURPLE_TEXT = '0;35';
    const BROWN_TEXT = '0;33';
    const YELLOW_TEXT = '1;33';
    const WHITE_TEXT = '0;30';

    public static function http_get($key='') {
        return isset($_GET[$key]) ? htmlentities($_GET[$key]) : null;
    }

    public static function http_post($key='') {
        return isset($_POST[$key]) ? htmlentities($_POST[$key]) : null;
    }

    public static function http_files($key='') {
        return isset($_FILES[$key]) ? htmlentities($_FILES[$key]) : null;
    }

    public static function var_dump($var) {
        ob_start();
        var_dump($var);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    /**
     * @param null $type
     * @param mixed $arguments
     * @return \ormframework\core\managers\services_manager|\ormframework\core\managers\error_manager|\ormframework\core\setup\global_manager|\ormframework\core\managers\command_manager
     */
    public function get_manager($type = null, $arguments = [])
    {
        return $type ? global_manager::instence()->$type($arguments) : global_manager::instence();
    }

    public function color_cli_text($text = 'Hello', $color=self::WHITE_TEXT) {
        return "\033[{$color}m".$text."\033[0m";
    }
}