<?php
/**
 * Caching functions.
 *
 * This file contains all of the functions used to generate the cache files used by the site.
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

class Cache {

	private $values = array();

	// Safe create or write of cache files
	// Use LOCK
	function set($key, $content) {
		$file = FORUM_CACHE_DIR . $key . '.php';

		// Open
		$handle = @fopen($file, 'r+b'); // @ - file may not exist
		if (!$handle) {
			$handle = fopen($file, 'wb');
			if (!$handle) {
				return false;
			}
		}

		// Lock
		flock($handle, LOCK_EX);
		ftruncate($handle, 0);

		// Write
		if (fwrite($handle, $content) === false) {
			// Unlock and close
			flock($handle, LOCK_UN);
			fclose($handle);
			return false;
		}

		// Unlock and close
		flock($handle, LOCK_UN);
		fclose($handle);

		return true;
	}

	function get($key, $generator = null, $args = []) {
		$file = FORUM_CACHE_DIR . $key . '.php';
		if (isset($this->values[$key])) {
			return $this->values[$key];
		}
		if (file_exists($file)) {
			return $this->values[$key] = include $file;
		}
		if (is_callable($generator)) {
			call_user_func_array($generator, $args);
		}
		if (file_exists($file)) {
			return $this->values[$key] = include $file;
		}
		return null;
	}

}
