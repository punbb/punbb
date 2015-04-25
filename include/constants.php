<?php
namespace punbb;

// TMP for debuging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__DIR__) . '/.dev/errors.log');

if (!defined('FORUM_ROOT')) {
	define('FORUM_ROOT', dirname(__DIR__) . '/');
	define('FORUM_WEB', '/');
}

// Define the version and database revision that this code was written for
define('FORUM_VERSION', '1.4.3');
define('FORUM_DB_REVISION', 5);

// Define a few commonly used constants
define('FORUM_UNVERIFIED', 0);
define('FORUM_ADMIN', 1);
define('FORUM_GUEST', 2);

// Define avatars type
define('FORUM_AVATAR_NONE', 0);
define('FORUM_AVATAR_GIF', 1);
define('FORUM_AVATAR_JPG', 2);
define('FORUM_AVATAR_PNG', 3);

define('FORUM_SUBJECT_MAXIMUM_LENGTH', 70);
define('FORUM_DATABASE_QUERY_MAXIMUM_LENGTH', 140000);

define('FORUM_SEARCH_MIN_WORD', 3);
define('FORUM_SEARCH_MAX_WORD', 20);

define('FORUM_PUN_EXTENSION_REPOSITORY_URL', 'http://punbb.informer.com/extensions/1.4');
