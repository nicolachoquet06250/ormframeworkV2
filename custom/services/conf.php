<?php

class conf extends utils implements service
{

    public function __call($name, $arguments)
    {
        return $this->$name($arguments);
    }

    public function get_modules_conf()
    {

        $conf_core = json_decode(file_get_contents('core/ormf-modules-conf.json'));
        $conf_custom = json_decode(file_get_contents('custom/ormf-modules-conf.json'));
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

        return $conf;
    }

    public function get_sql_conf()
    {
        return json_decode(file_get_contents('custom/ormf-sql-conf.json'));
    }

    public function get_router()
    {
        return json_decode(file_get_contents('custom/router.json'));
    }


}