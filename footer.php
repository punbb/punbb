<?php
/**
 * Outputs the footer used by most forum pages.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */


// Make sure no one attempts to run this script "directly"
if (!defined('FORUM'))
	exit;

// START SUBST - <!-- forum_about -->
ob_start();
include FORUM_ROOT . 'include/view/partial/about.php';
$view_forum_about = forum_trim(ob_get_contents());
$tpl_main = str_replace('<!-- forum_about -->', $view_forum_about, $tpl_main);
ob_end_clean();
// END SUBST - <!-- forum_about -->


// START SUBST - <!-- forum_debug -->
if (defined('FORUM_DEBUG') || defined('FORUM_SHOW_QUERIES'))
{
	ob_start();
	include FORUM_ROOT . 'include/view/partial/debug.php';
	$view_forum_debug = forum_trim(ob_get_contents());
	$tpl_main = str_replace('<!-- forum_debug -->', $view_forum_debug, $tpl_main);
	ob_end_clean();
}
// END SUBST - <!-- forum_debug -->


// START SUBST - <!-- forum_javascript -->
include FORUM_ROOT . 'include/view/partial/javascript.php';
$view_forum_javascript = $forum_loader->render_js();
$tpl_main = str_replace('<!-- forum_javascript -->', $view_forum_javascript, $tpl_main);
// END SUBST - <!-- forum_javascript -->

// Last call!
($hook = get_hook('ft_end')) ? eval($hook) : null;

// End the transaction
$forum_db->end_transaction();

// Close the db connection (and free up any result data)
$forum_db->close();

// Spit out the page
exit($tpl_main);
