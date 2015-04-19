<?php
namespace punbb;

function flash() {
	return PUNBB::service('flash');
}

function db() {
	return PUNBB::service('db');
}

function config() {
	return PUNBB::service('config');
}

function user() {
	return PUNBB::service('user');
}

function assets() {
	return PUNBB::service('assets');
}

function template() {
	return PUNBB::service('template');
}

// configure

PUNBB::setService('db', function () {
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

PUNBB::setService('config', function () {
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

PUNBB::setService('flash', function () {
	global $_PUNBB;
	if (!isset($_PUNBB['flash'])) {
		$_PUNBB['flash'] = new FlashMessenger();
	}
	return $_PUNBB['flash'];
});

PUNBB::setService('assets', function () {
	// Create the loader adapter object
	return Loader::singleton();
});

PUNBB::setService('user', function () {
	global $_PUNBB;
	if (!isset($_PUNBB['user'])) {
		$_PUNBB['user'] = new \stdClass();
		// Login and fetch user info
		cookie_login($_PUNBB['user']);
	}
	return $_PUNBB['user'];
});

PUNBB::setService('template', function () {
	$f = PUNBB::getService('template');
	//if (!empty($f)) {
		//var_dump($f());
	//}
	//else {
		$template = new PhpTemplate();
	//}

	//var_dump($template);

	return PUNBB::set('template', $template);
});
