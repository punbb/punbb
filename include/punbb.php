<?php
namespace punbb;

class PUNBB {

	private static $services;
	private static $values;

	static function service($name) {
		$f = self::getService($name);
		return is_callable($f)? call_user_func($f) : $f;
	}

	static function getService($name) {
		if (!isset(self::$services[$name])) {
			return null;
		}
		return self::$services[$name];
	}

	static function setService($name, $f) {
		return self::$services[$name] = $f;
	}

	static function get($name) {
		if (!isset(self::$values[$name])) {
			self::$values[$name] = new \stdClass();
		}
		return self::$values[$name];
	}

	static function set($name, $value) {
		return self::$values[$name] = $value;
	}

}

if (!defined('FORUM_ROOT')) {
	define('FORUM_ROOT', dirname(__DIR__) . '/');
	define('FORUM_WEB', '/');
}
