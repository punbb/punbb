<?php
namespace punbb;

function service($name, $serv = null) {
	global $_PUNBB;

	// get service
	if ($serv === null) {
		$v = $_PUNBB['service'][$name];
		return is_callable($v)? call_user_func($v) : $v;
	}

	// set service
	if (!isset($_PUNBB['service'][$name])) {
		$_PUNBB['service'][$name] = $serv;
	}

	return $_PUNBB['service'][$name]; // remove this?
}

function flash($serv = null) {
	return service('flash', $serv);
}

function db($serv = null) {
	return service('db', $serv);
}

function config($serv = null) {
	return service('config', $serv);
}

function user($serv = null) {
	return service('user', $serv);
}

function assets($serv = null) {
	return service('assets', $serv);
}

// configure

db(function () {
	global $_PUNBB;
	// TODO fix
	global $db_type;
	global $db_host, $db_username, $db_password, $db_name, $db_prefix, $p_connect;

	if (!isset($_PUNBB['db'])) {
		// Create the database adapter object (and open/connect to/select db)
		$classname = 'punbb\\DBLayer_' . $db_type;
		$_PUNBB['db'] = new $classname(
			$db_host,
			$db_username,
			$db_password,
			$db_name,
			$db_prefix,
			$p_connect);
	}

	return $_PUNBB['db'];
});

config(function () {
	global $_PUNBB;

	if (isset($_PUNBB['config'])) {
		return $_PUNBB['config'];
	}

	// Load cached config
	if (file_exists(FORUM_CACHE_DIR . 'cache_config.php')) {
		$_PUNBB['config'] = (object)include FORUM_CACHE_DIR . 'cache_config.php';
	}

	if (empty($_PUNBB['config'])) {
		require FORUM_ROOT . 'include/cache.php';
		generate_config_cache();
		$_PUNBB['config'] = (object)include FORUM_CACHE_DIR . 'cache_config.php';
	}

	return $_PUNBB['config'];
});

flash(function () {
	global $_PUNBB;
	if (!isset($_PUNBB['flash'])) {
		$_PUNBB['flash'] = new FlashMessenger();
	}
	return $_PUNBB['flash'];
});

assets(function () {
	// Create the loader adapter object
	return Loader::singleton();
});

user(function () {
	global $_PUNBB;

	if (!isset($_PUNBB['user'])) {
		$_PUNBB['user'] = new \stdClass();
		// Login and fetch user info
		cookie_login($_PUNBB['user']);
	}
	return $_PUNBB['user'];
});
