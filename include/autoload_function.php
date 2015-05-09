<?php
namespace punbb;

class fn {

	//private static $path;

  static function __callStatic($f, $args) {
  	$fn = 'punbb\\' . $f;
    if (!function_exists($fn)) {
			require FORUM_ROOT . 'include/functions/' . $f . '.php';
    }
    return call_user_func_array($fn, $args);
  }
}
