<?php

namespace ormframework\core\managers;

use \Exception;
use \ormframework\core\errors\http_error;
/**
 * Class error_manager
 *
 * @method http_error error_404()
 * @method http_error error_500()
 * @method http_error http_error()
 * @method http_error code_200()
 */

class error_manager extends \ormframework\core\setup\utils implements \ormframework\core\setup\interfaces\manager
{

    protected static $instence = null;

    private function __construct() {

    }

    public static function instence() {
        if( self::$instence === null) {
            self::$instence = new error_manager();
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
        if(is_file('core/errors/'.$name.'.php')) {
        	$name = '\\ormframework\\core\\errors\\'.$name;
            $service = new $name($arguments);
            if($service instanceof http_error) {
                return $service;
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