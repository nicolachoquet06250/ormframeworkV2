<?php

namespace ormframework\core\setup;

use \Exception;
use \ormframework\core\setup\interfaces\manager;
use \ormframework\core\managers\services_manager;
use \ormframework\core\managers\error_manager;

/**
 * Class global_manager
 *
 * @method \ormframework\core\managers\error_manager error_manager()
 * @method \ormframework\core\managers\services_manager service_manager()
 */
class global_manager implements manager
{

    private static $instence = null;

    private function __construct() {

    }

    public static function instence()
    {
        if(self::$instence === null) {
            self::$instence = new global_manager();
        }
        return self::$instence;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        if(is_file('core/setup/managers/'.$name.'_manager.php')) {
            $name_class = '\\ormframework\\core\\managers\\'.$name.'_manager';
            $manager = $name_class::instence($arguments);
            if($manager instanceof manager) {
                return $manager;
            }
            else {
                throw new Exception('La classe '.$name.' n\'est pas une classe d\'erreur');
            }
        }
        else {
            throw new Exception('Erreur '.$name.' inconnu');
        }
    }
}