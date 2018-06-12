<?php

namespace ormframework\custom\mvc\models;

use \ormframework\custom\db_context\entity_test;
use \ormframework\custom\mvc\views\Json_view;
use \ormframework\custom\setup\utils;


class HelloWorld_model extends \ormframework\core\mvc\Model {

    private $my_utils;
    public function __construct($is_assoc)
    {
        parent::__construct($is_assoc);
        $this->my_utils = new utils();
    }

    /**
	 * @model HelloWorld
     * @description test de méthode d'un model
     * @method test
     * @param mixed $args
     * @return Json_view
     * @route index
     **/
    public function test($args) {
        $entity = new entity_test();
        $entity->say($args);
        $entity->toto('test');

        return new Json_view($entity);
    }

    /**
	 * @model HelloWorld
     * @description test 2 de méthode d'un model de test
     * @method test2
     * @param array $args
     * @return Json_view
     * @route toto/lol
     **/
	public function test2($args) {
		$entity = new entity_test();
		$entity->say($args);
		$entity->toto('test2');

		return new Json_view($entity);
	}

    /**
	 * @model HelloWorld
     * @description test 3 de méthode d'un model de test
     * @method test3
     * @param array $args
     * @return Json_view
     * @route HelloWorld/test3
     **/
    public function test3($args) {
        $entity = new entity_test();
        $entity->say($args);
        $entity->toto($this->my_utils->helloWorld());

        return new Json_view($entity);
    }
}