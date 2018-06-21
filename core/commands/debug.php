<?php

namespace ormframework\core\commands;


class debug extends command
{
    public function __construct(array $args = [])
    {
        $this->argv = $args;
    }

    public function enable() {
        $this->get_manager('services')->conf()->update_conf('debug', true);
    }

    public function disable() {
        $this->get_manager('services')->conf()->update_conf('debug', false);
    }
}