<?php

namespace ormframework\core\mvc\interfaces;

interface view_interface
{
    public function __construct($data);
    public function display();
}