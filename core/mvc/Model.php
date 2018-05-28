<?php

class Model implements Model_interface {

    private $is_assoc;

    public function __construct($is_assoc) {
        $this->is_assoc = $is_assoc;
    }

    function argv_is_assoc() {
        return $this->is_assoc;
    }
}