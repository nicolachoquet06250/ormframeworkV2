<?php

class Json_view extends view {

    public function content_type() {
        return 'application/json';
    }

    public function display()
    {
        return json_encode($this->data);
    }
}