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

($hook = get_hook('hd_pre_template_loaded')) ? eval($hook) : null;

$tpl_main = file_get_contents($tpl_path);

($hook = get_hook('hd_template_loaded')) ? eval($hook) : null;

if (0) { // no need this, should use own template construction for including files
// START SUBST - <!-- forum_include "*" -->
while (preg_match('#<!-- ?forum_include "([^/\\\\]*?)" ?-->#', $tpl_main, $cur_include))
{
	if (!file_exists(FORUM_ROOT.'include/user/'.$cur_include[1]))
		error('Unable to process user include &lt;!-- forum_include "'.forum_htmlencode($cur_include[1]).'" --&gt; from template main.tpl.<br />There is no such file in folder /include/user/', __FILE__, __LINE__);

	ob_start();
	include FORUM_ROOT.'include/user/'.$cur_include[1];
	$tpl_temp = ob_get_contents();
	$tpl_main = str_replace($cur_include[0], $tpl_temp, $tpl_main);
	ob_end_clean();
}
// END SUBST - <!-- forum_include "*" -->
}

// START SUBST - <!-- forum_local -->
$view_forum_local = 'lang="'.$lang_common['lang_identifier'].'" dir="'.$lang_common['lang_direction'].'"';
$tpl_main = str_replace('<!-- forum_local -->', $view_forum_local, $tpl_main);
// END SUBST - <!-- forum_local -->


// START SUBST - <!-- forum_head -->
include FORUM_ROOT . 'include/view/partial/head.php';
$view_forum_head = $tmp_head;
$tpl_main = str_replace('<!-- forum_head -->', $view_forum_head, $tpl_main);
unset($forum_head, $tmp_head);
// END SUBST - <!-- forum_head -->


// START SUBST OF COMMON ELEMENTS
// Setup array of general elements
$gen_elements = array();
include FORUM_ROOT . 'include/view/partial/gen_elements.php';
// TODO insert $view_forum_messages and etc. in template
$tpl_main = str_replace(array_keys($gen_elements), array_values($gen_elements), $tpl_main);
unset($gen_elements);
// END SUBST OF COMMON ELEMENTS


// START SUBST VISIT ELEMENTS
$visit_elements = array();
include FORUM_ROOT . 'include/view/partial/visit_elements.php';
// TODO insert $view_forum_visit and etc. in template
$tpl_main = str_replace(array_keys($visit_elements), array_values($visit_elements), $tpl_main);
unset($visit_elements);
// END SUBST VISIT ELEMENTS


// START SUBST - <!-- forum_admod -->
$admod_links = array();
include FORUM_ROOT . 'include/view/partial/admod.php';
$tpl_main = str_replace('<!-- forum_admod -->', (!empty($admod_links)) ? '<ul id="brd-admod">'.implode(' ', $admod_links).'</ul>' : '', $tpl_main);
// END SUBST - <!-- forum_admod -->


// MAIN SECTION INTERFACE ELEMENT SUBSTITUTION
$main_elements = array();
include FORUM_ROOT . 'include/view/partial/main_elements.php';
$tpl_main = str_replace(array_keys($main_elements), array_values($main_elements), $tpl_main);
unset($main_elements);
// END MAIN SECTION INTERFACE ELEMENT SUBSTITUTION


($hook = get_hook('hd_end')) ? eval($hook) : null;

if (!defined('FORUM_HEADER'))
	define('FORUM_HEADER', 1);
