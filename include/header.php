<?php
/**
 * Outputs the header used by most forum pages.
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

if (!defined('FORUM_HEADER')) {
	define('FORUM_HEADER', 1);

	// Send no-cache headers
	header('Expires: Thu, 21 Jul 1977 07:30:00 GMT');	// When yours truly first set eyes on this world! :)
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: post-check=0, pre-check=0', false);
	header('Pragma: no-cache');		// For HTTP/1.0 compability

	// Send the Content-type header in case the web server is setup to send something else
	header('Content-type: text/html; charset=utf-8');

	// TODO use event 'page_render'
	// ob_start();

	// Init the main template
	if (substr(FORUM_PAGE, 0, 5) == 'admin') {
		$template = 'layout/admin';
	}
	else if (FORUM_PAGE == 'help') {
		$template = 'layout/help';
	}
	else {
		$template = 'layout/main';
	}

	// Forum page id and classes
	if (!defined('FORUM_PAGE_TYPE')) {
		if (substr(FORUM_PAGE, 0, 5) == 'admin') {
			define('FORUM_PAGE_TYPE', 'admin-page');
		}
		else {
			if (!empty($forum_page['page_post'])) {
				define('FORUM_PAGE_TYPE', 'paged-page');
			}
			else if (!empty($forum_page['main_menu'])) {
				define('FORUM_PAGE_TYPE', 'menu-page');
			}
			else {
				define('FORUM_PAGE_TYPE', 'basic-page');
			}
		}
	}

}
