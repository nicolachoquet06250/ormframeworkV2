<?php

namespace ormframework\core\db_context;

use ormframework\core\setup\utils;
use sql_links\interfaces\IRequest;

/**
 * Class entity
 * @package ormframework\core\db_context
 */
class entity extends utils
{
    protected $request, $autosave;
    public function __construct(IRequest $request = null, $autosave = false, $fields = [])
    {
        $this->before_construct();
        $this->request($request);
        $this->autosave($autosave);
        foreach ($fields as $champ => $value) {
            if(in_array($champ, array_keys($this->get_props()))) {
                $this->$champ = $value;
            }
        }
        $this->after_construct();
    }

    public function __call($name, $arguments = [])
    {
        if(count($arguments) > 0 && $arguments[0] !== null) {
            $this->$name = gettype($arguments[0]) === 'object' && gettype($arguments[0]) === 'Closure' ?
                $arguments[0]((isset($arguments[1]) ? $arguments[1] : $this))
                    : $arguments[0];
            if($this->autosave) {
                $this->save();
            }
            return $this;
        }
        return $this->$name;
    }

    protected function before_construct() {}
    protected function after_construct() {}

    /**
     * @return array
     */
    public function get_props() {
        $this_vars = get_object_vars($this);
        $entity_vars = get_object_vars(new entity());
        foreach ($entity_vars as $entity_var => $entity_var_val) {
            unset($this_vars[$entity_var]);
        }
        return $this_vars;
    }

    /**
     * @return array
     */
    public function get_not_null_props() {
        $props = $this->get_props();
        $tmp = [];
        foreach ($props as $prop => $value) {
            if($value !== null) {
                $tmp[$prop] = $value;
            }
        }
        return $tmp;
    }

    /**
     * @return \stdClass
     */
    public function get_for_view() {
        $obj = $this;
        unset($obj->request);
        unset($obj->autosave);
        $tmp = new \stdClass();
        foreach (get_object_vars($obj) as $name => $value) {
            $tmp->$name = $value;
        }
        return $tmp;
    }

    /**
     * @param bool|entity|null $autosave
     * @return $this
     */
    public function autosave(bool $autosave = null) {
        if($autosave === null) {
            return $this->autosave;
        }
        $this->autosave = $autosave;
        return $this;
    }

    /**
     * @param IRequest|entity|null $request
     * @return $this
     */
    public function request(IRequest $request = null) {
        if($request === null) {
            return $this->request;
        }
        $this->request = $request;
        return $this;
    }

    public function name() {
        return explode('\\', get_class($this))[count(explode('\\', get_class($this)))-1];
    }

    public function add() {
        if($this->request) {
            $this_vars = $this->get_props();
            $table = explode('\\', get_class($this))[count(explode('\\', get_class($this)))-1];

            $this->request->insert()->from($table)->values([$this_vars])->query();
        }
    }

    public function remove() {
        if($this->request) {
            $this_vars = $this->get_not_null_props();
            $table = explode('\\', get_class($this))[count(explode('\\', get_class($this)))-1];

            $this->request->delete()->from($table)->where($this_vars)->query();
        }
    }

    public function save() {
        if($this->request) {
            $this_vars = $this->get_props();
            $id = ['id' => $this->id()];
            unset($this_vars['id']);
            $table = explode('\\', get_class($this))[count(explode('\\', get_class($this)))-1];

            $this->request->update($table)->set($this_vars)->where($id)->query();
        }
    }
}