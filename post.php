<?php
/**
 * Adds a new post to the specified topic or a new topic to the specified forum.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */
namespace punbb;

define('FORUM_SKIP_CSRF_CONFIRM', 1);

require __DIR__ . '/vendor/pautoload.php';

($hook = get_hook('po_start')) ? eval($hook) : null;

if (user()->g_read_board == '0') {
	message(__('No view'));
}

$tid = isset($_GET['tid']) ? intval($_GET['tid']) : 0;
$fid = isset($_GET['fid']) ? intval($_GET['fid']) : 0;
if ($tid < 1 && $fid < 1 || $tid > 0 && $fid > 0)
	message(__('Bad request'));


// Fetch some info about the topic and/or the forum
if ($tid)
{
	$query = array(
		'SELECT'	=> 'f.id, f.forum_name, f.moderators, f.redirect_url, fp.post_replies, fp.post_topics, t.subject, t.closed, s.user_id AS is_subscribed',
		'FROM'		=> 'topics AS t',
		'JOINS'		=> array(
			array(
				'INNER JOIN'	=> 'forums AS f',
				'ON'			=> 'f.id=t.forum_id'
			),
			array(
				'LEFT JOIN'		=> 'forum_perms AS fp',
				'ON'			=> '(fp.forum_id=f.id AND fp.group_id='.user()->g_id.')'
			),
			array(
				'LEFT JOIN'		=> 'subscriptions AS s',
				'ON'			=> '(t.id=s.topic_id AND s.user_id='.user()->id.')'
			)
		),
		'WHERE'		=> '(fp.read_forum IS NULL OR fp.read_forum=1) AND t.id='.$tid
	);

	($hook = get_hook('po_qr_get_topic_forum_info')) ? eval($hook) : null;
}
else
{
	$query = array(
		'SELECT'	=> 'f.id, f.forum_name, f.moderators, f.redirect_url, fp.post_replies, fp.post_topics',
		'FROM'		=> 'forums AS f',
		'JOINS'		=> array(
			array(
				'LEFT JOIN'		=> 'forum_perms AS fp',
				'ON'			=> '(fp.forum_id=f.id AND fp.group_id='.user()->g_id.')'
			)
		),
		'WHERE'		=> '(fp.read_forum IS NULL OR fp.read_forum=1) AND f.id='.$fid
	);

	($hook = get_hook('po_qr_get_forum_info')) ? eval($hook) : null;
}

$result = db()->query_build($query) or error(__FILE__, __LINE__);
$cur_posting = db()->fetch_assoc($result);

if (!$cur_posting)
	message(__('Bad request'));

$is_subscribed = $tid && $cur_posting['is_subscribed'];

// Is someone trying to post into a redirect forum?
if ($cur_posting['redirect_url'] != '')
	message(__('Bad request'));

// Sort out who the moderators are and if we are currently a moderator (or an admin)
$mods_array = ($cur_posting['moderators'] != '') ? unserialize($cur_posting['moderators']) : array();
$forum_page['is_admmod'] = (user()->g_id == FORUM_ADMIN || (user()->g_moderator == '1' && array_key_exists(user()->username, $mods_array))) ? true : false;

($hook = get_hook('po_pre_permission_check')) ? eval($hook) : null;

// Do we have permission to post?
if ((($tid && (($cur_posting['post_replies'] == '' && user()->g_post_replies == '0') || $cur_posting['post_replies'] == '0')) ||
	($fid && (($cur_posting['post_topics'] == '' && user()->g_post_topics == '0') || $cur_posting['post_topics'] == '0')) ||
	(isset($cur_posting['closed']) && $cur_posting['closed'] == '1')) &&
	!$forum_page['is_admmod'])
	message(__('No permission'));


($hook = get_hook('po_posting_location_selected')) ? eval($hook) : null;

// Start with a clean slate
$errors = array();

// Did someone just hit "Submit" or "Preview"?
if (isset($_POST['form_sent']))
{
	($hook = get_hook('po_form_submitted')) ? eval($hook) : null;

	// Make sure form_user is correct
	if ((user()->is_guest && $_POST['form_user'] != 'Guest') ||
			(!user()->is_guest && $_POST['form_user'] != user()->username)) {
		message(__('Bad request'));
	}

	// Flood protection
	if (!isset($_POST['preview']) && user()->last_post != '' &&
			(time() - user()->last_post) < user()->g_post_flood && (time() - user()->last_post) >= 0) {
		$errors[] = sprintf(__('Flood', 'post'), user()->g_post_flood);
	}

	// If it's a new topic
	if ($fid)
	{
		$subject = forum_trim($_POST['req_subject']);

		if ($subject == '')
			$errors[] = __('No subject', 'post');
		else if (utf8_strlen($subject) > FORUM_SUBJECT_MAXIMUM_LENGTH)
			$errors[] = sprintf(__('Too long subject', 'post'), FORUM_SUBJECT_MAXIMUM_LENGTH);
		else if (config()->p_subject_all_caps == '0' && check_is_all_caps($subject) && !$forum_page['is_admmod'])
			$errors[] = __('All caps subject', 'post');
	}

	// If the user is logged in we get the username and e-mail from forum_user
	if (!user()->is_guest) {
		$username = user()->username;
		$email = user()->email;
	}
	// Otherwise it should be in $_POST
	else
	{
		$username = forum_trim($_POST['req_username']);
		$email = strtolower(forum_trim((config()->p_force_guest_email == '1') ? $_POST['req_email'] : $_POST['email']));

		// It's a guest, so we have to validate the username
		$errors = array_merge($errors, validate_username($username));

		if (config()->p_force_guest_email == '1' || $email != '')
		{
			if (!defined('FORUM_EMAIL_FUNCTIONS_LOADED'))
				require FORUM_ROOT.'include/email.php';

			if (!is_valid_email($email))
				$errors[] = __('Invalid e-mail', 'post');

			if (is_banned_email($email))
				$errors[] = __('Banned e-mail', 'profile');
		}
	}

	// If we're an administrator or moderator, make sure the CSRF token in $_POST is valid
	if (user()->is_admmod && (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== generate_form_token(get_current_url())))
		$errors[] = __('CSRF token mismatch', 'post');

	// Clean up message from POST
	$message = forum_linebreaks(forum_trim($_POST['req_message']));

	if (strlen($message) > FORUM_MAX_POSTSIZE_BYTES)
		$errors[] = sprintf(__('Too long message', 'post'), forum_number_format(strlen($message)), forum_number_format(FORUM_MAX_POSTSIZE_BYTES));
	else if (config()->p_message_all_caps == '0' && check_is_all_caps($message) && !$forum_page['is_admmod'])
		$errors[] = __('All caps message', 'post');

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
	$subscribe = isset($_POST['subscribe']) ? 1 : 0;

	$now = time();

	($hook = get_hook('po_end_validation')) ? eval($hook) : null;

	// Did everything go according to plan?
	if (empty($errors) && !isset($_POST['preview']))
	{
		// If it's a reply
		if ($tid)
		{
			$post_info = array(
				'is_guest'		=> user()->is_guest,
				'poster'		=> $username,
				'poster_id'		=> user()->id,	// Always 1 for guest posts
				'poster_email'	=> (user()->is_guest && $email != '') ? $email : null,	// Always null for non-guest posts
				'subject'		=> $cur_posting['subject'],
				'message'		=> $message,
				'hide_smilies'	=> $hide_smilies,
				'posted'		=> $now,
				'subscr_action'	=> (config()->o_subscriptions == '1' && $subscribe && !$is_subscribed) ?
					1 : ((config()->o_subscriptions == '1' && !$subscribe && $is_subscribed) ? 2 : 0),
				'topic_id'		=> $tid,
				'forum_id'		=> $cur_posting['id'],
				'update_user'	=> true,
				'update_unread'	=> true
			);

			($hook = get_hook('po_pre_add_post')) ? eval($hook) : null;
			add_post($post_info, $new_pid);
		}
		// If it's a new topic
		else if ($fid)
		{
			$post_info = array(
				'is_guest'		=> user()->is_guest,
				'poster'		=> $username,
				'poster_id'		=> user()->id,	// Always 1 for guest posts
				'poster_email'	=> (user()->is_guest && $email != '') ? $email : null,	// Always null for non-guest posts
				'subject'		=> $subject,
				'message'		=> $message,
				'hide_smilies'	=> $hide_smilies,
				'posted'		=> $now,
				'subscribe'		=> (config()->o_subscriptions == '1' && (isset($_POST['subscribe']) && $_POST['subscribe'] == '1')),
				'forum_id'		=> $fid,
				'forum_name'	=> $cur_posting['forum_name'],
				'update_user'	=> true,
				'update_unread'	=> true
			);

			($hook = get_hook('po_pre_add_topic')) ? eval($hook) : null;
			add_topic($post_info, $new_tid, $new_pid);
		}

		($hook = get_hook('po_pre_redirect')) ? eval($hook) : null;

		redirect(forum_link($forum_url['post'], $new_pid), __('Post redirect', 'post'));
	}
}


// Are we quoting someone?
if ($tid && isset($_GET['qid']))
{
	$qid = intval($_GET['qid']);
	if ($qid < 1)
		message(__('Bad request'));

	// Get the quote and quote poster
	$query = array(
		'SELECT'	=> 'p.poster, p.message',
		'FROM'		=> 'posts AS p',
		'WHERE'		=> 'id='.$qid.' AND topic_id='.$tid
	);

	($hook = get_hook('po_qr_get_quote')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);
	$quote_info = db()->fetch_assoc($result);

	if (!$quote_info)
	{
		message(__('Bad request'));
	}

	($hook = get_hook('po_modify_quote_info')) ? eval($hook) : null;

	if (config()->p_message_bbcode == '1')
	{
		// If username contains a square bracket, we add "" or '' around it (so we know when it starts and ends)
		if (strpos($quote_info['poster'], '[') !== false || strpos($quote_info['poster'], ']') !== false)
		{
			if (strpos($quote_info['poster'], '\'') !== false)
				$quote_info['poster'] = '"'.$quote_info['poster'].'"';
			else
				$quote_info['poster'] = '\''.$quote_info['poster'].'\'';
		}
		else
		{
			// Get the characters at the start and end of $q_poster
			$ends = utf8_substr($quote_info['poster'], 0, 1).utf8_substr($quote_info['poster'], -1, 1);

			// Deal with quoting "Username" or 'Username' (becomes '"Username"' or "'Username'")
			if ($ends == '\'\'')
				$quote_info['poster'] = '"'.$quote_info['poster'].'"';
			else if ($ends == '""')
				$quote_info['poster'] = '\''.$quote_info['poster'].'\'';
		}

		$forum_page['quote'] = '[quote='.$quote_info['poster'].']'.$quote_info['message'].'[/quote]'."\n";
	}
	else
		$forum_page['quote'] = '> '.$quote_info['poster'].' '.__('wrote').':'."\n\n".'> '.$quote_info['message']."\n";
}


// Setup form
$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;
$forum_page['form_action'] = ($tid ? forum_link($forum_url['new_reply'], $tid) : forum_link($forum_url['new_topic'], $fid));
$forum_page['form_attributes'] = array();

$forum_page['hidden_fields'] = array(
	'form_sent'		=> '<input type="hidden" name="form_sent" value="1" />',
	'form_user'		=> '<input type="hidden" name="form_user" value="'.((!user()->is_guest) ? forum_htmlencode(user()->username) : 'Guest').'" />',
	'csrf_token'	=> '<input type="hidden" name="csrf_token" value="'.generate_form_token($forum_page['form_action']).'" />'
);

// Setup help
$forum_page['text_options'] = array();
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
$forum_page['crumbs'][] = array(config()->o_board_title, forum_link($forum_url['index']));
$forum_page['crumbs'][] = array($cur_posting['forum_name'], forum_link($forum_url['forum'], array($cur_posting['id'], sef_friendly($cur_posting['forum_name']))));
if ($tid)
	$forum_page['crumbs'][] = array($cur_posting['subject'], forum_link($forum_url['topic'], array($tid, sef_friendly($cur_posting['subject']))));
$forum_page['crumbs'][] = $tid ?
	__('Post reply', 'post') : __('Post new topic', 'post');

($hook = get_hook('po_pre_header_load')) ? eval($hook) : null;

define('FORUM_PAGE', 'post');

// Check if the topic review is to be displayed
if ($tid && config()->o_topic_review != '0') {
	if (!defined('FORUM_PARSER_LOADED')) {
		require FORUM_ROOT.'include/parser.php';
	}

	// Get the amount of posts in the topic
	$query = array(
		'SELECT'	=> 'count(p.id)',
		'FROM'		=> 'posts AS p',
		'WHERE'		=> 'topic_id='.$tid
	);

	($hook = get_hook('po_topic_review_qr_get_post_count')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);
	$forum_page['total_post_count'] = db()->result($result, 0);

	// Get posts to display in topic review
	$query = array(
		'SELECT'	=> 'p.id, p.poster, p.message, p.hide_smilies, p.posted',
		'FROM'		=> 'posts AS p',
		'WHERE'		=> 'topic_id='.$tid,
		'ORDER BY'	=> 'id DESC',
		'LIMIT'		=> config()->o_topic_review
	);

	($hook = get_hook('po_topic_review_qr_get_topic_review_posts')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);

	$posts = array();
	while ($cur_post = db()->fetch_assoc($result)) {
		$posts[] = $cur_post;
	}
}

$forum_main_view = 'post/main';
template()->render($forum_layout);
