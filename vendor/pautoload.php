<?php
namespace punbb;

// TMP autoloader wrapper

// TMP for debuging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/.dev/errors.log');

if (!defined('FORUM_ROOT')) {
	define('FORUM_ROOT', dirname(__DIR__) . '/');
	define('FORUM_WEB', '/');

	require __DIR__ . '/autoload.php';
}
