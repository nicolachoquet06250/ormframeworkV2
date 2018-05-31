<?php

class HelloWorld_model extends Model {

    /**
     * @description test de méthode d'un model
     * @method test
     * @param mixed $args
     * @param array $toto
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
     * @description test 3 de méthode d'un model de test
     * @method test3
     * @param array $args
     * @return Json_view
     *
     * @route HelloWorld/test3
     **/
    public function test3($args) {
        $entity = new entity_test();
        $entity->say($args);
        $entity->toto('test2');

        return new Json_view($entity);
    }
}