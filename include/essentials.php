<?php
/**
 * Loads the minimum amount of data (eg: functions, database connection, config data, etc) necessary to integrate the site.
 *
 * @copyright (C) 2008-2011 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */

if (!defined('FORUM_ROOT'))
	exit('The constant FORUM_ROOT must be defined and point to a valid PunBB installation root directory.');

// Define the version and database revision that this code was written for
define('FORUM_VERSION', '1.4RC1');
define('FORUM_DB_REVISION', 5);

// Record the start time (will be used to calculate the generation time for the page)
list($usec, $sec) = explode(' ', microtime());
$forum_start = ((float)$usec + (float)$sec);

// Load the functions script
require FORUM_ROOT.'include/functions.php';
// Load the Loader class
require FORUM_ROOT.'include/loader.php';
// Load the Flash messenger class
require FORUM_ROOT.'include/flash_messenger.php';

// Load UTF-8 functions
require FORUM_ROOT.'include/utf8/utf8.php';
require FORUM_ROOT.'include/utf8/ucwords.php';
require FORUM_ROOT.'include/utf8/trim.php';

// Reverse the effect of register_globals
forum_unregister_globals();

// Ignore any user abort requests
ignore_user_abort(true);

// Attempt to load the configuration file config.php
if (file_exists(FORUM_ROOT.'config.php'))
	include FORUM_ROOT.'config.php';

// If we have the 1.2 constant defined, define the proper 1.3 constant so we don't get
// an incorrect "need to install" message
if (defined('PUN'))
	define('FORUM', 1);

if (!defined('FORUM'))
	error('The file \'config.php\' doesn\'t exist or is corrupt.<br/>Please run <a href="'.FORUM_ROOT.'admin/install.php">install.php</a> to install PunBB first.');

// Block prefetch requests
if (isset($_SERVER['HTTP_X_MOZ']) && $_SERVER['HTTP_X_MOZ'] == 'prefetch')
{
	header('HTTP/1.1 403 Prefetching Forbidden');

	// Send no-cache headers
	header('Expires: Thu, 21 Jul 1977 07:30:00 GMT');	// When yours truly first set eyes on this world! :)
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: post-check=0, pre-check=0', false);
	header('Pragma: no-cache');		// For HTTP/1.0 compability

	exit;
}

// Make sure PHP reports all errors except E_NOTICE. PunBB supports E_ALL, but a lot of scripts it may interact with, do not.
if (defined('FORUM_DEBUG'))
	error_reporting(E_ALL);
else
	error_reporting(E_ALL ^ E_NOTICE);

// Detect UTF-8 support in PCRE
if ((version_compare(PHP_VERSION, '5.1.0', '>=') || (version_compare(PHP_VERSION, '5.0.0-dev', '<=') && version_compare(PHP_VERSION, '4.4.0', '>='))) && @/**/preg_match('/\p{L}/u', 'a') !== FALSE)
{
	define('FORUM_SUPPORT_PCRE_UNICODE', 1);
}

// Force POSIX locale (to prevent functions such as strtolower() from messing up UTF-8 strings)
setlocale(LC_CTYPE, 'C');

// If the cache directory is not specified, we use the default setting
if (!defined('FORUM_CACHE_DIR'))
	define('FORUM_CACHE_DIR', FORUM_ROOT.'cache/');

// Load DB abstraction layer and connect
require FORUM_ROOT.'include/dblayer/common_db.php';

// Start a transaction
$forum_db->start_transaction();

// Load cached config
if (file_exists(FORUM_CACHE_DIR.'cache_config.php'))
	include FORUM_CACHE_DIR.'cache_config.php';

if (!defined('FORUM_CONFIG_LOADED'))
{
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_config_cache();
	require FORUM_CACHE_DIR.'cache_config.php';
}

// If the request_uri is invalid try fix it
forum_fix_request_uri();

if (!isset($base_url))
{
	// Make an educated guess regarding base_url
	$base_url_guess = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://').preg_replace('/:80$/', '', $_SERVER['HTTP_HOST']).str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
	if (substr($base_url_guess, -1) == '/')
		$base_url_guess = substr($base_url_guess, 0, -1);

	$base_url = $base_url_guess;
}

// Verify that we are running the proper database schema revision
if (defined('PUN') || !isset($forum_config['o_database_revision']) || $forum_config['o_database_revision'] < FORUM_DB_REVISION || version_compare($forum_config['o_cur_version'], FORUM_VERSION, '<'))
	error('Your PunBB database is out-of-date and must be upgraded in order to continue.<br/>Please run <a href="'.$base_url.'/admin/db_update.php">db_update.php</a> in order to complete the upgrade process.');


// Load hooks
if (file_exists(FORUM_CACHE_DIR.'cache_hooks.php'))
	include FORUM_CACHE_DIR.'cache_hooks.php';

if (!defined('FORUM_HOOKS_LOADED'))
{
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_hooks_cache();
	require FORUM_CACHE_DIR.'cache_hooks.php';
}

// Define a few commonly used constants
define('FORUM_UNVERIFIED', 0);
define('FORUM_ADMIN', 1);
define('FORUM_GUEST', 2);

// Define avatars type
define('FORUM_AVATAR_NONE', 0);
define('FORUM_AVATAR_GIF', 1);
define('FORUM_AVATAR_JPG', 2);
define('FORUM_AVATAR_PNG', 3);

// A good place to add common functions for your extension
($hook = get_hook('es_essentials')) ? eval($hook) : null;

if (!defined('FORUM_MAX_POSTSIZE_BYTES'))
	define('FORUM_MAX_POSTSIZE_BYTES', 65535);

define('FORUM_ESSENTIALS_LOADED', 1);
