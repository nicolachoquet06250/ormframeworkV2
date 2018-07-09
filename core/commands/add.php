<?php
namespace ormframework\core\commands;

use \Exception;

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
        $this->get_manager('command')->add_command($commandName);
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

                $this->get_manager('command')->add_method($to_command, $name, 2, ['var1', 'var2']);
            } else {
                throw new Exception("La methode {$name} existe déja dans la commande {$to_command}");
            }
        } else {
            throw new Exception("La commande {$to_command} n'existe pas");
        }
    }

	/**
	 * @throws Exception
	 */
	public function module()
    {

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

        function parcour_dir($directory, $module_path, &$body)
        {

            $dir = opendir($directory);
            while (($file = readdir($dir)) !== false) {
                if ($file !== '.' && $file !== '..'
                    && substr($file, 0, 1) !== '.'
                    && !strstr(strtolower($file), 'demo')
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

        $moduleName = $this->get_from_name('module_name') ? $this->get_from_name('module_name') : $this->argv[1];
        $autoloadCustom = $this->get_from_name('custom_autoload') ? $this->get_from_name('custom_autoload') : true;
        $autoloadCustom = ($autoloadCustom === 'false') ? false : true;
        $autoloadCore = $this->get_from_name('core_autoload') ? $this->get_from_name('core_autoload') : true;
        $autoloadCore = ($autoloadCore === 'false') ? false : true;
        $pathCustom = $this->get_from_name('custom_path') ? $this->get_from_name('custom_path') : $moduleName;
        $pathCore = $this->get_from_name('core_path') ? $this->get_from_name('core_path') : $moduleName;
        $moduleVersion = $this->get_from_name('version') ? $this->get_from_name('version') : '0.0.1';

        $moduleCore = [
            "version" => $moduleVersion,
            "location" => $pathCore,
            "enable" => true,
            "autoload" => $autoloadCore
        ];

        $moduleCustom = [
            "version" => $moduleVersion,
            "autoload" => $autoloadCustom,
            "location" => $pathCustom
        ];

        if(isset($this->get_manager('services')->conf()->get_modules_conf()->modules->$moduleName)) {
            throw new Exception("Le module {$moduleName} exite déja");
        }
        $path = $this->get_from_name('path') ? $this->get_from_name('path') : $this->argv[0];
        if (is_dir($path = ($this->get_from_name('path') ? $this->get_from_name('path') : $this->argv[0]))) {
            // partie custom
            // copie de la lib dans un module
            copy_directory("$path", "custom/{$pathCustom}");
            if ($autoloadCustom) {
                if (!is_file("custom/{$pathCustom}/autoload.php")) {
                    $start = "<?php\n
            namespace ormframework;\n";
                    $end = "if(DEBUG)
            Loading::log_loading_module(\$date, 'module '.\$module_name.'-custom chargé en version '.\$module_confs->version);";
                    $body = '';

                    parcour_dir("custom/{$pathCustom}", "custom/{$pathCustom}", $body);

                    file_put_contents("custom/{$pathCustom}/autoload.php", $start . $body . $end);
                }
            }
            $this->get_manager('services')->conf()->add_module('custom', $moduleName, $moduleCustom);

            // partie core
            // création du répertoire
            mkdir("core/{$pathCore}");
            if ($autoloadCore) {
                file_put_contents("core/{$pathCore}/autoload.php", "<?php
            namespace ormframework;\n
                    
        if(DEBUG)
            Loading::log_loading_module(\$date, 'module '.\$module_name.'-core chargé en version '.\$module_confs->version);");
            }
            $this->get_manager('services')->conf()->add_module('core', $moduleName, $moduleCore);

        } else {
            if (substr($path, 0, strlen('https://github.com/')) === 'https://github.com/') {
				mkdir("core/{$pathCore}");
				if ($autoloadCore) {
					file_put_contents("core/{$pathCore}/autoload.php", "<?php
            namespace ormframework;\n
                    
        if(DEBUG)
            Loading::log_loading_module(\$date, 'module '.\$module_name.'-core chargé en version '.\$module_confs->version);");
				}
				$moduleCore['repository'] = [
					'type' => 'git',
					'path' => $path
				];
				$this->get_manager('services')->conf()->add_module('core', $moduleName, $moduleCore);

				if(!is_dir("custom/{$pathCustom}")) {
					exec("git clone {$path} custom/$pathCustom");
					if (is_dir("custom/{$pathCustom}/.idea")) {
						rmdir_recursif("custom/{$pathCustom}/.idea");
						rmdir("custom/{$pathCustom}/.idea");
					}
					$ligne      = "\n    <mapping directory=\"\$PROJECT_DIR$/custom/{$pathCustom}\" vcs=\"Git\" />";
					$to_replace = "\n  </component>\n</project>";
					$vcs        = file_get_contents('./.idea/vcs.xml');
					$vcs        = str_replace($to_replace, $ligne, $vcs).$to_replace;

					file_put_contents('./.idea/vcs.xml', $vcs);

					if ($autoloadCustom) {
						if (!is_file("custom/{$pathCustom}/autoload.php")) {
							$start = "<?php\n
				namespace ormframework;\n";
							$end   = "if(DEBUG)
				Loading::log_loading_module(\$date, 'module '.\$module_name.'-custom chargé en version '.\$module_confs->version);";
							$body  = '';

							parcour_dir("custom/{$pathCustom}", "custom/$pathCustom", $body);

							file_put_contents("custom/{$pathCustom}/autoload.php", $start.$body.$end);
						}
					}
					$this->get_manager('services')->conf()->add_module('custom', $moduleName, $moduleCustom);
				}
				else {
					throw new Exception("Ce module existe déja.\nLancez la commande : php ormframework.php initialize do dependencies");
				}
            }
        }
    }

    public function test_commands_manager() {
	    $command = $this->get_from_name('command');
	    $method = $this->get_from_name('method');
        $cm = $this->get_manager('command');
        if($method) {
            $cm->delete_method($command, $method);
        }
        else {
            $cm->delete_command($command);
        }
    }
}