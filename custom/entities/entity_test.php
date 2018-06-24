<?php

namespace ormframework\custom\db_context;

use \ormframework\core\db_context\entity;

/**
 * @method array|entity_test say(array $say = null)
 * @method string|entity_test toto(string $toto = null)
 **/
class entity_test extends entity
{
    protected $say;
    protected $toto;
}