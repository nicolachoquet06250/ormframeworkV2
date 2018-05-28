<?php

class Controller implements Controller_interface {

    private $method, $args, $is_assoc;

    public function __construct($method, $args, $is_assoc=false)
    {
        $this->method = $method;
        $this->args = $args;
        $this->is_assoc = $is_assoc;
    }

    protected function is_assoc():bool {
        return $this->is_assoc;
    }

    public function response(): view
    {
        $model = str_replace('_controller', '_model', get_class($this));
        $method = $this->method;
        return (new $model($this->is_assoc()))->$method($this->args);
    }
}