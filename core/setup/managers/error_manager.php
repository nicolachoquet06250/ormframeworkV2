<?php
/**
 * Class error_manager
 *
 * @method http_error error_404()
 * @method http_error error_500()
 * @method http_error http_error()
 * @method http_error code_200()
 */

class error_manager extends core_utils implements manager
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