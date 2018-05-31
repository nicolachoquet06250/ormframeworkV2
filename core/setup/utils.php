<?php

class utils {
    public static function http_get($key='') {
        return isset($_GET[$key]) ? $_GET[$key] : null;
    }

    public static function http_post($key='') {
        return isset($_POST[$key]) ? $_POST[$key] : null;
    }

    public static function http_files($key='') {
        return isset($_FILES[$key]) ? $_FILES[$key] : null;
    }

    public static function var_dump($var) {
        ob_start();
        var_dump($var);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
}