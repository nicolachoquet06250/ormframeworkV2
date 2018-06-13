<?php

namespace ormframework;

use ormframework\core\setup\utils;

require_once 'core/setup/autoload.php';

class initialize_dependences extends utils
{
    private static $instence = null;
    private function __construct() {
        $modules = $this->get_manager('services')->conf()->get_modules_conf()->modules;

        function parcour_dir($directory, $module_path, &$body)
        {

            $dir = opendir($directory);
            while (($file = readdir($dir)) !== false) {
                if ($file !== '.' && $file !== '..'
                    && substr($file, 0, 1) !== '.'
                    && strtolower($file) !== 'demo'
                    && strtolower($file) !== 'readme.md'
                    && strtolower($file) !== 'readme.txt') {
                    if (is_dir("{$directory}/{$file}")) {
                        parcour_dir("{$directory}/{$file}", $module_path, $body);
                    } else {
                        $path = str_replace($module_path.'/', '', "{$directory}/{$file}");
                        $body .= "require_once '{$path}';\n";
                    }
                }
            }
        }

        foreach ($modules as $module) {
            if(isset($module->repository)) {
                // Pour le core
                if(!is_dir($module->location['core'])) {
                    mkdir($module->location['core'], 0777, true);
                }
                // Pour le custom
                if(!is_dir($module->location['custom'])) {
                    exec("{$module->repository->type} clone {$module->repository->path} {$module->location['custom']}");
                }
                if($module->autoload) {
                    file_put_contents("{$module->location['core']}/autoload.php", "<?php
        namespace ormframework;\n
                    
        if(DEBUG)
            log_loading_module(\$date, 'module '.\$module_name.'-core chargé en version '.\$module_confs->version);");

                    $start = "<?php\n
            namespace ormframework;\n";
                    $end = "if(DEBUG)
            log_loading_module(\$date, 'module '.\$module_name.'-custom chargé en version '.\$module_confs->version);";
                    $body = '';

                    parcour_dir($module->location['custom'], $module->location['custom'], $body);

                    file_put_contents("{$module->location['custom']}/autoload.php", $start . $body . $end);
                }
                else {
                    if ($module->autoload['core']) {
                        file_put_contents("{$module->location['core']}/autoload.php", "<?php
        namespace ormframework;
                    
        if(DEBUG)
            log_loading_module(\$date, 'module '.\$module_name.'-core chargé en version '.\$module_confs->version);");
                    }

                    if ($module->autoload['custom']) {
                        $start = "<?php\n
                        namespace ormframework;\n";
                        $end = "if(DEBUG)
            log_loading_module(\$date, 'module '.\$module_name.'-custom chargé en version '.\$module_confs->version);";
                        $body = '';

                        parcour_dir($module->location['custom'], $module->location['custom'], $body);

                        file_put_contents("{$module->location['custom']}/autoload.php", $start . $body . $end);
                    }
                }
            }
        }
    }
    public static function go() {
        if(self::$instence === null) {
            self::$instence = new initialize_dependences();
        }
        return self::$instence;
    }
}