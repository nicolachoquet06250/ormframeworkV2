<?php

namespace ormframework\core\managers;


use ormframework\core\setup\interfaces\manager;
use ormframework\core\setup\utils;
use Exception;

/**
 * Class command_manager
 * @package ormframework\core\managers
 */
class command_manager extends utils implements manager
{
    private $path = 'core/commands/enable_commands.json';
    private $commands = null;
    private function __construct()
    {
        $this->commands = json_decode(file_get_contents($this->path));
    }

    private static $instence = null;
    public static function instence()
    {
        if(self::$instence === null) {
            self::$instence = new command_manager();
        }
        return self::$instence;
    }

    /**
     * @param $name
     * @param $arguments
     * @return $this
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        foreach (get_object_vars($this->commands) as $prop => $obj) {
            if($prop === $name) {
                if(!empty($arguments) && $arguments[0]) {
                    if(gettype($arguments[0]) === 'object'
                        && get_class($arguments[0]) === 'Closure') {
                        $arguments[1] = isset($arguments[1]) ? $arguments[1] : $this;
                        $this->commands->$name = $arguments[0]($arguments[1]);
                    }
                    else {
                        $this->commands->$name = $arguments[0];
                    }
                    return $this;
                }
                else {
                    return $this->commands->$name;
                }
            }
        }

        if(in_array($name, get_class_methods(__CLASS__))) {
            return $this->$name($arguments);
        }

        throw new Exception(__CLASS__."::{$name}() or ".__CLASS__."::\${$name} not found");
    }

    public function add_command(string $command) {
        $commands_conf = json_decode(file_get_contents($this->path), true);
        $commands_conf[$command] = [];
        file_put_contents($this->path, json_encode($commands_conf));
    }

    public function add_method(string $command, string $method, int $nbr_params = 0, array $keys = []) {
        $commands_conf = json_decode(file_get_contents($this->path), true);
        $commands_conf[$command][] = [];
        $commands_conf[$command][count((array)$commands_conf[$command]) - 1]['method'] = $method;
        $commands_conf[$command][count((array)$commands_conf[$command]) - 1]['args'] = $nbr_params;
        if(!empty($keys)) {
            $commands_conf[$command][count((array)$commands_conf[$command]) - 1]['keys'] = $keys;
        }
        file_put_contents($this->path, json_encode($commands_conf));
    }

    public function delete_command(string $command) {
        $commands_conf = json_decode(file_get_contents($this->path), true);
        if (isset($commands_conf[$command])) {
            unset($commands_conf[$command]);
            file_put_contents($this->path, json_encode($commands_conf));
        }
    }

    public function delete_method(string $command, string $method) {
        $commands_conf = json_decode(file_get_contents($this->path), true);
        foreach ($commands_conf[$command] as $id => $item) {
            if ($item['method'] === $method) {
                unset($commands_conf[$command][$id]);
                break;
            }
        }
        file_put_contents($this->path, json_encode($commands_conf));
    }
}