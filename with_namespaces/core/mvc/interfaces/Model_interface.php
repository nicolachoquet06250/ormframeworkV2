<?php

namespace ormframework\core\mvc\interfaces;

interface Model_interface {

    public function __construct($is_assoc);

    function argv_is_assoc();

}