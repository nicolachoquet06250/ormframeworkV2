<?php

class command implements commande_interface
{

    private static $instence;

    /**
     * Commande constructor.
     * @param $args
     * @throws Exception
     */
    public function __construct($args = [])
    {
        if(count($args) > 1) {
            $args = implode('|', $args);
            $args = explode('|do|', $args);
            $args[0] = str_replace('|', '_', $args[0]);
            $args_tmp = $args[1];
            $args_tmp = explode('|-p|', $args_tmp);
            $args[1] = str_replace('|', '_', $args_tmp[0]);
            $args[2] = explode('|', $args_tmp[1]);
            $args['class'] = $args[0];
            $args['method'] = $args[1];
            $args['args'] = $args[2];
            unset($args[0]);
            unset($args[1]);
            unset($args[2]);
        }

        $class = (empty($args)
            && ( $args[0] == ''
            || $args[0] == "-h"
            || $args[0] == "--help")) ? 'help' : $args['class'];

        $class_path = false;
        if (is_file('custom/commands/' . $class . '.php')) {
            $class_path = 'custom/commands/' . $class . '.php';
        } elseif (is_file('core/commands/' . $class . '.php')) {
            $class_path = 'core/commands/' . $class . '.php';
        }

        require_once $class_path;
        if (class_exists($class)) {
            /**
             * @var command $command
             */
            $command = new $class($args);
            if($command instanceof command) {
                if(isset($args['method'])) {
                    if(in_array($args['method'], get_class_methods($command))) {
                        $method = $args['method'];
                    }
                    else {
                        $method = 'exec';
                    }
                }
                else {
                    $method = 'exec';
                }

                if(isset($args['args'])) {
                    $argv = $args['args'];
                }
                else {
                    $argv = [];
                }

                $command->$method($argv);
            }
            else {
                throw new Exception("Class {$class} is not a command");
            }
        } else {
            throw new Exception("Command {$class} not found !");
        }
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws Exception
     */
    public function __call($name, $arguments) {
        if(in_array($name, get_class_methods(get_class($this)))) {
            return $this->$name($arguments);
        }
        throw new Exception('method `'.$name.'` not found');
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