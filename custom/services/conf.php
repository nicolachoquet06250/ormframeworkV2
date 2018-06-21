<?php

namespace ormframework\custom\services;

use \ormframework\core\services\interfaces\service;
use \ormframework\core\setup\utils;

class conf extends utils implements service
{

	private $utils;

	public function __construct() {
		$this->utils = new \ormframework\custom\setup\utils();
	}

	/**
	 * @param $name
	 * @param $arguments
	 * @return mixed
	 */
	public function __call($name, $arguments)
    {
        return $this->$name($arguments);
    }

	/**
	 * renvoie la conf des modules filtré ou pas par type de module ( core/custom )
	 *
	 * @param string $type
	 * @return object
	 */
    public function get_modules_conf($type='all')
    {

        $conf_core = (object)json_decode(file_get_contents('core/ormf-modules-conf.json'));
        $conf_custom = (object)json_decode(file_get_contents('custom/ormf-modules-conf.json'));

        if($type === 'all') {
            $conf = $conf_core;
            foreach ($conf_custom as $cnf => $value_cnf) {
                if (gettype($value_cnf) === 'object') {
                    foreach ($value_cnf as $sous_cnf => $sous_value_cnf) {
                        foreach ($sous_value_cnf as $sous_sous_cnf => $sous_sous_value_cnf) {
                            if ($sous_sous_cnf === 'location') {
                                $conf->$cnf->$sous_cnf->$sous_sous_cnf = ['core' => 'core/' . $conf->$cnf->$sous_cnf->$sous_sous_cnf];

                                if ($sous_sous_value_cnf) {
                                    $conf->$cnf->$sous_cnf->$sous_sous_cnf['custom'] = 'custom/' . $sous_sous_value_cnf;
                                }

                            } elseif ($sous_sous_cnf === 'autoload') {
                                if ($conf->$cnf->$sous_cnf->$sous_sous_cnf !== $sous_sous_value_cnf) {
                                    $conf->$cnf->$sous_cnf->$sous_sous_cnf = ['core' => $conf->$cnf->$sous_cnf->$sous_sous_cnf, 'custom' => $sous_sous_value_cnf];
                                }

                            }
                        }

                    }
                } else {
                    $conf->$cnf = $value_cnf;
                }
            }
        }
        else {
            $conf = ${'conf_'.$type};
        }
        return $conf;
    }

	/**
	 * lié à `add_module()`
	 *
	 * @param string $type
	 * @param string $name
	 * @param null|array   $module
	 */
    private function set_module_conf(string $type, string $name, $module=null) {
        $conf = $this->get_modules_conf($type);
        if($module) {
            $conf->modules->$name = $module;
        }
        else {
            unset($conf->modules->$name);
        }
        file_put_contents($type.'/ormf-modules-conf.json', json_encode($conf));
    }

	/**
	 * ajoute un module dans le code et dans la conf
	 *
	 * @param string $type
	 * @param string $name
	 * @param null|array   $module
	 */
    public function add_module(string $type, string $name, array $module) {
        $this->set_module_conf($type, $name, $module);
    }

	/**
	 * supprime un module dans le code et dans la conf
	 *
	 * @param string $name
	 * @return array
	 */
    public function remove_module(string $name) {
        $this->set_module_conf('core', $name);
        $this->set_module_conf('custom', $name);
        return [
        	'core' => $this->get_modules_conf('core'),
			'custom' => $this->get_modules_conf('custom')
		];
    }

    public function update_conf($name, $conf, $type = 'core') {
        $old_conf = $this->get_modules_conf($type);
        $old_conf->$name = $conf;
        $new_conf = $old_conf;

        file_put_contents('core/ormf-modules-conf.json', json_encode($new_conf));
    }

	/**
	 * renvoie la conf sql avec ou sans filtre par type de base
	 *
	 * @param string $type
	 * @return array
	 */
    public function get_sql_conf($type = 'all')
    {
		if($type === 'all') {
			return (array)json_decode(file_get_contents('custom/ormf-sql-conf.json'));
		}
		if(isset(json_decode(file_get_contents('custom/ormf-sql-conf.json'))->$type))
			return (array)json_decode(file_get_contents('custom/ormf-sql-conf.json'))->$type;
		return null;
    }

	/**
	 * lié à `add_sql_conf()`
	 *
	 * @param $type
	 * @param $alias
	 * @param $new_conf
	 */
    private function set_sql_conf($type, $alias, $new_conf) {
    	$conf = $this->get_sql_conf();
    	$conf[$type]->$alias = $new_conf;
    	file_put_contents('custom/ormf-sql-conf.json', json_encode($conf));
	}

	/**
	 * ajoute une conf sql
	 *
	 * @param $alias
	 * @param $new_conf
	 */
    public function add_sql_conf($alias, $new_conf) {
    	if($conf = $this->get_sql_conf($new_conf->bdd_type)) {
    		if(!isset($conf[$alias])) {
    			$bdd_type = $new_conf->bdd_type;
    			unset($new_conf->bdd_type);
    			$conf[$alias] = $new_conf;
    			$this->set_sql_conf($bdd_type, $alias, $new_conf);
			}
		}
	}

	/**
	 * supprime une conf sql en fonction de son type et de son alias
	 *
	 * @param $alias
	 * @param $bdd_type
	 */
	public function remove_sql_conf($alias, $bdd_type) {
		if($conf = $this->get_sql_conf()) {
			if(isset($conf[$bdd_type]->$alias)) {
				unset($conf[$bdd_type]->$alias);
				file_put_contents('custom/ormf-sql-conf.json', json_encode($conf));
			}
		}
	}

	public function update_sql_conf($alias, $new_conf) {
		if($conf = $this->get_sql_conf($new_conf->bdd_type)) {
			if(isset($conf[$alias])) {
				$bdd_type = $new_conf->bdd_type;
				unset($new_conf->bdd_type);
				foreach ($new_conf as $key => $value) {
					if(isset($conf[$alias]->$key)) {
						$conf[$alias]->$key = $value;
					}
				}

				$global_conf = $this->get_sql_conf();
				$global_conf[$bdd_type] = $conf;

				file_put_contents('custom/ormf-sql-conf.json', json_encode($global_conf));
			}
		}
	}

	/**
	 * renvoie le tableau de routes définies en dur
	 *
	 * @return array
	 */
    public function get_router()
    {
        return (array)json_decode(file_get_contents('custom/router.json'));
    }


}