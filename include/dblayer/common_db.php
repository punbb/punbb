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

db(function () {
	global $_PUNBB;
	// TODO fix
	global $db_type;
	global $db_host, $db_username, $db_password, $db_name, $db_prefix, $p_connect;

	if (!isset($_PUNBB['db'])) {
		// Create the database adapter object (and open/connect to/select db)
		$classname = 'punbb\\DBLayer_' . $db_type;
		$_PUNBB['db'] = new $classname(
			$db_host,
			$db_username,
			$db_password,
			$db_name,
			$db_prefix,
			$p_connect);
	}

	return $_PUNBB['db'];
});