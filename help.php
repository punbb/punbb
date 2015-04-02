<?php
/**
 * Help page.
 *
 * Provides examples of how to use various features of the forum (ie: BBCode, smilies).
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */


if (!defined('FORUM_ROOT'))
	define('FORUM_ROOT', './');
require FORUM_ROOT.'include/common.php';

($hook = get_hook('he_start')) ? eval($hook) : null;

if ($forum_user['g_read_board'] == '0')
	message($lang_common['No view']);

// Load the help.php language file
require FORUM_ROOT.'lang/'.$forum_user['language'].'/help.php';

$section = isset($_GET['section']) ? $_GET['section'] : null;
if (!$section)
	message($lang_common['Bad request']);

$forum_page['crumbs'] = array(
	array($forum_config['o_board_title'], forum_link($forum_url['help'])),
	$lang_help['Help']
);

define('FORUM_PAGE', 'help');

$view_forum_main = 'section/help/main';
include FORUM_ROOT . 'include/render.php';
