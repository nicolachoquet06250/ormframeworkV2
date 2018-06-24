<?php

namespace ormframework\core\commands;


use Exception;
use ormframework\core\db_context\db_context;

class migration extends command
{
    public function __construct(array $args = [])
    {
        $this->argv = $args;
    }

    /**
     * @throws Exception
     */
    public function database() {
        if(($bdd_type = $this->get_from_name('bdd_type')) && ($alias = $this->get_from_name('alias'))) {
            (new db_context($bdd_type, (array)$this->get_manager('services')->conf()->get_sql_conf($bdd_type)[$alias]))->genere_sql_db();
        }

    }
}