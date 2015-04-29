<?php
/**
 * Loads common data and performs various functions necessary for the site to work properly.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */
namespace punbb;

if (!defined('FORUM_ROOT')) {
	exit('The constant FORUM_ROOT must be defined and point to a valid PunBB installation root directory.');
}

// Turn off magic_quotes_runtime
if (get_magic_quotes_runtime()) {
	@ini_set('magic_quotes_runtime', false);
}

// Strip slashes from GET/POST/COOKIE (if magic_quotes_gpc is enabled)
if (get_magic_quotes_gpc()) {
	function stripslashes_array($array) {
		return is_array($array) ?
			array_map('stripslashes_array', $array) : stripslashes($array);
	}
	$_GET = stripslashes_array($_GET);
	$_POST = stripslashes_array($_POST);
	$_COOKIE = stripslashes_array($_COOKIE);
}

// Strip out "bad" UTF-8 characters
forum_remove_bad_characters();

global $cookie_name;
// If a cookie name is not specified in config.php, we use the default (forum_cookie)
if (empty($cookie_name)) {
	$cookie_name = 'forum_cookie';
}

// TODO move to output page handler
// Enable output buffering
if (!defined('FORUM_DISABLE_BUFFERING')) {
	// For some very odd reason, "Norton Internet Security" unsets this
	$_SERVER['HTTP_ACCEPT_ENCODING'] = isset($_SERVER['HTTP_ACCEPT_ENCODING'])?
		$_SERVER['HTTP_ACCEPT_ENCODING'] : '';

	// Should we use gzip output compression?
	if (config()->o_gzip && extension_loaded('zlib') &&
				(strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false ||
				strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate') !== false)) {
		ob_start('ob_gzhandler');
	}
	else {
		ob_start();
	}
}

global $forum_time_formats, $forum_date_formats;

// Define standard date/time formats
$forum_time_formats = array(
	config()->o_time_format,
	'H:i:s', 'H:i', 'g:i:s a', 'g:i a'
);
$forum_date_formats = array(
	config()->o_date_format,
	'Y-m-d', 'Y-d-m', 'd-m-Y', 'm-d-Y', 'M j Y', 'jS M Y'
);

global $forum_page;
// Create forum_page array
$forum_page = array();

// TODO error - use triger_error or throw new exception
// Attempt to load the common language file
if (!file_exists(PUNBB::get('language')->path[user()->language] . '/common.php')) {
	error('There is no valid language pack \'' .
		forum_htmlencode(user()->language) .
		'\' installed.<br />Please reinstall a language of that name.');
}

global $forum_url, $forum_reserved_strings, $forum_rewrite_rules;
// Setup the URL rewriting scheme
$fname_rewrites = PUNBB::get('urls')->path[config()->o_sef] . '/forum_urls.php';
if (config()->o_sef != 'Default' && file_exists($fname_rewrites)) {
	$forum_url = require $fname_rewrites;
}
else {
	$forum_url = PUNBB::get('urls')->path['Default'] . '/forum_urls.php';
}

var_dump($forum_url);

// A good place to modify the URL scheme
($hook = get_hook('co_modify_url_scheme')) ? eval($hook) : null;

// Check if we are to display a maintenance message
if (config()->o_maintenance && user()->g_id > FORUM_ADMIN &&
		!defined('FORUM_TURN_OFF_MAINT')) {
	maintenance_message();
}

// Load cached updates info
if (user()->g_id == FORUM_ADMIN) {
	if (file_exists(FORUM_CACHE_DIR . 'cache_updates.php')) {
		include FORUM_CACHE_DIR . 'cache_updates.php';
	}
	// Regenerate cache only if automatic updates are enabled and if the cache is more than 12 hours old
	if (config()->o_check_for_updates == '1' &&
			(!defined('FORUM_UPDATES_LOADED') ||
				$forum_updates['cached'] < (time() - 43200))) {
		require FORUM_ROOT . 'include/cache.php';
		generate_updates_cache();
		require FORUM_CACHE_DIR . 'cache_updates.php';
	}
}

global $forum_bans;
// Load cached bans
if (file_exists(FORUM_CACHE_DIR . 'cache_bans.php')) {
	include FORUM_CACHE_DIR . 'cache_bans.php';
}
if (!defined('FORUM_BANS_LOADED')) {
	require FORUM_ROOT . 'include/cache.php';
	generate_bans_cache();
	require FORUM_CACHE_DIR . 'cache_bans.php';
}
// Check if current user is banned
check_bans();

// Update online list
update_users_online();

// Check to see if we logged in without a cookie being set
if (user()->is_guest && isset($_GET['login'])) {
	message(__('No cookie'));
}

// If we're an administrator or moderator, make sure the CSRF token in $_POST is valid (token in post.php is dealt with in post.php)
if (!empty($_POST) && (isset($_POST['confirm_cancel']) ||
	(!isset($_POST['csrf_token']) ||
		$_POST['csrf_token'] !== generate_form_token(get_current_url()))) &&
		!defined('FORUM_SKIP_CSRF_CONFIRM')) {
	csrf_confirm_form();
}

($hook = get_hook('co_common')) ? eval($hook) : null;
