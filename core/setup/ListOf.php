<?php

namespace ormframework\core\setup;
use Exception;
use \ormframework\core\db_context\entity;
use sql_links\interfaces\IRequest;

class ListOf
{
    private $entitiesArray = [];
    private $request;
    private $autosave;
    private $entityName;
    public function __construct(string $entityName, IRequest $request = null, bool $autosave = false)
    {
        $this->entityName = $entityName;
        $this->request = $request;
        $this->autosave = $autosave;
    }

    public function __debugInfo()
    {
        return $this->entitiesArray;
    }

    /**
     * @param entity $entity
     * @param bool $db_save
     * @throws Exception
     */
    public function append(entity $entity, $db_save = true) {
        if(get_class($entity) === 'ormframework\\custom\\db_context\\'.$this->entityName) {
            $entity->autosave($this->autosave)->request($this->request);
            if($db_save) {
                $entity->add();
            }
            $this->entitiesArray[] = $entity;
        }
        else {
            throw new Exception('Ne sont admisent uniquement les entitées de type \''.$this->entityName.'\' dans cette liste. or, vous avez injecté une entité de type \''.explode('\\', get_class($entity))[count(explode('\\', get_class($entity)))-1].'\'');
        }
    }

    /**
     * @param string|integer|entity $entity
     * @param string $by
     */
    public function remove($entity, $by='') {
        if(gettype($entity) === 'integer' || gettype($entity) === 'string') {
            if($by !== '') {
                foreach ($this->entitiesArray as $id => $entity_local) {
                    if($entity_local->$by() === $entity) {
                        $this->entitiesArray[$id]->remove();
                        unset($this->entitiesArray[$id]);
                    }
                }
            }
            else {
                $this->entitiesArray[$entity]->remove();
                unset($this->entitiesArray[$entity]);
            }
        }
        elseif (gettype($entity) === 'object' && get_class($entity) === 'ormframework\\custom\\db_context\\'.$this->entityName) {
            foreach ($this->entitiesArray as $id => $entity_local) {
                foreach (array_keys($entity->get_not_null_props()) as $prop) {
                    if($entity_local->$prop() === $entity->$prop()) {
                        $this->entitiesArray[$id]->remove();
                        unset($this->entitiesArray[$id]);
                    }
                }
            }
            return;
        }
        $tmp = [];
        foreach ($this->entitiesArray as $entity) {
            $tmp[] = $entity;
        }
        $this->entitiesArray = $tmp;
    }

    /**
     * @param integer $id
     * @return entity
     * @throws Exception
     */
    public function get($id = null) {
        if($id) {
            if (isset($this->entitiesArray[$id])) {
                return $this->entitiesArray[$id];
            } else {
                throw new Exception("{$id} out of range");
            }
        }
        return $this->entitiesArray;
    }
}