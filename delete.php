<?php
/**
 * Post deletion page.
 *
 * Deletes the specified post (and, if necessary, the topic it is in).
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */


if (!defined('FORUM_ROOT'))
	define('FORUM_ROOT', './');
require FORUM_ROOT.'include/common.php';

($hook = get_hook('dl_start')) ? eval($hook) : null;

if ($forum_user['g_read_board'] == '0')
	message($lang_common['No view']);

// Load the delete.php language file
require FORUM_ROOT.'lang/'.$forum_user['language'].'/delete.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id < 1)
	message($lang_common['Bad request']);


// Fetch some info about the post, the topic and the forum
$query = array(
	'SELECT'	=> 'f.id AS fid, f.forum_name, f.moderators, f.redirect_url, fp.post_replies, fp.post_topics, t.id AS tid, t.subject, t.first_post_id, t.closed, p.poster, p.poster_id, p.message, p.hide_smilies, p.posted',
	'FROM'		=> 'posts AS p',
	'JOINS'		=> array(
		array(
			'INNER JOIN'	=> 'topics AS t',
			'ON'			=> 't.id=p.topic_id'
		),
		array(
			'INNER JOIN'	=> 'forums AS f',
			'ON'			=> 'f.id=t.forum_id'
		),
		array(
			'LEFT JOIN'		=> 'forum_perms AS fp',
			'ON'			=> '(fp.forum_id=f.id AND fp.group_id='.$forum_user['g_id'].')'
		)
	),
	'WHERE'		=> '(fp.read_forum IS NULL OR fp.read_forum=1) AND p.id='.$id
);

($hook = get_hook('dl_qr_get_post_info')) ? eval($hook) : null;
$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
$cur_post = $forum_db->fetch_assoc($result);

if (!$cur_post)
	message($lang_common['Bad request']);

// Sort out who the moderators are and if we are currently a moderator (or an admin)
$mods_array = ($cur_post['moderators'] != '') ? unserialize($cur_post['moderators']) : array();
$forum_page['is_admmod'] = ($forum_user['g_id'] == FORUM_ADMIN || ($forum_user['g_moderator'] == '1' && array_key_exists($forum_user['username'], $mods_array))) ? true : false;

$cur_post['is_topic'] = ($id == $cur_post['first_post_id']) ? true : false;

($hook = get_hook('dl_pre_permission_check')) ? eval($hook) : null;

// Do we have permission to delete this post?
if ((($forum_user['g_delete_posts'] == '0' && !$cur_post['is_topic']) ||
	($forum_user['g_delete_topics'] == '0' && $cur_post['is_topic']) ||
	$cur_post['poster_id'] != $forum_user['id'] ||
	$cur_post['closed'] == '1') &&
	!$forum_page['is_admmod'])
	message($lang_common['No permission']);


($hook = get_hook('dl_post_selected')) ? eval($hook) : null;

// User pressed the cancel button
if (isset($_POST['cancel']))
	redirect(forum_link($forum_url['post'], $id), $lang_common['Cancel redirect']);

// User pressed the delete button
else if (isset($_POST['delete']))
{
	($hook = get_hook('dl_form_submitted')) ? eval($hook) : null;

	if (!isset($_POST['req_confirm']))
		redirect(forum_link($forum_url['post'], $id), $lang_common['No confirm redirect']);

	if ($cur_post['is_topic'])
	{
		// Delete the topic and all of it's posts
		delete_topic($cur_post['tid'], $cur_post['fid']);

		$forum_flash->add_info($lang_delete['Topic del redirect']);

		($hook = get_hook('dl_topic_deleted_pre_redirect')) ? eval($hook) : null;

		redirect(forum_link($forum_url['forum'], array($cur_post['fid'], sef_friendly($cur_post['forum_name']))), $lang_delete['Topic del redirect']);
	}
	else
	{
		// Delete just this one post
		delete_post($id, $cur_post['tid'], $cur_post['fid']);

		// Fetch previus post #id in some topic for redirect after delete
		$query = array(
			'SELECT'	=> 'p.id',
			'FROM'		=> 'posts AS p',
			'WHERE'		=> 'p.topic_id = '.$cur_post['tid'].' AND p.id < '.$id,
			'ORDER BY'	=> 'p.id DESC',
			'LIMIT'		=> '1'
		);

		($hook = get_hook('dl_post_deleted_get_prev_post_id')) ? eval($hook) : null;
		$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
		$prev_post = $forum_db->fetch_assoc($result);

		$forum_flash->add_info($lang_delete['Post del redirect']);

		($hook = get_hook('dl_post_deleted_pre_redirect')) ? eval($hook) : null;

		if (isset($prev_post['id']))
		{
			redirect(forum_link($forum_url['post'], $prev_post['id']), $lang_delete['Post del redirect']);
		}
		else
		{
			redirect(forum_link($forum_url['topic'], array($cur_post['tid'], sef_friendly($cur_post['subject']))), $lang_delete['Post del redirect']);
		}
	}
}

// Run the post through the parser
if (!defined('FORUM_PARSER_LOADED'))
	require FORUM_ROOT.'include/parser.php';

$cur_post['message'] = parse_message($cur_post['message'], $cur_post['hide_smilies']);

// Setup form
$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;
$forum_page['form_action'] = forum_link($forum_url['delete'], $id);

$forum_page['hidden_fields'] = array(
	'form_sent'		=> '<input type="hidden" name="form_sent" value="1" />',
	'csrf_token'	=> '<input type="hidden" name="csrf_token" value="'.generate_form_token($forum_page['form_action']).'" />'
);

// Setup form information
$forum_page['frm_info'] = array(
	'<li><span>'.$lang_delete['Forum'].':<strong> '.forum_htmlencode($cur_post['forum_name']).'</strong></span></li>',
	'<li><span>'.$lang_delete['Topic'].':<strong> '.forum_htmlencode($cur_post['subject']).'</strong></span></li>'
);

// Generate the post heading
$forum_page['post_ident'] = array();
$forum_page['post_ident']['byline'] = '<span class="post-byline">'.sprintf((($cur_post['is_topic']) ? $lang_delete['Topic byline'] : $lang_delete['Reply byline']), '<strong>'.forum_htmlencode($cur_post['poster']).'</strong>').'</span>';
$forum_page['post_ident']['link'] = '<span class="post-link"><a class="permalink" href="'.forum_link($forum_url['post'], $cur_post['tid']).'">'.format_time($cur_post['posted']).'</a></span>';

($hook = get_hook('dl_pre_item_ident_merge')) ? eval($hook) : null;

// Generate the post title
if ($cur_post['is_topic'])
	$forum_page['item_subject'] = sprintf($lang_delete['Topic title'], $cur_post['subject']);
else
	$forum_page['item_subject'] = sprintf($lang_delete['Reply title'], $cur_post['subject']);

$forum_page['item_subject'] = forum_htmlencode($forum_page['item_subject']);

// Setup breadcrumbs
$forum_page['crumbs'] = array(
	array($forum_config['o_board_title'], forum_link($forum_url['index'])),
	array($cur_post['forum_name'], forum_link($forum_url['forum'], array($cur_post['fid'], sef_friendly($cur_post['forum_name'])))),
	array($cur_post['subject'], forum_link($forum_url['topic'], array($cur_post['tid'], sef_friendly($cur_post['subject'])))),
	(($cur_post['is_topic']) ? $lang_delete['Delete topic'] : $lang_delete['Delete post'])
);

($hook = get_hook('dl_pre_header_load')) ? eval($hook) : null;

define ('FORUM_PAGE', 'postdelete');
require FORUM_ROOT.'header.php';

$view_forum_main = 'delete/main';

ob_start();
include view($view_forum_layout);
$tpl_main = forum_trim(ob_get_contents());
ob_end_clean();

require FORUM_ROOT.'footer.php';
