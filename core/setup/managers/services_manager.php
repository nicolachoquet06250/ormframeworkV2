<?php

namespace ormframework\core\managers;

use \Exception;
use \ormframework\core\services\interfaces\service;
use \ormframework\core\services\my_first_service;
use ormframework\custom\services\conf;

/**
 * Class services_manager
 *
 * @method my_first_service my_first_service()
 * @method conf				conf()
 */
class services_manager extends \ormframework\core\setup\utils implements \ormframework\core\setup\interfaces\manager {

	protected static $instence = null;

	private function __construct() {

	}

	public static function instence() {
		if( self::$instence === null) {
			self::$instence = new services_manager();
		}

		return self::$instence;
	}

	/**
	 * @param $name
	 * @param $arguments
	 * @return mixed
	 * @throws Exception
	 */
	public function __call($name, $arguments) {
		if(is_file("custom/services/{$name}.php") || is_file("core/services/{$name}.php")) {
			$path = is_file("custom/services/{$name}.php") ? "custom/services/{$name}.php" : "core/services/{$name}.php";
		    require_once 'core/services/interfaces/service.php';
			require_once $path;
			$classe = 'ormframework\\'.str_replace('/', '\\', str_replace(basename($path), '', $path)).$name;
			$service = new $classe($arguments);
			if($service instanceof service) {
				return $service;
			}
			else {
				throw new Exception("La classe {$name} n'est pas un service");
			}
		}
		else {
			throw new Exception("Service {$name} inconnu");
		}
	}
}