<?php

class command implements commande_interface
{

    private static $instence;

    /**
     * Commande constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $args = func_get_arg(0);
        var_dump($args);
        if(count($args) > 1) {
            $args = implode('|', $args);
            $args = explode('|do|', $args);
            $args[0] = str_replace('|', '_', $args[0]);
            $args_tmp = $args[1];
            $args_tmp = explode('|-p|', $args_tmp);
            $args[1] = $args_tmp[0];
            $args[2] = explode('|', $args_tmp[1]);
        }
        $class = (empty($args)
            || $args[0] == ''
            || $args[0] == "-h"
            || $args[0] == "--help") ? 'help' : $args[0];
        unset($args[0]);
        unset($args[1]);
        $i = 0;
        $array = [];
        foreach ($args as $arg) {
            $array[$i] = $arg;
            $i++;
        }
        $args = $array;

        $class_path = false;
        if (is_file('custom/commands/' . $class . '.php')) {
            $class_path = 'custom/commands/' . $class . '.php';
        } elseif (is_file('core/commands/' . $class . '.php')) {
            $class_path = 'core/commands/' . $class . '.php';
        }

        if ($class_path) {
            require_once $class_path;
            if (class_exists($class)) {
                /**
                 * @var command $command
                 */
                $command = new $class($args);
                var_dump($class);
                if($command instanceof command) {
                    var_dump(get_class_methods($command));
                    //if(get_class_methods($command))
                    $command->exec();
                }
                else {
                    throw new Exception("Class {$class} is not a command");
                }
            } else {
                throw new Exception("Command {$class} not found !");
            }
        }
        else {
            throw new Exception("Command {$class} not found !");
        }
    }

    public function exec()
    {
    }

    /**
     * @param $args
     * @return array
     */
    public static function rm_file_name_of_args($args)
    {
        unset($args[0]);
        $i = 0;
        $array = [];
        foreach ($args as $arg) {
            $array[$i] = $arg;
            $i++;
        }
        return $array;
    }

    public static function autoload()
    {
        require_once 'core/commands/autoload.php';
        require_once 'custom/commands/autoload.php';
    }

    /**
     * @return command
     * @throws Exception
     */
    public static function instence()
    {
        if (self::$instence == null) {
            self::$instence = new command(func_get_arg(0));
        }
        return self::$instence;
    }
}