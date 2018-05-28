<?php

class utils_core {
	public static function http_get($key='') {
	    return isset($_GET[$key]) ? $_GET[$key] : null;
    }

    public static function http_post($key='') {
        return isset($_POST[$key]) ? $_POST[$key] : null;
    }

    public static function http_files($key='') {
        return isset($_FILES[$key]) ? $_FILES[$key] : null;
    }
}