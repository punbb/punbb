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
	// TODO fix
	global $db_type, $db_host, $db_username, $db_password, $db_name, $db_prefix, $p_connect;

	$db = PUNBB::get('db');
	if (empty((array)$db)) {
		// Create the database adapter object (and open/connect to/select db)
		$classname = 'punbb\\DBLayer_' . $db_type;
		$db = new $classname(
			$db_host,
			$db_username,
			$db_password,
			$db_name,
			$db_prefix,
			$p_connect);
	}
	return PUNBB::set('db', $db);
});

PUNBB::setService('config', function () {
	$config = PUNBB::get('config');
	if (!empty((array)$config)) {
		return $config;
	}
	// Load cached config
	if (file_exists(FORUM_CACHE_DIR . 'cache_config.php')) {
		$config = (object)include FORUM_CACHE_DIR . 'cache_config.php';
	}
	if (empty($config)) {
		require FORUM_ROOT . 'include/cache.php';
		generate_config_cache();
		$config = (object)include FORUM_CACHE_DIR . 'cache_config.php';
	}
	return PUNBB::set('config', $config);
});

PUNBB::setService('flash', function () {
	$flash = PUNBB::get('flash');
	if (empty((array)$flash)) {
		$flash = new FlashMessenger();
	}
	return PUNBB::set('flash', $flash);
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

// init default template engine
if (empty(PUNBB::getService('template'))) {
	PUNBB::setService('template', function () {
		$template = new PhpTemplate();
		return PUNBB::set('template', $template);
	});
}