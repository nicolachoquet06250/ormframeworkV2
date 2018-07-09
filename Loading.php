<?php

namespace ormframework;


class Loading
{
    private function __construct() {}

    public static function log_loading_module($date, $content, $type = 'success') {
        $type = $type === 'success' ? 'SUCCESS' : 'ERROR';
        $logs = file_get_contents("logs/{$date}.log");
        file_put_contents("logs/{$date}.log", $logs.$date.' [ '.$type.' ] => '.$content.''."\n");
    }

    public static function load_module($module_name, $module_confs, $date) {
        if ($module_confs->autoload === true) {
            require_once $module_confs->location['core'].'/autoload.php';
            require_once $module_confs->location['custom'].'/autoload.php';
        } else {
            if ($module_confs->autoload['core']) {
                require_once $module_confs->location['core'].'/autoload.php';
            }
            if ($module_confs->autoload['custom']) {
                require_once $module_confs->location['custom'].'/autoload.php';
            }
        }
    }

    public static function module_exists($module_confs) {
        if(isset($module_confs->location['custom'])) {
            return is_dir($module_confs->location['core']) && is_dir($module_confs->location['custom']);
        }
        else {
            return is_dir($module_confs->location['core']);
        }
    }
}