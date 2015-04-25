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

	// include for admin area
	if (substr($_SERVER['SCRIPT_NAME'],
				1, strpos($_SERVER['SCRIPT_NAME'], '/', 1) - 1) == 'admin') {
		require FORUM_ROOT . 'include/common_admin.php';
	}
}
