<?php

namespace ormframework\core\mvc;

class Model extends \ormframework\core\setup\utils implements \ormframework\core\mvc\interfaces\Model_interface
{

    private $is_assoc;

    public function __construct($is_assoc) {
        $this->is_assoc = $is_assoc;
    }

    function argv_is_assoc() {
        return $this->is_assoc;
    }

    /**
     * @param $name
     * @param $args
     * @return mixed
     */
    public function get_from_name($name, $args)
    {
        return isset($args[$name]) ? $args[$name] : null;
    }
}