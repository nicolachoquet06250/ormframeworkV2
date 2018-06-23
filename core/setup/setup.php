<?php

namespace ormframework\core\setup;

use \Exception;
use \ormframework\custom\mvc\views\Json;

class setup extends utils
{
    /**
     * @var bool $is_assoc
     */
    private $is_assoc = false;

    /**
     * Setup constructor.
     * @param $argv
     */
    public function __construct($argv) {
        $argv = $this->stringToArray($argv);

        $max = count($argv["argv"])-1;
        if($max > -1) {
            if ($argv["argv"][$max] == '') {
                unset($argv["argv"][$max]);
            }
        }

        if(isset($argv["argv"][0]) && strstr($argv["argv"][0], '=')) {
            $argv['argv'] = $this->indexeToAssoc($argv["argv"]);
            $this->is_assoc = true;
        }
        $this->start($argv);
    }

    /**
     * @return bool
     */
    public function argv_is_assoc() {
        return $this->is_assoc;
    }

    /**
     * @param $argv
     * @return array
     */
    private function stringToArray($argv) {
        $tab = explode("/", $argv);
        $tmp = [
            "controller" => $tab[0],
            "method" => isset($tab[1])?$tab[1]:'',
            "argv" => []
        ];
        $tmp2 = [];
        $i = 0;
        foreach ($tab as $item => $value) {
            if ($item > 1) {
                $tmp2[$i] = $value;
                $i++;
            }
        }
        $tmp["argv"] = $tmp2;
        return $tmp;
    }

    /**
     * @param array $argv
     * @return array $tmp
     */
    private function indexeToAssoc(array $argv) {
        $tmp = [];
        foreach($argv as $i => $value) {
            $tab = explode('=', $value);
            $tmp[$tab[0]] = $tab[1];
        }
        return $tmp;
    }

    /**
     * @param $argv
     */
    private function start(array $argv) {
        if(file_exists("custom/mvc/controllers/{$argv['controller']}.php")) {
        	$namespace = '\\ormframework\\custom\\mvc\\controllers\\';
            $controllerName = $argv['controller'];
            $controller = $namespace.$controllerName;
            echo (new $controller($argv['method'], $argv['argv'], $this->argv_is_assoc()))
                ->response()->display();
        }
        else {
            try {
                ${404} = $this->get_manager('error')->error_404();
                ${404}->message = "Controller `{$argv['controller']}` not found";
                ${404}->header();
                echo (new Json(${404}))->display();
            }
            catch (Exception $e) {
                exit($e->getMessage());
            }
        }
    }
}