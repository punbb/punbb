<?php
namespace punbb;

function flash() {
	return service::get('flash');
}

function db() {
	return service::get('db');
}

function config() {
	return service::get('config');
}

function user() {
	return service::get('user');
}

function assets() {
	return service::get('assets');
}

function template() {
	return service::get('template');
}

function theme() {
	return service::get('theme');
}

function language() {
	return service::get('language');
}

function translations() {
	return service::get('translations');
}

function rewrite() {
	return service::get('rewrite');
}

function cache() {
	return service::get('cache');
}

// configure

service::set('cache', function () {
	return new Cache();
});

service::set('config', function () {
	// Load cached config
	$config = cache()->get('cache_config', 'punbb\\fn::generate_config_cache');
	return (object)$config;
});

service::set('flash', function () {
	return new FlashMessenger();
});

service::set('assets', function () {
	return Loader::singleton();
});

service::set('user', function () {
	// TODO fix
	global $_PUNBB;
	if (!isset($_PUNBB['user'])) {
		$_PUNBB['user'] = new \stdClass();
		// Login and fetch user info
		cookie_login($_PUNBB['user']);
	}
	return $_PUNBB['user'];
});

service::set('translations', function () {
	return new \stdClass();
});
