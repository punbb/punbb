<?php
/**
 * Loads the proper database layer class.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */
namespace punbb;

// Make sure no one attempts to run this script "directly"
if (!defined('FORUM')) {
	exit;
}

function db($type = null) {
	global $_PUNBB;
	// TODO fix
	global $db_type;
	global $db_host, $db_username, $db_password, $db_name, $db_prefix, $p_connect;

	if (isset($_PUNBB['db'])) {
		return $_PUNBB['db'];
	}

	if (!$type) {
		$type = $db_type;
	}

	// Create the database adapter object (and open/connect to/select db)
	$classname = 'punbb\\DBLayer_' . $type;
	return $_PUNBB['db'] = new $classname(
		$db_host,
		$db_username,
		$db_password,
		$db_name,
		$db_prefix,
		$p_connect);
}

// TODO fix
global $forum_db;
$forum_db = db();
