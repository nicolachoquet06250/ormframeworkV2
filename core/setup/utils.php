<?php

class utils {
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

    public function get_manager($type = null)
    {
        return $type ? global_manager::instence()->$type() : global_manager::instence();
    }
}