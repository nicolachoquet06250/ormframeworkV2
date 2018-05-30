<?php

interface commande_interface
{
    public function __call($name, $arguments);
}