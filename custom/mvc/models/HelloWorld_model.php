<?php

class HelloWorld_model extends Model {
    public function test($args) {
        $entity = new entity_test();
        $entity->say($args);
        $entity->toto('test');

        return new Json_view($entity);
    }
}