<?php

namespace ormframework\custom\mvc\views;

use ormframework\core\db_context\entity;

class Json extends \ormframework\core\mvc\view {

    public function content_type() {
        return 'application/json';
    }

    public function display()
    {
        if(gettype($this->data) === 'array' && $this->data[0] instanceof entity) {
            /**
             * @var entity $data
             */
            foreach ($this->data as $id => $data) {
                $this->data[$id] = $data->get_for_view();
            }
        }
        return json_encode($this->data);
    }
}