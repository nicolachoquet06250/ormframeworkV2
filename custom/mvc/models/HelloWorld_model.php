<?php

class HelloWorld_model extends Model {

	/**
	 * @description test de méthode d'un model
	 * @method test
	 * @param array $args
	 * @return Json_view
	 *
	 * @route index.php
	 **/
    public function test($args) {
        $entity = new entity_test();
        $entity->say($args);
        $entity->toto('test');

        return new Json_view($entity);
    }

	/**
	 * @description test 2 de méthode d'un model
	 * @method test2
	 * @param array $args
	 * @return Json_view
	 *
	 * @route toto.php
	 **/
	public function test2($args) {
		$entity = new entity_test();
		$entity->say($args);
		$entity->toto('test2');

		return new Json_view($entity);
	}
}