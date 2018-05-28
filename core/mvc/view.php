<?php

class view implements view_interface
{
    protected $data;

    public function __construct($data) {
        $this->data = $data;
        header("Content-Type: {$this->content_type()}");
    }

    public function content_type() {
        return 'plain/text';
    }

    public function to_object() {
        if(!gettype($this->data) === 'object') {
            $object = new stdClass();
            $object->attr = $this->data;
        }
        else {
            $object = $this->data;
        }
        return $object;
    }

    public function display() {}

}