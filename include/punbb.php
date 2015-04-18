<?php
namespace punbb;

class PUNBB {

	private static $services;
	private static $values;

	static function service($name, $serv = null) {
		// get service
		if ($serv === null) {
			$v = self::$services[$name];
			return is_callable($v)? call_user_func($v) : $v;
		}

		// set service
		//if (!isset(self::$services[$name])) {
		self::$services[$name] = $serv;
		//}
		return self::$services[$name]; // remove this?
	}

	static function get($name) {
		return self::$values[$name];
	}

	static function set($name, $value) {
		return self::$values[$name] = $value;
	}

}
