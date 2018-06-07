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
}