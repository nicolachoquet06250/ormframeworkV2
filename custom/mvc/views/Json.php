<?php

namespace ormframework\custom\mvc\views;

class Json extends \ormframework\core\mvc\view {

    public function content_type() {
        return 'application/json';
    }

    public function display()
    {
        return json_encode($this->data);
    }
}