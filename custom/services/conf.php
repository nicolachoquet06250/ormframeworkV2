<?php

class conf extends core_utils implements service
{

    public function __call($name, $arguments)
    {
        return $this->$name($arguments);
    }

	/**
	 * @param string $type
	 * @return array
	 */
    public function get_modules_conf($type='all')
    {

        $conf_core = (array)json_decode(file_get_contents('core/ormf-modules-conf.json'));
        $conf_custom = (array)json_decode(file_get_contents('custom/ormf-modules-conf.json'));

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
	 * @param string $type
	 * @param string $name
	 * @param null|array   $module
	 */
    public function add_module(string $type, string $name, array $module) {
        $this->set_module_conf($type, $name, $module);
    }

	/**
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

	/**
	 * @return array
	 */
    public function get_sql_conf()
    {
        return (array)json_decode(file_get_contents('custom/ormf-sql-conf.json'));
    }

	/**
	 * @return array
	 */
    public function get_router()
    {
        return (array)json_decode(file_get_contents('custom/router.json'));
    }


}