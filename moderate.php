<?php
/**
 * Provides various mass-moderation tools to moderators.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */
namespace punbb;

require __DIR__ . '/vendor/autoload.php';

($hook = get_hook('mr_start')) ? eval($hook) : null;

// This particular function doesn't require forum-based moderator access. It can be used
// by all moderators and admins.
if (isset($_GET['get_host']))
{
	if (!user()->is_admmod) {
		message(__('No permission'));
	}

	$_get_host = $_GET['get_host'];
	if (!is_string($_get_host))
		message(__('Bad request'));

	($hook = get_hook('mr_view_ip_selected')) ? eval($hook) : null;

	// Is get_host an IP address or a post ID?
	if (preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $_get_host) || preg_match('/^((([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}:[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){0,5}:((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(::([0-9A-Fa-f]{1,4}:){0,5}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|([0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}[0-9A-Fa-f]{1,4})|(::([0-9A-Fa-f]{1,4}:){0,6}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){1,7}:))$/', $_get_host))
		$ip = $_get_host;
	else
	{
		$get_host = intval($_get_host);
		if ($get_host < 1)
			message(__('Bad request'));

		$query = array(
			'SELECT'	=> 'p.poster_ip',
			'FROM'		=> 'posts AS p',
			'WHERE'		=> 'p.id='.$get_host
		);

		($hook = get_hook('mr_view_ip_qr_get_poster_ip')) ? eval($hook) : null;
		$result = db()->query_build($query) or error(__FILE__, __LINE__);
		$ip = db()->result($result);

		if (!$ip)
			message(__('Bad request'));
	}

	($hook = get_hook('mr_view_ip_pre_output')) ? eval($hook) : null;

	message(sprintf(__('Hostname lookup', 'misc'), $ip, @gethostbyaddr($ip), '<a href="'.link('admin_users').'?show_users='.$ip.'">'.
		__('Show more users', 'misc') . '</a>'));
}


// All other functions require moderator/admin access
$fid = isset($_GET['fid']) ? intval($_GET['fid']) : 0;
if ($fid < 1)
	message(__('Bad request'));

// Get some info about the forum we're moderating
$query = array(
	'SELECT'	=> 'f.forum_name, f.redirect_url, f.num_topics, f.moderators, f.sort_by',
	'FROM'		=> 'forums AS f',
	'JOINS'		=> array(
		array(
			'LEFT JOIN'		=> 'forum_perms AS fp',
			'ON'			=> '(fp.forum_id=f.id AND fp.group_id='.user()->g_id.')'
		)
	),
	'WHERE'		=> '(fp.read_forum IS NULL OR fp.read_forum=1) AND f.id='.$fid
);

($hook = get_hook('mr_qr_get_forum_data')) ? eval($hook) : null;
$result = db()->query_build($query) or error(__FILE__, __LINE__);
$cur_forum = db()->fetch_assoc($result);

if (!$cur_forum)
	message(__('Bad request'));

// Make sure we're not trying to moderate a redirect forum
if ($cur_forum['redirect_url'] != '')
	message(__('Bad request'));

// Setup the array of moderators
$mods_array = ($cur_forum['moderators'] != '') ? unserialize($cur_forum['moderators']) : array();

($hook = get_hook('mr_pre_permission_check')) ? eval($hook) : null;

if (user()->g_id != FORUM_ADMIN && (user()->g_moderator != '1' ||
	!array_key_exists(user()->username, $mods_array))) {
	message(__('No permission'));
}

// Get topic/forum tracking data
if (!user()->is_guest) {
	$tracked_topics = get_tracked_topics();
}

// Did someone click a cancel button?
if (isset($_POST['cancel']))
	redirect(link('forum', array($fid, sef_friendly($cur_forum['forum_name']))),
		__('Cancel redirect'));

// All topic moderation features require a topic id in GET
if (isset($_GET['tid']))
{
	($hook = get_hook('mr_post_actions_selected')) ? eval($hook) : null;

	$tid = intval($_GET['tid']);
	if ($tid < 1)
		message(__('Bad request'));

	// Fetch some info about the topic
	$query = array(
		'SELECT'	=> 't.subject, t.poster, t.first_post_id, t.posted, t.num_replies',
		'FROM'		=> 'topics AS t',
		'WHERE'		=> 't.id='.$tid.' AND t.moved_to IS NULL'
	);

	($hook = get_hook('mr_post_actions_qr_get_topic_info')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);
	$cur_topic = db()->fetch_assoc($result);

	if (!$cur_topic)
		message(__('Bad request'));

	// User pressed the cancel button
	if (isset($_POST['delete_posts_cancel']))
		redirect(link('topic', array($tid, sef_friendly($cur_topic['subject']))),
			__('Cancel redirect'));

	// Delete one or more posts
	if (isset($_POST['delete_posts']) || isset($_POST['delete_posts_comply']))
	{
		($hook = get_hook('mr_delete_posts_form_submitted')) ? eval($hook) : null;

		$posts = isset($_POST['posts']) && !empty($_POST['posts']) ? $_POST['posts'] : array();
		$posts = array_map('intval', (is_array($posts) ? $posts : explode(',', $posts)));

		if (empty($posts))
			message(__('No posts selected', 'misc'));

		if (isset($_POST['delete_posts_comply']))
		{
			if (!isset($_POST['req_confirm']))
				redirect(link('topic', array($tid, sef_friendly($cur_topic['subject']))),
					__('No confirm redirect'));

			($hook = get_hook('mr_confirm_delete_posts_form_submitted')) ? eval($hook) : null;

			// Verify that the post IDs are valid
			$query = array(
				'SELECT'	=> 'COUNT(p.id)',
				'FROM'		=> 'posts AS p',
				'WHERE'		=> 'p.id IN('.implode(',', $posts).') AND p.id!='.$cur_topic['first_post_id'].' AND p.topic_id='.$tid
			);

			($hook = get_hook('mr_confirm_delete_posts_qr_verify_post_ids')) ? eval($hook) : null;
			$result = db()->query_build($query) or error(__FILE__, __LINE__);
			if (db()->result($result) != count($posts))
				message(__('Bad request'));

			// Delete the posts
			$query = array(
				'DELETE'	=> 'posts',
				'WHERE'		=> 'id IN('.implode(',', $posts).')'
			);

			($hook = get_hook('mr_confirm_delete_posts_qr_delete_posts')) ? eval($hook) : null;
			db()->query_build($query) or error(__FILE__, __LINE__);

			if (!defined('FORUM_SEARCH_IDX_FUNCTIONS_LOADED'))
				require FORUM_ROOT.'include/search_idx.php';

			strip_search_index($posts);

			sync_topic($tid);
			sync_forum($fid);

			flash()->add_info(__('Delete posts redirect', 'misc'));

			($hook = get_hook('mr_confirm_delete_posts_pre_redirect')) ? eval($hook) : null;

			redirect(link('topic', array($tid, sef_friendly($cur_topic['subject']))),
				__('Delete posts redirect', 'misc'));
		}

		// Setup form
		$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;
		$forum_page['form_action'] = link('moderate_topic', array($fid, $tid));

		$forum_page['hidden_fields'] = array(
			'csrf_token'	=> '<input type="hidden" name="csrf_token" value="'.generate_form_token($forum_page['form_action']).'" />',
			'posts'			=> '<input type="hidden" name="posts" value="'.implode(',', $posts).'" />'
		);

		// Setup breadcrumbs
		$forum_page['crumbs'] = array(
			array(config()->o_board_title, link('index')),
			array($cur_forum['forum_name'], link('forum', array($fid, sef_friendly($cur_forum['forum_name'])))),
			array($cur_topic['subject'], link('topic', array($tid, sef_friendly($cur_topic['subject'])))),
			__('Delete posts', 'misc')
		);

		($hook = get_hook('mr_confirm_delete_posts_pre_header_load')) ? eval($hook) : null;

		define('FORUM_PAGE', 'dialogue');

		template()->render([
			'main_view' => 'moderate/dialogue'
		]);
	}
	else if (isset($_POST['split_posts']) || isset($_POST['split_posts_comply']))
	{
		($hook = get_hook('mr_split_posts_form_submitted')) ? eval($hook) : null;

		$posts = isset($_POST['posts']) && !empty($_POST['posts']) ? $_POST['posts'] : array();
		$posts = array_map('intval', (is_array($posts) ? $posts : explode(',', $posts)));

		if (empty($posts))
			message(__('No posts selected', 'misc'));

		if (isset($_POST['split_posts_comply']))
		{
			if (!isset($_POST['req_confirm']))
				redirect(link('topic', array($tid, sef_friendly($cur_topic['subject']))),
					__('No confirm redirect'));

			($hook = get_hook('mr_confirm_split_posts_form_submitted')) ? eval($hook) : null;

			// Verify that the post IDs are valid
			$query = array(
				'SELECT'	=> 'COUNT(p.id)',
				'FROM'		=> 'posts AS p',
				'WHERE'		=> 'p.id IN('.implode(',', $posts).') AND p.id!='.$cur_topic['first_post_id'].' AND p.topic_id='.$tid
			);

			($hook = get_hook('mr_confirm_split_posts_qr_verify_post_ids')) ? eval($hook) : null;
			$result = db()->query_build($query) or error(__FILE__, __LINE__);
			if (db()->result($result) != count($posts))
				message(__('Bad request'));

			$new_subject = isset($_POST['new_subject']) ? forum_trim($_POST['new_subject']) : '';

			if ($new_subject == '')
				message(__('No subject', 'post'));
			else if (utf8_strlen($new_subject) > FORUM_SUBJECT_MAXIMUM_LENGTH)
				message(sprintf(__('Too long subject', 'post'), FORUM_SUBJECT_MAXIMUM_LENGTH));

			// Get data from the new first post
			$query = array(
				'SELECT'	=> 'p.id, p.poster, p.posted',
				'FROM'		=> 'posts AS p',
				'WHERE'		=> 'p.id = '.min($posts)
			);

			($hook = get_hook('mr_confirm_split_posts_qr_get_first_post_data')) ? eval($hook) : null;
			$result = db()->query_build($query) or error(__FILE__, __LINE__);
			$first_post_data = db()->fetch_assoc($result);

			// Create the new topic
			$query = array(
				'INSERT'	=> 'poster, subject, posted, first_post_id, forum_id',
				'INTO'		=> 'topics',
				'VALUES'	=> '\''.db()->escape($first_post_data['poster']).'\', \''.db()->escape($new_subject).'\', '.$first_post_data['posted'].', '.$first_post_data['id'].', '.$fid
			);

			($hook = get_hook('mr_confirm_split_posts_qr_add_topic')) ? eval($hook) : null;
			db()->query_build($query) or error(__FILE__, __LINE__);
			$new_tid = db()->insert_id();

			// Move the posts to the new topic
			$query = array(
				'UPDATE'	=> 'posts',
				'SET'		=> 'topic_id='.$new_tid,
				'WHERE'		=> 'id IN('.implode(',', $posts).')'
			);

			($hook = get_hook('mr_confirm_split_posts_qr_move_posts')) ? eval($hook) : null;
			db()->query_build($query) or error(__FILE__, __LINE__);

			// Sync last post data for the old topic, the new topic, and the forum itself
			sync_topic($new_tid);
			sync_topic($tid);
			sync_forum($fid);

			flash()->add_info(__('Split posts redirect', 'misc'));

			($hook = get_hook('mr_confirm_split_posts_pre_redirect')) ? eval($hook) : null;

			redirect(link('topic', array($new_tid, sef_friendly($new_subject))),
				__('Split posts redirect', 'misc'));
		}

		// Setup form
		$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;
		$forum_page['form_action'] = link('moderate_topic', array($fid, $tid));

		$forum_page['hidden_fields'] = array(
			'csrf_token'	=> '<input type="hidden" name="csrf_token" value="'.generate_form_token($forum_page['form_action']).'" />',
			'posts'			=> '<input type="hidden" name="posts" value="'.implode(',', $posts).'" />'
		);

		// Setup breadcrumbs
		$forum_page['crumbs'] = array(
			array(config()->o_board_title, link('index')),
			array($cur_forum['forum_name'], link('forum', array($fid, sef_friendly($cur_forum['forum_name'])))),
			array($cur_topic['subject'], link('topic', array($tid, sef_friendly($cur_topic['subject'])))),
			__('Split posts', 'misc')
		);

		($hook = get_hook('mr_confirm_split_posts_pre_header_load')) ? eval($hook) : null;

		define('FORUM_PAGE', 'dialogue');

		template()->render([
			'main_view' => 'moderate/dialogue2'
		]);
	}


	// Show the moderate topic view

	// Used to disable the Split and Delete buttons if there are no replies to this topic
	$forum_page['button_status'] = ($cur_topic['num_replies'] == 0) ? ' disabled="disabled"' : '';


	// Determine the post offset (based on $_GET['p'])
	$forum_page['num_pages'] = ceil(($cur_topic['num_replies'] + 1) / user()->disp_posts);
	$forum_page['page'] = (!isset($_GET['p']) || !is_numeric($_GET['p']) || $_GET['p'] <= 1 || $_GET['p'] > $forum_page['num_pages']) ? 1 : intval($_GET['p']);
	$forum_page['start_from'] = user()->disp_posts * ($forum_page['page'] - 1);
	$forum_page['finish_at'] = min(($forum_page['start_from'] + user()->disp_posts), ($cur_topic['num_replies'] + 1));
	$forum_page['items_info'] = generate_items_info(__('Posts', 'misc'), ($forum_page['start_from'] + 1), ($cur_topic['num_replies'] + 1));

	// Generate paging links
	$forum_page['page_post']['paging'] = '<p class="paging"><span class="pages">'.
		__('Pages') . '</span> '.paginate($forum_page['num_pages'], $forum_page['page'], $forum_url['moderate_topic'],
		__('Paging separator'), array($fid, $tid)).'</p>';

	// Navigation links for header and page numbering for title/meta description
	if ($forum_page['page'] < $forum_page['num_pages'])
	{
		$forum_page['nav']['last'] = '<link rel="last" href="'.forum_sublink('moderate_topic', $forum_url['page'], $forum_page['num_pages'], array($fid, $tid)).'" title="'.
			__('Page') . ' ' . $forum_page['num_pages'].'" />';
		$forum_page['nav']['next'] = '<link rel="next" href="'.forum_sublink('moderate_topic', $forum_url['page'], ($forum_page['page'] + 1), array($fid, $tid)).'" title="'.
			__('Page') . ' ' . ($forum_page['page'] + 1).'" />';
	}
	if ($forum_page['page'] > 1)
	{
		$forum_page['nav']['prev'] = '<link rel="prev" href="'.forum_sublink('moderate_topic', $forum_url['page'], ($forum_page['page'] - 1), array($fid, $tid)).'" title="'.
		__('Page') . ' ' . ($forum_page['page'] - 1).'" />';
		$forum_page['nav']['first'] = '<link rel="first" href="'.link('moderate_topic', array($fid, $tid)).'" title="'.
		__('Page').' 1" />';
	}

	if (config()->o_censoring == '1')
		$cur_topic['subject'] = censor_words($cur_topic['subject']);

	// Setup form
	$forum_page['form_action'] = link('moderate_topic', array($fid, $tid));

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array(config()->o_board_title, link('index')),
		array($cur_forum['forum_name'], link('forum', array($fid, sef_friendly($cur_forum['forum_name'])))),
		array($cur_topic['subject'], link('topic', array($tid, sef_friendly($cur_topic['subject'])))),
		__('Moderate topic', 'topic')
	);

	$forum_page['main_head_options']['select_all'] = '<span '.(empty($forum_page['main_head_options']) ? ' class="first-item"' : '').'><span class="select-all js_link" data-check-form="mr-post-actions-form">'.
		__('Select all', 'misc') . '</span></span>';
	$forum_page['main_foot_options']['select_all'] = '<span '.(empty($forum_page['main_foot_options']) ? ' class="first-item"' : '').'><span class="select-all js_link" data-check-form="mr-post-actions-form">'.
		__('Select all', 'misc') . '</span></span>';

	if ($forum_page['num_pages'] > 1)
		$forum_page['main_head_pages'] = sprintf(__('Page info'), $forum_page['page'], $forum_page['num_pages']);

	($hook = get_hook('mr_post_actions_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE', 'modtopic');

	if (!defined('FORUM_PARSER_LOADED'))
		require FORUM_ROOT.'include/parser.php';

	// Retrieve the posts (and their respective poster)
	$query = array(
		'SELECT'	=> 'u.title, u.num_posts, g.g_id, g.g_user_title, p.id, p.poster, p.poster_id, p.message, p.hide_smilies, p.posted, p.edited, p.edited_by',
		'FROM'		=> 'posts AS p',
		'JOINS'		=> array(
			array(
				'INNER JOIN'	=> 'users AS u',
				'ON'			=> 'u.id=p.poster_id'
			),
			array(
				'INNER JOIN'	=> 'groups AS g',
				'ON'			=> 'g.g_id=u.group_id'
			)
		),
		'WHERE'		=> 'p.topic_id='.$tid,
		'ORDER BY'	=> 'p.id',
		'LIMIT'		=> $forum_page['start_from'].','.user()->disp_posts
	);

	($hook = get_hook('mr_post_actions_qr_get_posts')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);

	template()->render([
		'main_view' => 'moderate/modtopic',
		'main_title' => sprintf(__('Moderate topic head', 'misc'), forum_htmlencode($cur_topic['subject']))
	]);
}


// Move one or more topics
if (isset($_REQUEST['move_topics']) || isset($_POST['move_topics_to']))
{
	if (isset($_POST['move_topics_to']))
	{
		($hook = get_hook('mr_confirm_move_topics_form_submitted')) ? eval($hook) : null;

		$topics = isset($_POST['topics']) && !empty($_POST['topics']) ? explode(',', $_POST['topics']) : array();
		$topics = array_map('intval', $topics);

		$move_to_forum = isset($_POST['move_to_forum']) ? intval($_POST['move_to_forum']) : 0;
		if (empty($topics) || $move_to_forum < 1)
			message(__('Bad request'));

		// Fetch the forum name for the forum we're moving to
		$query = array(
			'SELECT'	=> 'f.forum_name',
			'FROM'		=> 'forums AS f',
			'WHERE'		=> 'f.id='.$move_to_forum
		);

		($hook = get_hook('mr_confirm_move_topics_qr_get_move_to_forum_name')) ? eval($hook) : null;
		$result = db()->query_build($query) or error(__FILE__, __LINE__);

		$move_to_forum_name = db()->result($result);

		if (!$move_to_forum_name)
			message(__('Bad request'));

		// Verify that the topic IDs are valid
		$query = array(
			'SELECT'	=> 'COUNT(t.id)',
			'FROM'		=> 'topics AS t',
			'WHERE'		=> 't.id IN('.implode(',', $topics).') AND t.forum_id='.$fid
		);

		($hook = get_hook('mr_confirm_move_topics_qr_verify_topic_ids')) ? eval($hook) : null;
		$result = db()->query_build($query) or error(__FILE__, __LINE__);
		if (db()->result($result) != count($topics))
			message(__('Bad request'));

		// Delete any redirect topics if there are any (only if we moved/copied the topic back to where it where it was once moved from)
		$query = array(
			'DELETE'	=> 'topics',
			'WHERE'		=> 'forum_id='.$move_to_forum.' AND moved_to IN('.implode(',', $topics).')'
		);

		($hook = get_hook('mr_confirm_move_topics_qr_delete_redirect_topics')) ? eval($hook) : null;
		db()->query_build($query) or error(__FILE__, __LINE__);

		// Move the topic(s)
		$query = array(
			'UPDATE'	=> 'topics',
			'SET'		=> 'forum_id='.$move_to_forum,
			'WHERE'		=> 'id IN('.implode(',', $topics).')'
		);

		($hook = get_hook('mr_confirm_move_topics_qr_move_topics')) ? eval($hook) : null;
		db()->query_build($query) or error(__FILE__, __LINE__);

		// Should we create redirect topics?
		if (isset($_POST['with_redirect']))
		{
			foreach ($topics as $cur_topic)
			{
				// Fetch info for the redirect topic
				$query = array(
					'SELECT'	=> 't.poster, t.subject, t.posted, t.last_post',
					'FROM'		=> 'topics AS t',
					'WHERE'		=> 't.id='.$cur_topic
				);

				($hook = get_hook('mr_confirm_move_topics_qr_get_redirect_topic_data')) ? eval($hook) : null;
				$result = db()->query_build($query) or error(__FILE__, __LINE__);
				$moved_to = db()->fetch_assoc($result);

				// Create the redirect topic
				$query = array(
					'INSERT'	=> 'poster, subject, posted, last_post, moved_to, forum_id',
					'INTO'		=> 'topics',
					'VALUES'	=> '\''.db()->escape($moved_to['poster']).'\', \''.db()->escape($moved_to['subject']).'\', '.$moved_to['posted'].', '.$moved_to['last_post'].', '.$cur_topic.', '.$fid
				);

				($hook = get_hook('mr_confirm_move_topics_qr_add_redirect_topic')) ? eval($hook) : null;
				db()->query_build($query) or error(__FILE__, __LINE__);
			}
		}

		sync_forum($fid);			// Synchronize the forum FROM which the topic was moved
		sync_forum($move_to_forum);	// Synchronize the forum TO which the topic was moved

		$forum_page['redirect_msg'] = (count($topics) > 1) ?
			__('Move topics redirect', 'misc') : __('Move topic redirect', 'misc');

		flash()->add_info($forum_page['redirect_msg']);

		($hook = get_hook('mr_confirm_move_topics_pre_redirect')) ? eval($hook) : null;

		redirect(link('forum', array($move_to_forum, sef_friendly($move_to_forum_name))), $forum_page['redirect_msg']);
	}

	if (isset($_POST['move_topics']))
	{
		$topics = isset($_POST['topics']) && is_array($_POST['topics']) ? $_POST['topics'] : array();
		$topics = array_map('intval', $topics);

		if (empty($topics))
			message(__('No topics selected', 'misc'));

		if (count($topics) == 1)
		{
			$topics = $topics[0];
			$action = 'single';
		}
		else
			$action = 'multiple';
	}
	else
	{
		$topics = intval($_GET['move_topics']);
		if ($topics < 1)
			message(__('Bad request'));

		$action = 'single';
	}
	if ($action == 'single')
	{
		// Fetch the topic subject
		$query = array(
			'SELECT'	=> 't.subject',
			'FROM'		=> 'topics AS t',
			'WHERE'		=> 't.id='.$topics
		);

		($hook = get_hook('mr_move_topics_qr_get_topic_to_move_subject')) ? eval($hook) : null;
		$result = db()->query_build($query) or error(__FILE__, __LINE__);
		$subject = db()->result($result);

		if (!$subject)
		{
			message(__('Bad request'));
		}
	}

	// Get forums we can move the post into
	$query = array(
		'SELECT'	=> 'c.id AS cid, c.cat_name, f.id AS fid, f.forum_name',
		'FROM'		=> 'categories AS c',
		'JOINS'		=> array(
			array(
				'INNER JOIN'	=> 'forums AS f',
				'ON'			=> 'c.id=f.cat_id'
			),
			array(
				'LEFT JOIN'		=> 'forum_perms AS fp',
				'ON'			=> '(fp.forum_id=f.id AND fp.group_id='.user()->g_id.')'
			)
		),
		'WHERE'		=> '(fp.read_forum IS NULL OR fp.read_forum=1) AND f.redirect_url IS NULL AND f.id!='.$fid,
		'ORDER BY'	=> 'c.disp_position, c.id, f.disp_position'
	);

	($hook = get_hook('mr_move_topics_qr_get_target_forums')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);

	$forum_list = array();
	while ($cur_sel_forum = db()->fetch_assoc($result))
	{
		$forum_list[] = $cur_sel_forum;
	}

	if (empty($forum_list))
	{
		message(__('Nowhere to move', 'misc'));
	}


	// Setup form
	$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;
	$forum_page['form_action'] = link('moderate_forum', $fid);

	$forum_page['hidden_fields'] = array(
		'csrf_token'	=> '<input type="hidden" name="csrf_token" value="'.generate_form_token($forum_page['form_action']).'" />',
		'topics'		=> '<input type="hidden" name="topics" value="'.($action == 'single' ? $topics : implode(',', $topics)).'" />'
	);

	// Setup breadcrumbs
	$forum_page['crumbs'][] = array(config()->o_board_title, link('index'));
	$forum_page['crumbs'][] = array($cur_forum['forum_name'], link('forum', array($fid, sef_friendly($cur_forum['forum_name']))));
	if ($action == 'single')
		$forum_page['crumbs'][] = array($subject, link('topic', array($topics, sef_friendly($subject))));
	else
		$forum_page['crumbs'][] = array(__('Moderate forum', 'misc'), link('moderate_forum', $fid));
	$forum_page['crumbs'][] = ($action == 'single') ?
		__('Move topic', 'misc') : __('Move topics', 'misc');

	($hook = get_hook('mr_move_topics_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE', 'dialogue');

	template()->render([
		'main_view' => 'moderate/dialogue3',
		'main_title' => end($forum_page['crumbs']) . ' ' . __('To new forum', 'misc')
	]);
}
// Merge topics
else if (isset($_POST['merge_topics']) || isset($_POST['merge_topics_comply']))
{
	$topics = isset($_POST['topics']) && !empty($_POST['topics']) ? $_POST['topics'] : array();
	$topics = array_map('intval', (is_array($topics) ? $topics : explode(',', $topics)));

	if (empty($topics))
		message(__('No topics selected', 'misc'));

	if (count($topics) == 1)
		message(__('Merge error', 'misc'));

	if (isset($_POST['merge_topics_comply']))
	{
		($hook = get_hook('mr_confirm_merge_topics_form_submitted')) ? eval($hook) : null;

		// Verify that the topic IDs are valid
		$query = array(
			'SELECT'	=> 'COUNT(t.id), MIN(t.id)',
			'FROM'		=> 'topics AS t',
			'WHERE'		=> 't.id IN('.implode(',', $topics).') AND t.moved_to IS NULL AND t.forum_id='.$fid
		);

		($hook = get_hook('mr_confirm_merge_topics_qr_verify_topic_ids')) ? eval($hook) : null;
		$result = db()->query_build($query) or error(__FILE__, __LINE__);
		list($num_topics, $merge_to_tid) = db()->fetch_row($result);
		if ($num_topics != count($topics))
			message(__('Bad request'));

		// Make any redirect topics point to our new, merged topic
		$query = array(
			'UPDATE'	=> 'topics',
			'SET'		=> 'moved_to='.$merge_to_tid,
			'WHERE'		=> 'moved_to IN('.implode(',', $topics).')'
		);

		// Should we create redirect topics?
		if (isset($_POST['with_redirect']))
			$query['WHERE'] .= ' OR (id IN('.implode(',', $topics).') AND id != '.$merge_to_tid.')';

		($hook = get_hook('mr_confirm_merge_topics_qr_fix_redirect_topics')) ? eval($hook) : null;
		db()->query_build($query) or error(__FILE__, __LINE__);

		// Merge the posts into the topic
		$query = array(
			'UPDATE'	=> 'posts',
			'SET'		=> 'topic_id='.$merge_to_tid,
			'WHERE'		=> 'topic_id IN('.implode(',', $topics).')'
		);

		($hook = get_hook('mr_confirm_merge_topics_qr_merge_posts')) ? eval($hook) : null;
		db()->query_build($query) or error(__FILE__, __LINE__);

		// Delete any subscriptions
		$query = array(
			'DELETE'	=> 'subscriptions',
			'WHERE'		=> 'topic_id IN('.implode(',', $topics).') AND topic_id != '.$merge_to_tid
		);

		($hook = get_hook('mr_confirm_merge_topics_qr_delete_subscriptions')) ? eval($hook) : null;
		db()->query_build($query) or error(__FILE__, __LINE__);

		if (!isset($_POST['with_redirect']))
		{
			// Delete the topics that have been merged
			$query = array(
				'DELETE'	=> 'topics',
				'WHERE'		=> 'id IN('.implode(',', $topics).') AND id != '.$merge_to_tid
			);

			($hook = get_hook('mr_confirm_merge_topics_qr_delete_merged_topics')) ? eval($hook) : null;
			db()->query_build($query) or error(__FILE__, __LINE__);
		}

		// Synchronize the topic we merged to and the forum where the topics were merged
		sync_topic($merge_to_tid);
		sync_forum($fid);

		flash()->add_info(__('Merge topics redirect', 'misc'));

		($hook = get_hook('mr_confirm_merge_topics_pre_redirect')) ? eval($hook) : null;

		redirect(link('forum', array($fid, sef_friendly($cur_forum['forum_name']))),
			__('Merge topics redirect', 'misc'));
	}

	// Setup form
	$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;
	$forum_page['form_action'] = link('moderate_forum', $fid);

	$forum_page['hidden_fields'] = array(
		'csrf_token'	=> '<input type="hidden" name="csrf_token" value="'.generate_form_token($forum_page['form_action']).'" />',
		'topics'		=> '<input type="hidden" name="topics" value="'.implode(',', $topics).'" />'
	);

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array(config()->o_board_title, link('index')),
		array($cur_forum['forum_name'], link('forum', array($fid, sef_friendly($cur_forum['forum_name'])))),
		array(__('Moderate forum', 'misc'), link('moderate_forum', $fid)),
		__('Merge topics', 'misc')
	);

	($hook = get_hook('mr_merge_topics_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE', 'dialogue');

	template()->render([
		'main_view' => 'moderate/dialogue4'
	]);
}


// Delete one or more topics
else if (isset($_REQUEST['delete_topics']) || isset($_POST['delete_topics_comply']))
{
	$topics = isset($_POST['topics']) && !empty($_POST['topics']) ? $_POST['topics'] : array();
	$topics = array_map('intval', (is_array($topics) ? $topics : explode(',', $topics)));

	if (empty($topics))
		message(__('No topics selected', 'misc'));

	$multi = count($topics) > 1;
	if (isset($_POST['delete_topics_comply']))
	{
		if (!isset($_POST['req_confirm']))
			redirect(link('forum', array($fid, sef_friendly($cur_forum['forum_name']))),
				__('Cancel redirect'));

		($hook = get_hook('mr_confirm_delete_topics_form_submitted')) ? eval($hook) : null;

		// Verify that the topic IDs are valid
		$query = array(
			'SELECT'	=> 'COUNT(t.id)',
			'FROM'		=> 'topics AS t',
			'WHERE'		=> 't.id IN('.implode(',', $topics).') AND t.forum_id='.$fid
		);

		($hook = get_hook('mr_confirm_delete_topics_qr_verify_topic_ids')) ? eval($hook) : null;
		$result = db()->query_build($query) or error(__FILE__, __LINE__);
		if (db()->result($result) != count($topics))
			message(__('Bad request'));

		// Create an array of forum IDs that need to be synced
		$forum_ids = array($fid);
		$query = array(
			'SELECT'	=> 't.forum_id',
			'FROM'		=> 'topics AS t',
			'WHERE'		=> 't.moved_to IN('.implode(',', $topics).')'
		);

		($hook = get_hook('mr_confirm_delete_topics_qr_get_forums_to_sync')) ? eval($hook) : null;
		$result = db()->query_build($query) or error(__FILE__, __LINE__);
		while ($row = db()->fetch_row($result))
			$forum_ids[] = $row[0];

		// Delete the topics and any redirect topics
		$query = array(
			'DELETE'	=> 'topics',
			'WHERE'		=> 'id IN('.implode(',', $topics).') OR moved_to IN('.implode(',', $topics).')'
		);

		($hook = get_hook('mr_confirm_delete_topics_qr_delete_topics')) ? eval($hook) : null;
		db()->query_build($query) or error(__FILE__, __LINE__);

		// Delete any subscriptions
		$query = array(
			'DELETE'	=> 'subscriptions',
			'WHERE'		=> 'topic_id IN('.implode(',', $topics).')'
		);

		($hook = get_hook('mr_confirm_delete_topics_qr_delete_subscriptions')) ? eval($hook) : null;
		db()->query_build($query) or error(__FILE__, __LINE__);

		// Create a list of the post ID's in the deleted topic and strip the search index
		$query = array(
			'SELECT'	=> 'p.id',
			'FROM'		=> 'posts AS p',
			'WHERE'		=> 'p.topic_id IN('.implode(',', $topics).')'
		);

		($hook = get_hook('mr_confirm_delete_topics_qr_get_deleted_posts')) ? eval($hook) : null;
		$result = db()->query_build($query) or error(__FILE__, __LINE__);

		$post_ids = array();
		while ($row = db()->fetch_row($result))
			$post_ids[] = $row[0];

		// Strip the search index provided we're not just deleting redirect topics
		if (!empty($post_ids))
		{
			if (!defined('FORUM_SEARCH_IDX_FUNCTIONS_LOADED'))
				require FORUM_ROOT.'include/search_idx.php';

			strip_search_index($post_ids);
		}

		// Delete posts
		$query = array(
			'DELETE'	=> 'posts',
			'WHERE'		=> 'topic_id IN('.implode(',', $topics).')'
		);

		($hook = get_hook('mr_confirm_delete_topics_qr_delete_topic_posts')) ? eval($hook) : null;
		db()->query_build($query) or error(__FILE__, __LINE__);

		foreach ($forum_ids as $cur_forum_id)
			sync_forum($cur_forum_id);

		flash()->add_info($multi ?
			__('Delete topics redirect', 'misc') :
			__('Delete topic redirect', 'misc'));

		($hook = get_hook('mr_confirm_delete_topics_pre_redirect')) ? eval($hook) : null;

		redirect(link('forum', array($fid, sef_friendly($cur_forum['forum_name']))), $multi ?
			__('Delete topics redirect', 'misc') :
			__('Delete topic redirect'));
	}


	// Setup form
	$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] =0;
	$forum_page['form_action'] = link('moderate_forum', $fid);

	$forum_page['hidden_fields'] = array(
		'csrf_token'	=> '<input type="hidden" name="csrf_token" value="'.generate_form_token($forum_page['form_action']).'" />',
		'topics'		=> '<input type="hidden" name="topics" value="'.implode(',', $topics).'" />'
	);

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array(config()->o_board_title, link('index')),
		array($cur_forum['forum_name'], link('forum', array($fid, sef_friendly($cur_forum['forum_name'])))),
		array(__('Moderate forum', 'misc'), link('moderate_forum', $fid)),
		$multi ? __('Delete topics', 'misc') : __('Delete topic', 'misc')
	);

	($hook = get_hook('mr_delete_topics_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE', 'dialogue');

	template()->render([
		'main_view' => 'moderate/dialogue5'
	]);
}


// Open or close one or more topics
else if (isset($_REQUEST['open']) || isset($_REQUEST['close']))
{
	$action = (isset($_REQUEST['open'])) ? 0 : 1;

	($hook = get_hook('mr_open_close_topic_selected')) ? eval($hook) : null;

	// There could be an array of topic ID's in $_POST
	if (isset($_POST['open']) || isset($_POST['close']))
	{
		$topics = isset($_POST['topics']) && is_array($_POST['topics']) ? $_POST['topics'] : array();
		$topics = array_map('intval', $topics);

		if (empty($topics))
			message(__('No topics selected', 'misc'));

		$query = array(
			'UPDATE'	=> 'topics',
			'SET'		=> 'closed='.$action,
			'WHERE'		=> 'id IN('.implode(',', $topics).') AND forum_id='.$fid
		);

		($hook = get_hook('mr_open_close_multi_topics_qr_open_close_topics')) ? eval($hook) : null;
		db()->query_build($query) or error(__FILE__, __LINE__);

		if (count($topics) == 1)
			$forum_page['redirect_msg'] = ($action) ?
				__('Close topic redirect', 'misc') : __('Open topic redirect', 'misc');
		else
			$forum_page['redirect_msg'] = ($action) ?
				__('Close topics redirect', 'misc') : __('Open topics redirect', 'misc');

		flash()->add_info($forum_page['redirect_msg']);

		($hook = get_hook('mr_open_close_multi_topics_pre_redirect')) ? eval($hook) : null;

		redirect(link('moderate_forum', $fid), $forum_page['redirect_msg']);
	}
	// Or just one in $_GET
	else
	{
		$topic_id = ($action) ? intval($_GET['close']) : intval($_GET['open']);
		if ($topic_id < 1)
			message(__('Bad request'));

		// We validate the CSRF token. If it's set in POST and we're at this point, the token is valid.
		// If it's in GET, we need to make sure it's valid.
		if (!isset($_POST['csrf_token']) && (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== generate_form_token(($action ? 'close' : 'open').$topic_id)))
			csrf_confirm_form();

		// Get the topic subject
		$query = array(
			'SELECT'	=> 't.subject',
			'FROM'		=> 'topics AS t',
			'WHERE'		=> 't.id='.$topic_id.' AND forum_id='.$fid
		);

		($hook = get_hook('mr_open_close_single_topic_qr_get_subject')) ? eval($hook) : null;
		$result = db()->query_build($query) or error(__FILE__, __LINE__);

		$subject = db()->result($result);

		if (!$subject)
		{
			message(__('Bad request'));
		}

		$query = array(
			'UPDATE'	=> 'topics',
			'SET'		=> 'closed='.$action,
			'WHERE'		=> 'id='.$topic_id.' AND forum_id='.$fid
		);

		($hook = get_hook('mr_open_close_single_topic_qr_open_close_topic')) ? eval($hook) : null;
		db()->query_build($query) or error(__FILE__, __LINE__);

		$forum_page['redirect_msg'] = ($action) ?
			__('Close topic redirect', 'misc') : __('Open topic redirect', 'misc');

		flash()->add_info($forum_page['redirect_msg']);

		($hook = get_hook('mr_open_close_single_topic_pre_redirect')) ? eval($hook) : null;

		redirect(link('topic', array($topic_id, sef_friendly($subject))), $forum_page['redirect_msg']);
	}
}


// Stick a topic
else if (isset($_GET['stick']))
{
	$stick = intval($_GET['stick']);
	if ($stick < 1)
		message(__('Bad request'));

	// We validate the CSRF token. If it's set in POST and we're at this point, the token is valid.
	// If it's in GET, we need to make sure it's valid.
	if (!isset($_POST['csrf_token']) && (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== generate_form_token('stick'.$stick)))
		csrf_confirm_form();

	($hook = get_hook('mr_stick_topic_selected')) ? eval($hook) : null;

	// Get the topic subject
	$query = array(
		'SELECT'	=> 't.subject',
		'FROM'		=> 'topics AS t',
		'WHERE'		=> 't.id='.$stick.' AND forum_id='.$fid
	);

	($hook = get_hook('mr_stick_topic_qr_get_subject')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);
	$subject = db()->result($result);

	if (!$subject)
	{
		message(__('Bad request'));
	}

	$query = array(
		'UPDATE'	=> 'topics',
		'SET'		=> 'sticky=1',
		'WHERE'		=> 'id='.$stick.' AND forum_id='.$fid
	);

	($hook = get_hook('mr_stick_topic_qr_stick_topic')) ? eval($hook) : null;
	db()->query_build($query) or error(__FILE__, __LINE__);

	flash()->add_info(__('Stick topic redirect', 'misc'));

	($hook = get_hook('mr_stick_topic_pre_redirect')) ? eval($hook) : null;

	redirect(link('topic', array($stick, sef_friendly($subject))),
		__('Stick topic redirect', 'misc'));
}


// Unstick a topic
else if (isset($_GET['unstick']))
{
	$unstick = intval($_GET['unstick']);
	if ($unstick < 1)
		message(__('Bad request'));

	// We validate the CSRF token. If it's set in POST and we're at this point, the token is valid.
	// If it's in GET, we need to make sure it's valid.
	if (!isset($_POST['csrf_token']) && (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== generate_form_token('unstick'.$unstick)))
		csrf_confirm_form();

	($hook = get_hook('mr_unstick_topic_selected')) ? eval($hook) : null;

	// Get the topic subject
	$query = array(
		'SELECT'	=> 't.subject',
		'FROM'		=> 'topics AS t',
		'WHERE'		=> 't.id='.$unstick.' AND forum_id='.$fid
	);

	($hook = get_hook('mr_unstick_topic_qr_get_subject')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);
	$subject = db()->result($result);

	if (!$subject)
	{
		message(__('Bad request'));
	}

	$query = array(
		'UPDATE'	=> 'topics',
		'SET'		=> 'sticky=0',
		'WHERE'		=> 'id='.$unstick.' AND forum_id='.$fid
	);

	($hook = get_hook('mr_unstick_topic_qr_unstick_topic')) ? eval($hook) : null;
	db()->query_build($query) or error(__FILE__, __LINE__);

	flash()->add_info(__('Unstick topic redirect', 'misc'));

	($hook = get_hook('mr_unstick_topic_pre_redirect')) ? eval($hook) : null;

	redirect(link('topic', array($unstick, sef_friendly($subject))),
		__('Unstick topic redirect', 'misc'));
}


($hook = get_hook('mr_new_action')) ? eval($hook) : null;


// No specific forum moderation action was specified in the query string, so we'll display the moderate forum view

// If forum is empty
if ($cur_forum['num_topics'] == 0)
	message(__('Bad request'));

// Determine the topic offset (based on $_GET['p'])
$forum_page['num_pages'] = ceil($cur_forum['num_topics'] / user()->disp_topics);

$forum_page['page'] = (!isset($_GET['p']) || !is_numeric($_GET['p']) || $_GET['p'] <= 1 || $_GET['p'] > $forum_page['num_pages']) ? 1 : $_GET['p'];
$forum_page['start_from'] = user()->disp_topics * ($forum_page['page'] - 1);
$forum_page['finish_at'] = min(($forum_page['start_from'] + user()->disp_topics), ($cur_forum['num_topics']));
$forum_page['items_info'] = generate_items_info(__('Topics', 'misc'), ($forum_page['start_from'] + 1), $cur_forum['num_topics']);

// Select topics
$query = array(
	'SELECT'	=> 't.id, t.poster, t.subject, t.posted, t.last_post, t.last_post_id, t.last_poster, t.num_views, t.num_replies, t.closed, t.sticky, t.moved_to',
	'FROM'		=> 'topics AS t',
	'WHERE'		=> 'forum_id='.$fid,
	'ORDER BY'	=> 't.sticky DESC, '.(($cur_forum['sort_by'] == '1') ? 't.posted' : 't.last_post').' DESC',
	'LIMIT'		=>	$forum_page['start_from'].', '.user()->disp_topics
);

// With "has posted" indication
if (!user()->is_guest && config()->o_show_dot == '1')
{
	$query['SELECT'] .= ', p.poster_id AS has_posted';
	$query['JOINS'][]	= array(
		'LEFT JOIN'		=> 'posts AS p',
		'ON'			=> '(p.poster_id='.user()->id.' AND p.topic_id=t.id)'
	);

	// Must have same columns as in prev SELECT
	$query['GROUP BY'] = 't.id, t.poster, t.subject, t.posted, t.last_post, t.last_post_id, t.last_poster, t.num_views, t.num_replies, t.closed, t.sticky, t.moved_to, p.poster_id';

	($hook = get_hook('mr_qr_get_has_posted')) ? eval($hook) : null;
}

($hook = get_hook('mr_qr_get_topics')) ? eval($hook) : null;
$result = db()->query_build($query) or error(__FILE__, __LINE__);

// Generate paging links
$forum_page['page_post']['paging'] = '<p class="paging"><span class="pages">'.
	__('Pages') . '</span> '.paginate($forum_page['num_pages'], $forum_page['page'], $forum_url['moderate_forum'],
		__('Paging separator'), $fid).'</p>';

// Navigation links for header and page numbering for title/meta description
if ($forum_page['page'] < $forum_page['num_pages'])
{
	$forum_page['nav']['last'] = '<link rel="last" href="'.forum_sublink('moderate_forum', $forum_url['page'], $forum_page['num_pages'], $fid).'" title="'.
		__('Page') . ' ' . $forum_page['num_pages'].'" />';
	$forum_page['nav']['next'] = '<link rel="next" href="'.forum_sublink('moderate_forum', $forum_url['page'], ($forum_page['page'] + 1), $fid).'" title="'.
		__('Page') . ' ' . ($forum_page['page'] + 1).'" />';
}
if ($forum_page['page'] > 1)
{
	$forum_page['nav']['prev'] = '<link rel="prev" href="'.forum_sublink('moderate_forum', $forum_url['page'], ($forum_page['page'] - 1), $fid).'" title="'.
		__('Page') . ' ' . ($forum_page['page'] - 1).'" />';
	$forum_page['nav']['first'] = '<link rel="first" href="'.link('moderate_forum', $fid).'" title="'.
		__('Page') . ' 1" />';
}

// Setup form
$forum_page['fld_count'] = 0;
$forum_page['form_action'] = link('moderate_forum', $fid);

// Setup breadcrumbs
$forum_page['crumbs'] = array(
	array(config()->o_board_title, link('index')),
	array($cur_forum['forum_name'], link('forum', array($fid, sef_friendly($cur_forum['forum_name'])))),
	sprintf(__('Moderate forum head', 'misc'), forum_htmlencode($cur_forum['forum_name']))
);

// Setup main heading
if ($forum_page['num_pages'] > 1)
	$forum_page['main_head_pages'] = sprintf(__('Page info'), $forum_page['page'], $forum_page['num_pages']);

$forum_page['main_head_options']['select_all'] = '<span '.(empty($forum_page['main_head_options']) ? ' class="first-item"' : '').'><span class="select-all js_link" data-check-form="mr-topic-actions-form">'.
	__('Select all', 'misc') . '</span></span>';
$forum_page['main_foot_options']['select_all'] = '<span '.(empty($forum_page['main_foot_options']) ? ' class="first-item"' : '').'><span class="select-all js_link" data-check-form="mr-topic-actions-form">'.
	__('Select all', 'misc') . '</span></span>';

($hook = get_hook('mr_topic_actions_pre_header_load')) ? eval($hook) : null;

define('FORUM_PAGE', 'modforum');

template()->render([
	'main_view' => 'moderate/modforum'
]);
