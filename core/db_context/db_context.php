<?php

namespace ormframework\core\db_context;


use Exception;
use ormframework\core\setup\utils;
use sql_links\factories\Request;
use sql_links\factories\RequestConnexion;
use stdClass;

class db_context extends utils
{
    private $bdd_type, $cnx_array;
    public function __construct($bdd_type, $cnx_array)
    {
        $this->bdd_type = $bdd_type;
        $this->cnx_array = $cnx_array;
    }

    /**
     * @throws Exception
     */
    public function genere_sql_db() {
        $method_name = __FUNCTION__.'_'.$this->bdd_type;
        $this->$method_name();
    }

    /**
     * @param callable $callback
     * @throws Exception
     */
    private function get_entities(callable $callback) {
        $dir = opendir('./'.$this->get_manager('services')->conf()->get_modules_conf()->modules->dbcontext->location['custom']);
        $cnx = new RequestConnexion($this->cnx_array, $this->bdd_type);
        $db = Request::getIRequest($cnx, $this->bdd_type);
        while (($file = readdir($dir)) !== false) {
            if ($file !== '.' && $file !== '..' && $file !== 'autoload.php') {
                $infos = new stdClass();
                $entity = '\\ormframework\\custom\\db_context\\'.explode('.', $file)[0];
                $infos->cnx = $cnx;
                $infos->db = $db;
                $infos->entity = new $entity($db);
                $infos->bdd_format_class = '\\sql_links\\requests\\'.ucfirst($this->bdd_type);
                $infos->props_bdd_format = [];
                foreach ($infos->entity->get_props() as $prop => $value) {
                    $infos->props_bdd_format[$prop] = [
                        'type' => ($prop === 'id' ? 'INT' : 'TEXT')
                    ];
                    if($value !== null) {
                        $infos->props_bdd_format[$prop]['default'] = $value;
                    }
                    if($prop === 'id') {
                        $infos->props_bdd_format[$prop]['key'] = 'primary';
                        $infos->props_bdd_format[$prop]['increment'] = 'auto';
                    }
                }

                $callback($infos);
            }
        }
    }

    /**
     * @throws Exception
     */
    public function genere_sql_db_json() {
        $this->get_entities(function ($infos) {
            $infos->db->create($infos->bdd_format_class::TABLE, $infos->entity->name())->set($infos->props_bdd_format)->query();
        });
    }

    /**
     * @throws Exception
     */
    public function genere_sql_db_mysql() {
        $this->get_entities(function ($infos) {
            $infos->db->create($infos->bdd_format_class::TABLE, $infos->entity->name())->set($infos->props_bdd_format)->query();
        });
    }
}