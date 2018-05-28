<?php

class entity
{
    public function __call($name, $arguments=null)
    {
        if(isset($this->$name) || $this->$name === null) {
            if($arguments !== null) {
                $this->$name = $arguments[0];
            }
            return $this->$name;
        }
        return null;
    }
}