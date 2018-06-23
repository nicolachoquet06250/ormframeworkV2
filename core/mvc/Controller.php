<?php

namespace ormframework\core\mvc;

use \Exception;
use \ormframework\custom\mvc\views\Json;
use ormframework\core\mvc\interfaces\Controller_interface;

class Controller extends \ormframework\core\setup\utils implements Controller_interface
{

    private $method, $args, $is_assoc;

    public function __construct($method, $args, $is_assoc=false)
    {
        $this->method = $method;
        $this->args = $args;
        $this->is_assoc = $is_assoc;
    }

	protected function is_assoc(): bool {
        return $this->is_assoc;
    }

    /**
     * @return view
     * @throws Exception
     */
    public function response(): view
    {
        $model = str_replace('controllers', 'models', get_class($this));
        $method = $this->method;

        if(get_class_methods($model)) {
			if (in_array($method, get_class_methods($model))) {
				return (new $model($this->is_assoc()))->$method($this->args);
			}
		}
        ${404} = $this->get_manager('error')->error_404();
        ${404}->message = "method `{$method}` not found in model `{$model}`";
        ${404}->header();
        throw new Exception((new Json(${404}))->display());
    }
}