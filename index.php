<?php
/**
 * Displays a list of the categories/forums that the current user can see, along with some statistics.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */


if (!defined('FORUM_ROOT'))
	define('FORUM_ROOT', './');
require FORUM_ROOT.'include/common.php';

($hook = get_hook('in_start')) ? eval($hook) : null;

if ($forum_user['g_read_board'] == '0')
	message($lang_common['No view']);

// Load the index.php language file
require FORUM_ROOT.'lang/'.$forum_user['language'].'/index.php';


// Get list of forums and topics with new posts since last visit
if (!$forum_user['is_guest'])
{
	$query = array(
		'SELECT'	=> 't.forum_id, t.id, t.last_post',
		'FROM'		=> 'topics AS t',
		'JOINS'		=> array(
			array(
				'INNER JOIN'	=> 'forums AS f',
				'ON'			=> 'f.id=t.forum_id'
			),
			array(
				'LEFT JOIN'		=> 'forum_perms AS fp',
				'ON'			=> '(fp.forum_id=f.id AND fp.group_id='.$forum_user['g_id'].')'
			)
		),
		'WHERE'		=> '(fp.read_forum IS NULL OR fp.read_forum=1) AND t.last_post>'.$forum_user['last_visit'].' AND t.moved_to IS NULL'
	);

	($hook = get_hook('in_qr_get_new_topics')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

	$new_topics = array();
	while ($cur_topic = $forum_db->fetch_assoc($result))
		$new_topics[$cur_topic['forum_id']][$cur_topic['id']] = $cur_topic['last_post'];

	$tracked_topics = get_tracked_topics();
}

// Setup main heading
$forum_page['main_title'] = forum_htmlencode($forum_config['o_board_title']);

($hook = get_hook('in_pre_header_load')) ? eval($hook) : null;

define('FORUM_ALLOW_INDEX', 1);
define('FORUM_PAGE', 'index');
require FORUM_ROOT.'header.php';

// START SUBST - <!-- forum_main -->
ob_start();
include FORUM_ROOT . 'include/view/index/main.php';
$view_forum_main = forum_trim(ob_get_contents());
$tpl_main = str_replace('<!-- forum_main -->', $view_forum_main, $tpl_main);
ob_end_clean();
// END SUBST - <!-- forum_main -->

// START SUBST - <!-- forum_info -->
ob_start();
include FORUM_ROOT . 'include/view/index/info.php';
$view_forum_info = forum_trim(ob_get_contents());
$tpl_main = str_replace('<!-- forum_info -->', $view_forum_info, $tpl_main);
ob_end_clean();
// END SUBST - <!-- forum_info -->

require FORUM_ROOT.'footer.php';
