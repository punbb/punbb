<?php
/**
 * Outputs the header used by most forum pages.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */


// Make sure no one attempts to run this script "directly"
if (!defined('FORUM'))
	exit;

// Send no-cache headers
header('Expires: Thu, 21 Jul 1977 07:30:00 GMT');	// When yours truly first set eyes on this world! :)
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');		// For HTTP/1.0 compability

// Send the Content-type header in case the web server is setup to send something else
header('Content-type: text/html; charset=utf-8');

ob_start();

// Load the main template
if (substr(FORUM_PAGE, 0, 5) == 'admin')
{
	if ($forum_user['style'] != 'Oxygen' && file_exists(FORUM_ROOT.'style/'.$forum_user['style'].'/admin.tpl'))
		$tpl_path = FORUM_ROOT.'style/'.$forum_user['style'].'/admin.tpl';
	else
		$tpl_path = FORUM_ROOT.'include/template/admin.tpl';
}
else if (FORUM_PAGE == 'help')
{
	if ($forum_user['style'] != 'Oxygen' && file_exists(FORUM_ROOT.'style/'.$forum_user['style'].'/help.tpl'))
		$tpl_path = FORUM_ROOT.'style/'.$forum_user['style'].'/help.tpl';
	else
		$tpl_path = FORUM_ROOT.'include/template/help.tpl';
}
else
{
	if ($forum_user['style'] != 'Oxygen' && file_exists(FORUM_ROOT.'style/'.$forum_user['style'].'/main.tpl'))
		$tpl_path = FORUM_ROOT.'style/'.$forum_user['style'].'/main.tpl';
	else
		$tpl_path = FORUM_ROOT.'include/template/main.tpl';
}

// init layout for templates
$view_forum_layout = 'layout/' . basename($tpl_path, '.tpl');

($hook = get_hook('hd_pre_template_loaded')) ? eval($hook) : null;

$tpl_main = file_get_contents($tpl_path);// TODO remove

($hook = get_hook('hd_template_loaded')) ? eval($hook) : null;

$view_forum_local = 'lang="'.$lang_common['lang_identifier'].'" dir="'.$lang_common['lang_direction'].'"';

include view('partial/gen_elements');
include view('partial/visit_elements');
include view('partial/admod');
include view('partial/main_elements');

($hook = get_hook('hd_end')) ? eval($hook) : null;

if (!defined('FORUM_HEADER'))
	define('FORUM_HEADER', 1);
