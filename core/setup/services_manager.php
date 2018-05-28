<?php

class services_manager extends utils implements manager {

	protected static $instence = null;

	protected function __construct() {

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
		if(is_file('custom/services/'.$name.'.php') || is_file('core/services/'.$name.'.php')) {
			$service = new $name($arguments);
			if($service instanceof service) {
				return $service;
			}
			else {
				throw new Exception('La classe '.$name.' n\'est pas un service');
			}
		}
		else {
			throw new Exception('Service '.$name.' inconnu');
		}
	}
}