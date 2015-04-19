<?php
/**
 * Edit post page.
 *
 * Modifies the contents of the specified post.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */
namespace punbb;

require __DIR__ . '/vendor/pautoload.php';


($hook = get_hook('ed_start')) ? eval($hook) : null;

if (user()->g_read_board == '0') {
	message(__('No view'));
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id < 1)
	message(__('Bad request'));


// Fetch some info about the post, the topic and the forum
$query = array(
	'SELECT'	=> 'f.id AS fid, f.forum_name, f.moderators, f.redirect_url, fp.post_replies, fp.post_topics, t.id AS tid, t.subject, t.posted, t.first_post_id, t.closed, p.poster, p.poster_id, p.message, p.hide_smilies',
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
			'ON'			=> '(fp.forum_id=f.id AND fp.group_id='.user()->g_id.')'
		)
	),
	'WHERE'		=> '(fp.read_forum IS NULL OR fp.read_forum=1) AND p.id='.$id
);

($hook = get_hook('ed_qr_get_post_info')) ? eval($hook) : null;
$result = db()->query_build($query) or error(__FILE__, __LINE__);
$cur_post = db()->fetch_assoc($result);

if (!$cur_post)
	message(__('Bad request'));

// Sort out who the moderators are and if we are currently a moderator (or an admin)
$mods_array = ($cur_post['moderators'] != '') ? unserialize($cur_post['moderators']) : array();
$forum_page['is_admmod'] = (user()->g_id == FORUM_ADMIN || (user()->g_moderator == '1' && array_key_exists(user()->username, $mods_array))) ? true : false;

($hook = get_hook('ed_pre_permission_check')) ? eval($hook) : null;

// Do we have permission to edit this post?
if ((user()->g_edit_posts == '0' ||
	$cur_post['poster_id'] != user()->id ||
	$cur_post['closed'] == '1') &&
	!$forum_page['is_admmod'])
	message(__('No permission'));


$can_edit_subject = $id == $cur_post['first_post_id'];

($hook = get_hook('ed_post_selected')) ? eval($hook) : null;


// Start with a clean slate
$errors = array();

if (isset($_POST['form_sent']))
{
	($hook = get_hook('ed_form_submitted')) ? eval($hook) : null;

	// If it is a topic it must contain a subject
	if ($can_edit_subject)
	{
		$subject = forum_trim($_POST['req_subject']);

		if ($subject == '')
			$errors[] = __('No subject', 'post');
		else if (utf8_strlen($subject) > FORUM_SUBJECT_MAXIMUM_LENGTH)
			$errors[] = sprintf(__('Too long subject', 'post'), FORUM_SUBJECT_MAXIMUM_LENGTH);
		else if (config()->p_subject_all_caps == '0' && check_is_all_caps($subject) && !$forum_page['is_admmod'])
			$subject = utf8_ucwords(utf8_strtolower($subject));
	}

	// Clean up message from POST
	$message = forum_linebreaks(forum_trim($_POST['req_message']));

	if (strlen($message) > FORUM_MAX_POSTSIZE_BYTES)
		$errors[] = sprintf(__('Too long message', 'post'), forum_number_format(strlen($message)), forum_number_format(FORUM_MAX_POSTSIZE_BYTES));
	else if (config()->p_message_all_caps == '0' && check_is_all_caps($message) && !$forum_page['is_admmod'])
		$message = utf8_ucwords(utf8_strtolower($message));

	// Validate BBCode syntax
	if (config()->p_message_bbcode == '1' || config()->o_make_links == '1')
	{
		if (!defined('FORUM_PARSER_LOADED'))
			require FORUM_ROOT.'include/parser.php';

		$message = preparse_bbcode($message, $errors);
	}

	if ($message == '')
		$errors[] = __('No message', 'post');

	$hide_smilies = isset($_POST['hide_smilies']) ? 1 : 0;

	($hook = get_hook('ed_end_validation')) ? eval($hook) : null;

	// Did everything go according to plan?
	if (empty($errors) && !isset($_POST['preview']))
	{
		($hook = get_hook('ed_pre_post_edited')) ? eval($hook) : null;

		if (!defined('FORUM_SEARCH_IDX_FUNCTIONS_LOADED'))
			require FORUM_ROOT.'include/search_idx.php';

		if ($can_edit_subject)
		{
			// Update the topic and any redirect topics
			$query = array(
				'UPDATE'	=> 'topics',
				'SET'		=> 'subject=\''.db()->escape($subject).'\'',
				'WHERE'		=> 'id='.$cur_post['tid'].' OR moved_to='.$cur_post['tid']
			);

			($hook = get_hook('ed_qr_update_subject')) ? eval($hook) : null;
			db()->query_build($query) or error(__FILE__, __LINE__);

			// We changed the subject, so we need to take that into account when we update the search words
			update_search_index('edit', $id, $message, $subject);
		}
		else
			update_search_index('edit', $id, $message);

		// Update the post
		$query = array(
			'UPDATE'	=> 'posts',
			'SET'		=> 'message=\''.db()->escape($message).'\', hide_smilies=\''.$hide_smilies.'\'',
			'WHERE'		=> 'id='.$id
		);

		if (!isset($_POST['silent']) || !$forum_page['is_admmod'])
			$query['SET'] .= ', edited='.time().', edited_by=\''.db()->escape(user()->username).'\'';

		($hook = get_hook('ed_qr_update_post')) ? eval($hook) : null;
		db()->query_build($query) or error(__FILE__, __LINE__);

		($hook = get_hook('ed_pre_redirect')) ? eval($hook) : null;

		redirect(forum_link($forum_url['post'], $id), __('Edit redirect', 'post'));
	}
}

// Setup error messages
if (!empty($errors))
{
	$forum_page['errors'] = array();

	foreach ($errors as $cur_error)
		$forum_page['errors'][] = '<li><span>'.$cur_error.'</span></li>';
}

// Setup form
$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;
$forum_page['form_action'] = forum_link($forum_url['edit'], $id);
$forum_page['form_attributes'] = array();

$forum_page['hidden_fields'] = array(
	'form_sent'		=> '<input type="hidden" name="form_sent" value="1" />',
	'csrf_token'	=> '<input type="hidden" name="csrf_token" value="'.generate_form_token($forum_page['form_action']).'" />'
);

// Setup help
$forum_page['main_head_options'] = array();
if (config()->p_message_bbcode == '1')
	$forum_page['text_options']['bbcode'] = '<span'.(empty($forum_page['text_options']) ? ' class="first-item"' : '').'><a class="exthelp" href="'.forum_link($forum_url['help'], 'bbcode').'" title="'.
	sprintf(__('Help page'), __('BBCode')).'">'.__('BBCode').'</a></span>';
if (config()->p_message_img_tag == '1')
	$forum_page['text_options']['img'] = '<span'.(empty($forum_page['text_options']) ? ' class="first-item"' : '').'><a class="exthelp" href="'.forum_link($forum_url['help'], 'img').'" title="'.
	sprintf(__('Help page'), __('Images')).'">'.__('Images').'</a></span>';
if (config()->o_smilies == '1')
	$forum_page['text_options']['smilies'] = '<span'.(empty($forum_page['text_options']) ? ' class="first-item"' : '').'><a class="exthelp" href="'.forum_link($forum_url['help'], 'smilies').'" title="'.
	sprintf(__('Help page'), __('Smilies')).'">'.__('Smilies').'</a></span>';


// Setup breadcrumbs
$forum_page['crumbs'] = array(
	array(config()->o_board_title, forum_link($forum_url['index'])),
	array($cur_post['forum_name'], forum_link($forum_url['forum'], array($cur_post['fid'], sef_friendly($cur_post['forum_name'])))),
	array($cur_post['subject'], forum_link($forum_url['topic'], array($cur_post['tid'], sef_friendly($cur_post['subject'])))),
	(($id == $cur_post['first_post_id']) ?
		__('Edit topic', 'post') : __('Edit reply', 'post'))
);

($hook = get_hook('ed_pre_header_load')) ? eval($hook) : null;

define('FORUM_PAGE', 'postedit');

$forum_main_view = 'edit/main';
template()->render();
