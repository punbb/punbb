<?php
namespace punbb;

class PUNBB {

	private static $services;
	private static $values;

	// set service value
	static function set($name, $f) {
		return self::$services[$name] = $f;
	}

	// get value
	static function get($name) {
		if (isset(self::$values[$name])) {
			return self::$values[$name];
		}
		$f = self::raw($name);
		return self::$values[$name] = is_callable($f)? call_user_func($f) : $f;
	}

	// get service
	static function raw($name) {
		if (!isset(self::$services[$name])) {
			return null;
		}
		return self::$services[$name];
	}

}

if (!defined('FORUM_ROOT')) {
	define('FORUM_ROOT', dirname(__DIR__) . '/');
	define('FORUM_WEB', '/');
}
