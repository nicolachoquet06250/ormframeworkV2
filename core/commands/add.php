<?php

class add extends command
{
    public function __construct(array $args = [])
    {
        $this->argv = $args;
    }

    public function command()
    {
        $scriptName = $this->get_from_name('script_name') ? $this->get_from_name('script_name') : $this->argv[0];
        $commandName = str_replace(' ', '_', $scriptName);

        file_put_contents("custom/commands/{$commandName}.php", "<?php
    class {$commandName} extends command
    {
        public function __construct(array \$args = []) {
            \$this->argv = \$args;
        }
        
        public function exec() {
            var_dump('Hello World');
        }
    }");
        $commands_conf = json_decode(file_get_contents('core/commands/enable_commands.json'), true);
        $commands_conf[$commandName] = [];
        file_put_contents('core/commands/enable_commands.json', json_encode($commands_conf));
    }

    /**
     * @throws Exception
     */
    public function method()
    {
        $name = $this->get_from_name('name') ? $this->get_from_name('name') : $this->argv[0];
        if ($this->get_from_name('to_command') || $this->get_from_name('command')) {
            $to_command = str_replace(' ', '_', ($this->get_from_name('to_command') ? $this->get_from_name('to_command') : $this->get_from_name('command')));
        } else {
            $to_command = str_replace(' ', '_', $this->argv[1]);
        }
        if (is_file("custom/commands/{$to_command}.php")) {
            if (!preg_match('`[^µ]+public\ function\ ' . $name . '\(`', file_get_contents("custom/commands/{$to_command}.php"))) {
                $command_class_content = file_get_contents("custom/commands/{$to_command}.php");
                $command_class_content = substr($command_class_content, 0, strlen($command_class_content) - 1);
                $command_class_content .= "
            public function {$name}() {
                var_dump('{$name} Working');
            }
        }";
                file_put_contents("custom/commands/{$to_command}.php", $command_class_content);

                $commands_conf = json_decode(file_get_contents('core/commands/enable_commands.json'), true);
                $commands_conf[$to_command][] = [];
                $commands_conf[$to_command][count((array)$commands_conf[$to_command]) - 1]['method'] = $name;
                $commands_conf[$to_command][count((array)$commands_conf[$to_command]) - 1]['args'] = 2;

                file_put_contents('core/commands/enable_commands.json', json_encode($commands_conf));
            } else {
                throw new Exception("La methode {$name} existe déja dans la commande {$to_command}");
            }
        } else {
            throw new Exception("La commande {$to_command} n'existe pas");
        }
    }

    public function module()
    {
        $moduleName = $this->get_from_name('module_name') ? $this->get_from_name('module_name') : $this->argv[1];
        $autoloadCustom = $this->get_from_name('custom_autoload') ? $this->get_from_name('custom_autoload') : true;
        $autoloadCustom = ($autoloadCustom === 'false') ? false : true;
        $autoloadCore = $this->get_from_name('core_autoload') ? $this->get_from_name('core_autoload') : true;
        $autoloadCore = ($autoloadCore === 'false') ? false : true;
        $pathCustom = $this->get_from_name('custom_path') ? $this->get_from_name('custom_path') : $moduleName;
        $pathCore = $this->get_from_name('core_path') ? $this->get_from_name('core_path') : $moduleName;

        if (is_dir($path = ($this->get_from_name('path') ? $this->get_from_name('path') : $this->argv[0]))) {

            function copy_directory($path_source, $path_dest)
            {
                $dir = opendir($path_source);
                mkdir("{$path_dest}");

                while (($file = readdir($dir)) !== false) {
                    if ($file !== '.' && $file !== '..' && $file !== '.idea') {
                        if (is_dir("$path_source/{$file}")) {
                            copy_directory("$path_source/{$file}", "$path_dest/{$file}");
                        } else {
                            copy("$path_source/{$file}", "{$path_dest}/{$file}");
                        }
                    }
                }
            }

            // partie custom
            // copie de la lib dans un module
            copy_directory("$path", "custom/{$pathCustom}");
            if ($autoloadCustom) {
                if (!is_file("custom/{$pathCustom}/autoload.php")) {
                    file_put_contents("custom/{$pathCustom}/autoload.php", "<?php
    require_once 'Autoload.php';
    Auto::load();
                
    if(DEBUG)
        log_loading_module(\$date, 'module '.\$module_name.'-custom chargé en version '.\$module_confs->version);");
                }
            }

            // partie core
            // création du répertoire
            mkdir("core/{$pathCore}");
            if ($autoloadCore) {
                file_put_contents("core/{$pathCore}/autoload.php", "<?php
                
    if(DEBUG)
        log_loading_module(\$date, 'module '.\$module_name.'-core chargé en version '.\$module_confs->version);");
            }

        } else {
            if (substr($path, 0, strlen('https://github.com/')) === 'https://github.com/') {
                exec("git clone {$path} custom/$pathCustom");
            }
        }
    }
}