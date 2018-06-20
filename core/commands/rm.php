<?php
namespace ormframework\core\commands;

use \Exception;

class rm extends command
{
    public function __construct(array $args = [])
    {
        $this->argv = $args;
    }

    /**
     * @throws Exception
     */
    public function command()
    {
        $scriptName = $this->get_from_name('script_name') ? $this->get_from_name('script_name') : $this->argv[0];
        $commandName = str_replace(' ', '_', $scriptName);

        if (is_file("custom/commands/{$commandName}.php")) {
            unlink("custom/commands/{$commandName}.php");

            $this->get_manager('command')->delete_command($commandName);
        } else {
            throw new Exception("La commande {$commandName} n'existe pas");
        }
    }

    /**
     * @throws Exception
     */
    public function method()
    {
        $name = $this->get_from_name('name') ? $this->get_from_name('name') : $this->argv[0];
        if ($this->get_from_name('from_command') || $this->get_from_name('command')) {
            $from_command = str_replace(' ', '_', ($this->get_from_name('from_command') ? $this->get_from_name('from_command') : $this->get_from_name('command')));
        } else {
            $from_command = str_replace(' ', '_', $this->argv[1]);
        }

        if (preg_match("`[^µ]+public\ function\ {$name}\(\) \{[^µ]+}\\n`", file_get_contents("custom/commands/{$from_command}.php"))) {
            $command_class_content = file_get_contents("custom/commands/{$from_command}.php");
            preg_replace_callback("`[^µ]+(public\ function\ {$name}\(\) \{[^µ]+}\\n)`", function ($matches) use (&$command_class_content) {
                $command_class_content = str_replace($matches[1], '', $command_class_content);
            }, $command_class_content);

            preg_replace_callback("`[^µ]+}([^µ]+})`", function ($matches) use (&$command_class_content) {
                $command_class_content = str_replace($matches[1], '', $command_class_content);
            }, $command_class_content);

            $command_class_content .= "\n\t}";

            file_put_contents("custom/commands/{$from_command}.php", $command_class_content);

            $this->get_manager('command')->delete_method($from_command, $name);
        } else {
            throw new Exception("La methode {$name} n'existe pas dans la commande {$from_command}");
        }
    }

    public function module() {
        $module = $this->get_from_name('module');
        $conf = $this->get_manager('services')->conf()->get_modules_conf();

        function rmdir_recursif($path) {
            $dir = opendir($path);
            while (($file = readdir($dir)) !== false) {
                if($file !== '.' && $file !== '..') {
                    if(is_dir($path.'/'.$file)) {
                        rmdir_recursif($path.'/'.$file);
                        rmdir($path.'/'.$file);
                    }
                    else {
                        unlink($path.'/'.$file);
                    }
                }
            }
        }

        rmdir_recursif("{$conf->modules->$module->location['core']}");
        rmdir("{$conf->modules->$module->location['core']}");
        rmdir_recursif("{$conf->modules->$module->location['custom']}");
        rmdir("{$conf->modules->$module->location['custom']}");

		$ligne      = "\n    <mapping directory=\"\$PROJECT_DIR$/{$conf->modules->$module->location['custom']}\" vcs=\"Git\" />";
		$vcs        = file_get_contents('./.idea/vcs.xml');
		$vcs        = str_replace($ligne, '', $vcs);
		file_put_contents('./.idea/vcs.xml', $vcs);

        $this->get_manager('services')->conf()->remove_module($module);
    }
}