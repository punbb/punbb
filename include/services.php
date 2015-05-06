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

function cache() {
	return PUNBB::get('cache');
}

// configure

PUNBB::set('cache', function () {
	return new Cache();
});

PUNBB::set('config', function () {
	// Load cached config
	$config = cache()->get('cache_config', 'punbb\\fn::generate_config_cache');
	return (object)$config;
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
