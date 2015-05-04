<?php
namespace punbb;

function flash() {
	return PUNBB::get('flash');
}

function db() {
	return PUNBB::get('db');
}

function config() {
	return PUNBB::get('config');
}

function user() {
	return PUNBB::get('user');
}

function assets() {
	return PUNBB::get('assets');
}

function template() {
	return PUNBB::get('template');
}

function theme() {
	return PUNBB::get('theme');
}

function language() {
	return PUNBB::get('language');
}

function translations() {
	return PUNBB::get('translations');
}

function rewrite() {
	return PUNBB::get('rewrite');
}

// configure

PUNBB::set('config', function () {
	// Load cached config
	if (file_exists(FORUM_CACHE_DIR . 'cache_config.php')) {
		$config = (object)include FORUM_CACHE_DIR . 'cache_config.php';
	}
	if (empty($config)) {
		require FORUM_ROOT . 'include/cache.php';
		generate_config_cache();
		$config = (object)include FORUM_CACHE_DIR . 'cache_config.php';
	}
	return $config;
});

PUNBB::set('flash', function () {
	return new FlashMessenger();
});

PUNBB::set('assets', function () {
	return Loader::singleton();
});

PUNBB::set('user', function () {
	// TODO fix
	global $_PUNBB;
	if (!isset($_PUNBB['user'])) {
		$_PUNBB['user'] = new \stdClass();
		// Login and fetch user info
		cookie_login($_PUNBB['user']);
	}
	return $_PUNBB['user'];
});

PUNBB::set('translations', function () {
	return new \stdClass();
});
