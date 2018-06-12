<?php

namespace ormframework\core\commands\interfaces;

interface commande_interface
{
    public function __call($name, $arguments);
}