<?php

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

            $commands_conf = json_decode(file_get_contents('core/commands/enable_commands.json'), true);
            if (isset($commands_conf[$commandName])) {
                unset($commands_conf[$commandName]);
                file_put_contents('core/commands/enable_commands.json', json_encode($commands_conf));
            }
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

            $commands_conf = json_decode(file_get_contents('core/commands/enable_commands.json'), true);
            foreach ($commands_conf[$from_command] as $id => $item) {
                if ($item['method'] === $name) {
                    unset($commands_conf[$from_command][$id]);
                    break;
                }
            }
            file_put_contents('core/commands/enable_commands.json', json_encode($commands_conf));
        } else {
            throw new Exception("La methode {$name} n'existe pas dans la commande {$from_command}");
        }
    }
}