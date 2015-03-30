<?php
/**
 * Provides various mass-moderation tools to moderators.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */


if (!defined('FORUM_ROOT'))
	define('FORUM_ROOT', './');
require FORUM_ROOT.'include/common.php';

($hook = get_hook('mr_start')) ? eval($hook) : null;

// Load the misc.php language file
require FORUM_ROOT.'lang/'.$forum_user['language'].'/misc.php';


// This particular function doesn't require forum-based moderator access. It can be used
// by all moderators and admins.
if (isset($_GET['get_host']))
{
	if (!$forum_user['is_admmod'])
		message($lang_common['No permission']);

	$_get_host = $_GET['get_host'];
	if (!is_string($_get_host))
		message($lang_common['Bad request']);

	($hook = get_hook('mr_view_ip_selected')) ? eval($hook) : null;

	// Is get_host an IP address or a post ID?
	if (preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $_get_host) || preg_match('/^((([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}:[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){0,5}:((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(::([0-9A-Fa-f]{1,4}:){0,5}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|([0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}[0-9A-Fa-f]{1,4})|(::([0-9A-Fa-f]{1,4}:){0,6}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){1,7}:))$/', $_get_host))
		$ip = $_get_host;
	else
	{
		$get_host = intval($_get_host);
		if ($get_host < 1)
			message($lang_common['Bad request']);

		$query = array(
			'SELECT'	=> 'p.poster_ip',
			'FROM'		=> 'posts AS p',
			'WHERE'		=> 'p.id='.$get_host
		);

		($hook = get_hook('mr_view_ip_qr_get_poster_ip')) ? eval($hook) : null;
		$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
		$ip = $forum_db->result($result);

		if (!$ip)
			message($lang_common['Bad request']);
	}

	($hook = get_hook('mr_view_ip_pre_output')) ? eval($hook) : null;

	message(sprintf($lang_misc['Hostname lookup'], $ip, @gethostbyaddr($ip), '<a href="'.forum_link($forum_url['admin_users']).'?show_users='.$ip.'">'.$lang_misc['Show more users'].'</a>'));
}


// All other functions require moderator/admin access
$fid = isset($_GET['fid']) ? intval($_GET['fid']) : 0;
if ($fid < 1)
	message($lang_common['Bad request']);

// Get some info about the forum we're moderating
$query = array(
	'SELECT'	=> 'f.forum_name, f.redirect_url, f.num_topics, f.moderators, f.sort_by',
	'FROM'		=> 'forums AS f',
	'JOINS'		=> array(
		array(
			'LEFT JOIN'		=> 'forum_perms AS fp',
			'ON'			=> '(fp.forum_id=f.id AND fp.group_id='.$forum_user['g_id'].')'
		)
	),
	'WHERE'		=> '(fp.read_forum IS NULL OR fp.read_forum=1) AND f.id='.$fid
);

($hook = get_hook('mr_qr_get_forum_data')) ? eval($hook) : null;
$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
$cur_forum = $forum_db->fetch_assoc($result);

if (!$cur_forum)
	message($lang_common['Bad request']);

// Make sure we're not trying to moderate a redirect forum
if ($cur_forum['redirect_url'] != '')
	message($lang_common['Bad request']);

// Setup the array of moderators
$mods_array = ($cur_forum['moderators'] != '') ? unserialize($cur_forum['moderators']) : array();

($hook = get_hook('mr_pre_permission_check')) ? eval($hook) : null;

if ($forum_user['g_id'] != FORUM_ADMIN && ($forum_user['g_moderator'] != '1' || !array_key_exists($forum_user['username'], $mods_array)))
	message($lang_common['No permission']);

// Get topic/forum tracking data
if (!$forum_user['is_guest'])
	$tracked_topics = get_tracked_topics();


// Did someone click a cancel button?
if (isset($_POST['cancel']))
	redirect(forum_link($forum_url['forum'], array($fid, sef_friendly($cur_forum['forum_name']))), $lang_common['Cancel redirect']);

// All topic moderation features require a topic id in GET
if (isset($_GET['tid']))
{
	($hook = get_hook('mr_post_actions_selected')) ? eval($hook) : null;

	$tid = intval($_GET['tid']);
	if ($tid < 1)
		message($lang_common['Bad request']);

	// Fetch some info about the topic
	$query = array(
		'SELECT'	=> 't.subject, t.poster, t.first_post_id, t.posted, t.num_replies',
		'FROM'		=> 'topics AS t',
		'WHERE'		=> 't.id='.$tid.' AND t.moved_to IS NULL'
	);

	($hook = get_hook('mr_post_actions_qr_get_topic_info')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	$cur_topic = $forum_db->fetch_assoc($result);

	if (!$cur_topic)
		message($lang_common['Bad request']);

	// User pressed the cancel button
	if (isset($_POST['delete_posts_cancel']))
		redirect(forum_link($forum_url['topic'], array($tid, sef_friendly($cur_topic['subject']))), $lang_common['Cancel redirect']);

	// Delete one or more posts
	if (isset($_POST['delete_posts']) || isset($_POST['delete_posts_comply']))
	{
		($hook = get_hook('mr_delete_posts_form_submitted')) ? eval($hook) : null;

		$posts = isset($_POST['posts']) && !empty($_POST['posts']) ? $_POST['posts'] : array();
		$posts = array_map('intval', (is_array($posts) ? $posts : explode(',', $posts)));

		if (empty($posts))
			message($lang_misc['No posts selected']);

		if (isset($_POST['delete_posts_comply']))
		{
			if (!isset($_POST['req_confirm']))
				redirect(forum_link($forum_url['topic'], array($tid, sef_friendly($cur_topic['subject']))), $lang_common['No confirm redirect']);

			($hook = get_hook('mr_confirm_delete_posts_form_submitted')) ? eval($hook) : null;

			// Verify that the post IDs are valid
			$query = array(
				'SELECT'	=> 'COUNT(p.id)',
				'FROM'		=> 'posts AS p',
				'WHERE'		=> 'p.id IN('.implode(',', $posts).') AND p.id!='.$cur_topic['first_post_id'].' AND p.topic_id='.$tid
			);

			($hook = get_hook('mr_confirm_delete_posts_qr_verify_post_ids')) ? eval($hook) : null;
			$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
			if ($forum_db->result($result) != count($posts))
				message($lang_common['Bad request']);

			// Delete the posts
			$query = array(
				'DELETE'	=> 'posts',
				'WHERE'		=> 'id IN('.implode(',', $posts).')'
			);

			($hook = get_hook('mr_confirm_delete_posts_qr_delete_posts')) ? eval($hook) : null;
			$forum_db->query_build($query) or error(__FILE__, __LINE__);

			if (!defined('FORUM_SEARCH_IDX_FUNCTIONS_LOADED'))
				require FORUM_ROOT.'include/search_idx.php';

			strip_search_index($posts);

			sync_topic($tid);
			sync_forum($fid);

			$forum_flash->add_info($lang_misc['Delete posts redirect']);

			($hook = get_hook('mr_confirm_delete_posts_pre_redirect')) ? eval($hook) : null;

			redirect(forum_link($forum_url['topic'], array($tid, sef_friendly($cur_topic['subject']))), $lang_misc['Delete posts redirect']);
		}

		// Setup form
		$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;
		$forum_page['form_action'] = forum_link($forum_url['moderate_topic'], array($fid, $tid));

		$forum_page['hidden_fields'] = array(
			'csrf_token'	=> '<input type="hidden" name="csrf_token" value="'.generate_form_token($forum_page['form_action']).'" />',
			'posts'			=> '<input type="hidden" name="posts" value="'.implode(',', $posts).'" />'
		);

		// Setup breadcrumbs
		$forum_page['crumbs'] = array(
			array($forum_config['o_board_title'], forum_link($forum_url['index'])),
			array($cur_forum['forum_name'], forum_link($forum_url['forum'], array($fid, sef_friendly($cur_forum['forum_name'])))),
			array($cur_topic['subject'], forum_link($forum_url['topic'], array($tid, sef_friendly($cur_topic['subject'])))),
			$lang_misc['Delete posts']
		);

		($hook = get_hook('mr_confirm_delete_posts_pre_header_load')) ? eval($hook) : null;

		define('FORUM_PAGE', 'dialogue');
		require FORUM_ROOT.'header.php';

		$view_forum_main = 'moderate/dialogue';

		ob_start();
		include view($view_forum_layout);
		$tpl_main = forum_trim(ob_get_contents());
		ob_end_clean();

		require FORUM_ROOT.'footer.php';
	}
	else if (isset($_POST['split_posts']) || isset($_POST['split_posts_comply']))
	{
		($hook = get_hook('mr_split_posts_form_submitted')) ? eval($hook) : null;

		$posts = isset($_POST['posts']) && !empty($_POST['posts']) ? $_POST['posts'] : array();
		$posts = array_map('intval', (is_array($posts) ? $posts : explode(',', $posts)));

		if (empty($posts))
			message($lang_misc['No posts selected']);

		if (isset($_POST['split_posts_comply']))
		{
			if (!isset($_POST['req_confirm']))
				redirect(forum_link($forum_url['topic'], array($tid, sef_friendly($cur_topic['subject']))), $lang_common['No confirm redirect']);

			// Load the post.php language file
			require FORUM_ROOT.'lang/'.$forum_user['language'].'/post.php';

			($hook = get_hook('mr_confirm_split_posts_form_submitted')) ? eval($hook) : null;

			// Verify that the post IDs are valid
			$query = array(
				'SELECT'	=> 'COUNT(p.id)',
				'FROM'		=> 'posts AS p',
				'WHERE'		=> 'p.id IN('.implode(',', $posts).') AND p.id!='.$cur_topic['first_post_id'].' AND p.topic_id='.$tid
			);

			($hook = get_hook('mr_confirm_split_posts_qr_verify_post_ids')) ? eval($hook) : null;
			$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
			if ($forum_db->result($result) != count($posts))
				message($lang_common['Bad request']);

			$new_subject = isset($_POST['new_subject']) ? forum_trim($_POST['new_subject']) : '';

			if ($new_subject == '')
				message($lang_post['No subject']);
			else if (utf8_strlen($new_subject) > FORUM_SUBJECT_MAXIMUM_LENGTH)
				message(sprintf($lang_post['Too long subject'], FORUM_SUBJECT_MAXIMUM_LENGTH));

			// Get data from the new first post
			$query = array(
				'SELECT'	=> 'p.id, p.poster, p.posted',
				'FROM'		=> 'posts AS p',
				'WHERE'		=> 'p.id = '.min($posts)
			);

			($hook = get_hook('mr_confirm_split_posts_qr_get_first_post_data')) ? eval($hook) : null;
			$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
			$first_post_data = $forum_db->fetch_assoc($result);

			// Create the new topic
			$query = array(
				'INSERT'	=> 'poster, subject, posted, first_post_id, forum_id',
				'INTO'		=> 'topics',
				'VALUES'	=> '\''.$forum_db->escape($first_post_data['poster']).'\', \''.$forum_db->escape($new_subject).'\', '.$first_post_data['posted'].', '.$first_post_data['id'].', '.$fid
			);

			($hook = get_hook('mr_confirm_split_posts_qr_add_topic')) ? eval($hook) : null;
			$forum_db->query_build($query) or error(__FILE__, __LINE__);
			$new_tid = $forum_db->insert_id();

			// Move the posts to the new topic
			$query = array(
				'UPDATE'	=> 'posts',
				'SET'		=> 'topic_id='.$new_tid,
				'WHERE'		=> 'id IN('.implode(',', $posts).')'
			);

			($hook = get_hook('mr_confirm_split_posts_qr_move_posts')) ? eval($hook) : null;
			$forum_db->query_build($query) or error(__FILE__, __LINE__);

			// Sync last post data for the old topic, the new topic, and the forum itself
			sync_topic($new_tid);
			sync_topic($tid);
			sync_forum($fid);

			$forum_flash->add_info($lang_misc['Split posts redirect']);

			($hook = get_hook('mr_confirm_split_posts_pre_redirect')) ? eval($hook) : null;

			redirect(forum_link($forum_url['topic'], array($new_tid, sef_friendly($new_subject))), $lang_misc['Split posts redirect']);
		}

		// Setup form
		$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;
		$forum_page['form_action'] = forum_link($forum_url['moderate_topic'], array($fid, $tid));

		$forum_page['hidden_fields'] = array(
			'csrf_token'	=> '<input type="hidden" name="csrf_token" value="'.generate_form_token($forum_page['form_action']).'" />',
			'posts'			=> '<input type="hidden" name="posts" value="'.implode(',', $posts).'" />'
		);

		// Setup breadcrumbs
		$forum_page['crumbs'] = array(
			array($forum_config['o_board_title'], forum_link($forum_url['index'])),
			array($cur_forum['forum_name'], forum_link($forum_url['forum'], array($fid, sef_friendly($cur_forum['forum_name'])))),
			array($cur_topic['subject'], forum_link($forum_url['topic'], array($tid, sef_friendly($cur_topic['subject'])))),
			$lang_misc['Split posts']
		);

		($hook = get_hook('mr_confirm_split_posts_pre_header_load')) ? eval($hook) : null;

		define('FORUM_PAGE', 'dialogue');
		require FORUM_ROOT.'header.php';

		$view_forum_main = 'moderate/dialogue2';

		ob_start();
		include view($view_forum_layout);
		$tpl_main = forum_trim(ob_get_contents());
		ob_end_clean();

		require FORUM_ROOT.'footer.php';
	}


	// Show the moderate topic view

	// Load the viewtopic.php language file
	require FORUM_ROOT.'lang/'.$forum_user['language'].'/topic.php';

	// Used to disable the Split and Delete buttons if there are no replies to this topic
	$forum_page['button_status'] = ($cur_topic['num_replies'] == 0) ? ' disabled="disabled"' : '';


	// Determine the post offset (based on $_GET['p'])
	$forum_page['num_pages'] = ceil(($cur_topic['num_replies'] + 1) / $forum_user['disp_posts']);
	$forum_page['page'] = (!isset($_GET['p']) || !is_numeric($_GET['p']) || $_GET['p'] <= 1 || $_GET['p'] > $forum_page['num_pages']) ? 1 : intval($_GET['p']);
	$forum_page['start_from'] = $forum_user['disp_posts'] * ($forum_page['page'] - 1);
	$forum_page['finish_at'] = min(($forum_page['start_from'] + $forum_user['disp_posts']), ($cur_topic['num_replies'] + 1));
	$forum_page['items_info'] = generate_items_info($lang_misc['Posts'], ($forum_page['start_from'] + 1), ($cur_topic['num_replies'] + 1));

	// Generate paging links
	$forum_page['page_post']['paging'] = '<p class="paging"><span class="pages">'.$lang_common['Pages'].'</span> '.paginate($forum_page['num_pages'], $forum_page['page'], $forum_url['moderate_topic'], $lang_common['Paging separator'], array($fid, $tid)).'</p>';

	// Navigation links for header and page numbering for title/meta description
	if ($forum_page['page'] < $forum_page['num_pages'])
	{
		$forum_page['nav']['last'] = '<link rel="last" href="'.forum_sublink($forum_url['moderate_topic'], $forum_url['page'], $forum_page['num_pages'], array($fid, $tid)).'" title="'.$lang_common['Page'].' '.$forum_page['num_pages'].'" />';
		$forum_page['nav']['next'] = '<link rel="next" href="'.forum_sublink($forum_url['moderate_topic'], $forum_url['page'], ($forum_page['page'] + 1), array($fid, $tid)).'" title="'.$lang_common['Page'].' '.($forum_page['page'] + 1).'" />';
	}
	if ($forum_page['page'] > 1)
	{
		$forum_page['nav']['prev'] = '<link rel="prev" href="'.forum_sublink($forum_url['moderate_topic'], $forum_url['page'], ($forum_page['page'] - 1), array($fid, $tid)).'" title="'.$lang_common['Page'].' '.($forum_page['page'] - 1).'" />';
		$forum_page['nav']['first'] = '<link rel="first" href="'.forum_link($forum_url['moderate_topic'], array($fid, $tid)).'" title="'.$lang_common['Page'].' 1" />';
	}

	if ($forum_config['o_censoring'] == '1')
		$cur_topic['subject'] = censor_words($cur_topic['subject']);

	// Setup form
	$forum_page['form_action'] = forum_link($forum_url['moderate_topic'], array($fid, $tid));

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array($forum_config['o_board_title'], forum_link($forum_url['index'])),
		array($cur_forum['forum_name'], forum_link($forum_url['forum'], array($fid, sef_friendly($cur_forum['forum_name'])))),
		array($cur_topic['subject'], forum_link($forum_url['topic'], array($tid, sef_friendly($cur_topic['subject'])))),
		$lang_topic['Moderate topic']
	);

	// Setup main heading
	$forum_page['main_title'] = sprintf($lang_misc['Moderate topic head'], forum_htmlencode($cur_topic['subject']));

	$forum_page['main_head_options']['select_all'] = '<span '.(empty($forum_page['main_head_options']) ? ' class="first-item"' : '').'><span class="select-all js_link" data-check-form="mr-post-actions-form">'.$lang_misc['Select all'].'</span></span>';
	$forum_page['main_foot_options']['select_all'] = '<span '.(empty($forum_page['main_foot_options']) ? ' class="first-item"' : '').'><span class="select-all js_link" data-check-form="mr-post-actions-form">'.$lang_misc['Select all'].'</span></span>';

	if ($forum_page['num_pages'] > 1)
		$forum_page['main_head_pages'] = sprintf($lang_common['Page info'], $forum_page['page'], $forum_page['num_pages']);

	($hook = get_hook('mr_post_actions_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE', 'modtopic');
	require FORUM_ROOT.'header.php';

	$view_forum_main = 'moderate/modtopic';

	ob_start();
	include view($view_forum_layout);
	$tpl_main = forum_trim(ob_get_contents());
	ob_end_clean();

	require FORUM_ROOT.'footer.php';
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
			message($lang_common['Bad request']);

		// Fetch the forum name for the forum we're moving to
		$query = array(
			'SELECT'	=> 'f.forum_name',
			'FROM'		=> 'forums AS f',
			'WHERE'		=> 'f.id='.$move_to_forum
		);

		($hook = get_hook('mr_confirm_move_topics_qr_get_move_to_forum_name')) ? eval($hook) : null;
		$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

		$move_to_forum_name = $forum_db->result($result);

		if (!$move_to_forum_name)
			message($lang_common['Bad request']);

		// Verify that the topic IDs are valid
		$query = array(
			'SELECT'	=> 'COUNT(t.id)',
			'FROM'		=> 'topics AS t',
			'WHERE'		=> 't.id IN('.implode(',', $topics).') AND t.forum_id='.$fid
		);

		($hook = get_hook('mr_confirm_move_topics_qr_verify_topic_ids')) ? eval($hook) : null;
		$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
		if ($forum_db->result($result) != count($topics))
			message($lang_common['Bad request']);

		// Delete any redirect topics if there are any (only if we moved/copied the topic back to where it where it was once moved from)
		$query = array(
			'DELETE'	=> 'topics',
			'WHERE'		=> 'forum_id='.$move_to_forum.' AND moved_to IN('.implode(',', $topics).')'
		);

		($hook = get_hook('mr_confirm_move_topics_qr_delete_redirect_topics')) ? eval($hook) : null;
		$forum_db->query_build($query) or error(__FILE__, __LINE__);

		// Move the topic(s)
		$query = array(
			'UPDATE'	=> 'topics',
			'SET'		=> 'forum_id='.$move_to_forum,
			'WHERE'		=> 'id IN('.implode(',', $topics).')'
		);

		($hook = get_hook('mr_confirm_move_topics_qr_move_topics')) ? eval($hook) : null;
		$forum_db->query_build($query) or error(__FILE__, __LINE__);

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
				$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
				$moved_to = $forum_db->fetch_assoc($result);

				// Create the redirect topic
				$query = array(
					'INSERT'	=> 'poster, subject, posted, last_post, moved_to, forum_id',
					'INTO'		=> 'topics',
					'VALUES'	=> '\''.$forum_db->escape($moved_to['poster']).'\', \''.$forum_db->escape($moved_to['subject']).'\', '.$moved_to['posted'].', '.$moved_to['last_post'].', '.$cur_topic.', '.$fid
				);

				($hook = get_hook('mr_confirm_move_topics_qr_add_redirect_topic')) ? eval($hook) : null;
				$forum_db->query_build($query) or error(__FILE__, __LINE__);
			}
		}

		sync_forum($fid);			// Synchronize the forum FROM which the topic was moved
		sync_forum($move_to_forum);	// Synchronize the forum TO which the topic was moved

		$forum_page['redirect_msg'] = (count($topics) > 1) ? $lang_misc['Move topics redirect'] : $lang_misc['Move topic redirect'];

		$forum_flash->add_info($forum_page['redirect_msg']);

		($hook = get_hook('mr_confirm_move_topics_pre_redirect')) ? eval($hook) : null;

		redirect(forum_link($forum_url['forum'], array($move_to_forum, sef_friendly($move_to_forum_name))), $forum_page['redirect_msg']);
	}

	if (isset($_POST['move_topics']))
	{
		$topics = isset($_POST['topics']) && is_array($_POST['topics']) ? $_POST['topics'] : array();
		$topics = array_map('intval', $topics);

		if (empty($topics))
			message($lang_misc['No topics selected']);

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
			message($lang_common['Bad request']);

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
		$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
		$subject = $forum_db->result($result);

		if (!$subject)
		{
			message($lang_common['Bad request']);
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
				'ON'			=> '(fp.forum_id=f.id AND fp.group_id='.$forum_user['g_id'].')'
			)
		),
		'WHERE'		=> '(fp.read_forum IS NULL OR fp.read_forum=1) AND f.redirect_url IS NULL AND f.id!='.$fid,
		'ORDER BY'	=> 'c.disp_position, c.id, f.disp_position'
	);

	($hook = get_hook('mr_move_topics_qr_get_target_forums')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

	$forum_list = array();
	while ($cur_sel_forum = $forum_db->fetch_assoc($result))
	{
		$forum_list[] = $cur_sel_forum;
	}

	if (empty($forum_list))
	{
		message($lang_misc['Nowhere to move']);
	}


	// Setup form
	$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;
	$forum_page['form_action'] = forum_link($forum_url['moderate_forum'], $fid);

	$forum_page['hidden_fields'] = array(
		'csrf_token'	=> '<input type="hidden" name="csrf_token" value="'.generate_form_token($forum_page['form_action']).'" />',
		'topics'		=> '<input type="hidden" name="topics" value="'.($action == 'single' ? $topics : implode(',', $topics)).'" />'
	);

	// Setup breadcrumbs
	$forum_page['crumbs'][] = array($forum_config['o_board_title'], forum_link($forum_url['index']));
	$forum_page['crumbs'][] = array($cur_forum['forum_name'], forum_link($forum_url['forum'], array($fid, sef_friendly($cur_forum['forum_name']))));
	if ($action == 'single')
		$forum_page['crumbs'][] = array($subject, forum_link($forum_url['topic'], array($topics, sef_friendly($subject))));
	else
		$forum_page['crumbs'][] = array($lang_misc['Moderate forum'], forum_link($forum_url['moderate_forum'], $fid));
	$forum_page['crumbs'][] = ($action == 'single') ? $lang_misc['Move topic'] : $lang_misc['Move topics'];

	//Setup main heading
	$forum_page['main_title'] = end($forum_page['crumbs']).' '.$lang_misc['To new forum'];

	($hook = get_hook('mr_move_topics_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE', 'dialogue');
	require FORUM_ROOT.'header.php';

	$view_forum_main = 'moderate/dialogue3';

	ob_start();
	include view($view_forum_layout);
	$tpl_main = forum_trim(ob_get_contents());
	ob_end_clean();

	require FORUM_ROOT.'footer.php';
}


// Merge topics
else if (isset($_POST['merge_topics']) || isset($_POST['merge_topics_comply']))
{
	$topics = isset($_POST['topics']) && !empty($_POST['topics']) ? $_POST['topics'] : array();
	$topics = array_map('intval', (is_array($topics) ? $topics : explode(',', $topics)));

	if (empty($topics))
		message($lang_misc['No topics selected']);

	if (count($topics) == 1)
		message($lang_misc['Merge error']);

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
		$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
		list($num_topics, $merge_to_tid) = $forum_db->fetch_row($result);
		if ($num_topics != count($topics))
			message($lang_common['Bad request']);

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
		$forum_db->query_build($query) or error(__FILE__, __LINE__);

		// Merge the posts into the topic
		$query = array(
			'UPDATE'	=> 'posts',
			'SET'		=> 'topic_id='.$merge_to_tid,
			'WHERE'		=> 'topic_id IN('.implode(',', $topics).')'
		);

		($hook = get_hook('mr_confirm_merge_topics_qr_merge_posts')) ? eval($hook) : null;
		$forum_db->query_build($query) or error(__FILE__, __LINE__);

		// Delete any subscriptions
		$query = array(
			'DELETE'	=> 'subscriptions',
			'WHERE'		=> 'topic_id IN('.implode(',', $topics).') AND topic_id != '.$merge_to_tid
		);

		($hook = get_hook('mr_confirm_merge_topics_qr_delete_subscriptions')) ? eval($hook) : null;
		$forum_db->query_build($query) or error(__FILE__, __LINE__);

		if (!isset($_POST['with_redirect']))
		{
			// Delete the topics that have been merged
			$query = array(
				'DELETE'	=> 'topics',
				'WHERE'		=> 'id IN('.implode(',', $topics).') AND id != '.$merge_to_tid
			);

			($hook = get_hook('mr_confirm_merge_topics_qr_delete_merged_topics')) ? eval($hook) : null;
			$forum_db->query_build($query) or error(__FILE__, __LINE__);
		}

		// Synchronize the topic we merged to and the forum where the topics were merged
		sync_topic($merge_to_tid);
		sync_forum($fid);

		$forum_flash->add_info($lang_misc['Merge topics redirect']);

		($hook = get_hook('mr_confirm_merge_topics_pre_redirect')) ? eval($hook) : null;

		redirect(forum_link($forum_url['forum'], array($fid, sef_friendly($cur_forum['forum_name']))), $lang_misc['Merge topics redirect']);
	}

	// Setup form
	$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;
	$forum_page['form_action'] = forum_link($forum_url['moderate_forum'], $fid);

	$forum_page['hidden_fields'] = array(
		'csrf_token'	=> '<input type="hidden" name="csrf_token" value="'.generate_form_token($forum_page['form_action']).'" />',
		'topics'		=> '<input type="hidden" name="topics" value="'.implode(',', $topics).'" />'
	);

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array($forum_config['o_board_title'], forum_link($forum_url['index'])),
		array($cur_forum['forum_name'], forum_link($forum_url['forum'], array($fid, sef_friendly($cur_forum['forum_name'])))),
		array($lang_misc['Moderate forum'], forum_link($forum_url['moderate_forum'], $fid)),
		$lang_misc['Merge topics']
	);

	($hook = get_hook('mr_merge_topics_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE', 'dialogue');
	require FORUM_ROOT.'header.php';

	$view_forum_main = 'moderate/dialogue4';

	ob_start();
	include view($view_forum_layout);
	$tpl_main = forum_trim(ob_get_contents());
	ob_end_clean();

	require FORUM_ROOT.'footer.php';
}


// Delete one or more topics
else if (isset($_REQUEST['delete_topics']) || isset($_POST['delete_topics_comply']))
{
	$topics = isset($_POST['topics']) && !empty($_POST['topics']) ? $_POST['topics'] : array();
	$topics = array_map('intval', (is_array($topics) ? $topics : explode(',', $topics)));

	if (empty($topics))
		message($lang_misc['No topics selected']);

	$multi = count($topics) > 1;
	if (isset($_POST['delete_topics_comply']))
	{
		if (!isset($_POST['req_confirm']))
			redirect(forum_link($forum_url['forum'], array($fid, sef_friendly($cur_forum['forum_name']))), $lang_common['Cancel redirect']);

		($hook = get_hook('mr_confirm_delete_topics_form_submitted')) ? eval($hook) : null;

		// Verify that the topic IDs are valid
		$query = array(
			'SELECT'	=> 'COUNT(t.id)',
			'FROM'		=> 'topics AS t',
			'WHERE'		=> 't.id IN('.implode(',', $topics).') AND t.forum_id='.$fid
		);

		($hook = get_hook('mr_confirm_delete_topics_qr_verify_topic_ids')) ? eval($hook) : null;
		$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
		if ($forum_db->result($result) != count($topics))
			message($lang_common['Bad request']);

		// Create an array of forum IDs that need to be synced
		$forum_ids = array($fid);
		$query = array(
			'SELECT'	=> 't.forum_id',
			'FROM'		=> 'topics AS t',
			'WHERE'		=> 't.moved_to IN('.implode(',', $topics).')'
		);

		($hook = get_hook('mr_confirm_delete_topics_qr_get_forums_to_sync')) ? eval($hook) : null;
		$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
		while ($row = $forum_db->fetch_row($result))
			$forum_ids[] = $row[0];

		// Delete the topics and any redirect topics
		$query = array(
			'DELETE'	=> 'topics',
			'WHERE'		=> 'id IN('.implode(',', $topics).') OR moved_to IN('.implode(',', $topics).')'
		);

		($hook = get_hook('mr_confirm_delete_topics_qr_delete_topics')) ? eval($hook) : null;
		$forum_db->query_build($query) or error(__FILE__, __LINE__);

		// Delete any subscriptions
		$query = array(
			'DELETE'	=> 'subscriptions',
			'WHERE'		=> 'topic_id IN('.implode(',', $topics).')'
		);

		($hook = get_hook('mr_confirm_delete_topics_qr_delete_subscriptions')) ? eval($hook) : null;
		$forum_db->query_build($query) or error(__FILE__, __LINE__);

		// Create a list of the post ID's in the deleted topic and strip the search index
		$query = array(
			'SELECT'	=> 'p.id',
			'FROM'		=> 'posts AS p',
			'WHERE'		=> 'p.topic_id IN('.implode(',', $topics).')'
		);

		($hook = get_hook('mr_confirm_delete_topics_qr_get_deleted_posts')) ? eval($hook) : null;
		$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

		$post_ids = array();
		while ($row = $forum_db->fetch_row($result))
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
		$forum_db->query_build($query) or error(__FILE__, __LINE__);

		foreach ($forum_ids as $cur_forum_id)
			sync_forum($cur_forum_id);

		$forum_flash->add_info($multi ? $lang_misc['Delete topics redirect'] : $lang_misc['Delete topic redirect']);

		($hook = get_hook('mr_confirm_delete_topics_pre_redirect')) ? eval($hook) : null;

		redirect(forum_link($forum_url['forum'], array($fid, sef_friendly($cur_forum['forum_name']))), $multi ? $lang_misc['Delete topics redirect'] : $lang_misc['Delete topic redirect']);
	}


	// Setup form
	$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] =0;
	$forum_page['form_action'] = forum_link($forum_url['moderate_forum'], $fid);

	$forum_page['hidden_fields'] = array(
		'csrf_token'	=> '<input type="hidden" name="csrf_token" value="'.generate_form_token($forum_page['form_action']).'" />',
		'topics'		=> '<input type="hidden" name="topics" value="'.implode(',', $topics).'" />'
	);

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array($forum_config['o_board_title'], forum_link($forum_url['index'])),
		array($cur_forum['forum_name'], forum_link($forum_url['forum'], array($fid, sef_friendly($cur_forum['forum_name'])))),
		array($lang_misc['Moderate forum'], forum_link($forum_url['moderate_forum'], $fid)),
		$multi ? $lang_misc['Delete topics'] : $lang_misc['Delete topic']
	);

	($hook = get_hook('mr_delete_topics_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE', 'dialogue');
	require FORUM_ROOT.'header.php';

	$view_forum_main = 'moderate/dialogue5';

	ob_start();
	include view($view_forum_layout);
	$tpl_main = forum_trim(ob_get_contents());
	ob_end_clean();

	require FORUM_ROOT.'footer.php';
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
			message($lang_misc['No topics selected']);

		$query = array(
			'UPDATE'	=> 'topics',
			'SET'		=> 'closed='.$action,
			'WHERE'		=> 'id IN('.implode(',', $topics).') AND forum_id='.$fid
		);

		($hook = get_hook('mr_open_close_multi_topics_qr_open_close_topics')) ? eval($hook) : null;
		$forum_db->query_build($query) or error(__FILE__, __LINE__);

		if (count($topics) == 1)
			$forum_page['redirect_msg'] = ($action) ? $lang_misc['Close topic redirect'] : $lang_misc['Open topic redirect'];
		else
			$forum_page['redirect_msg'] = ($action) ? $lang_misc['Close topics redirect'] : $lang_misc['Open topics redirect'];

		$forum_flash->add_info($forum_page['redirect_msg']);

		($hook = get_hook('mr_open_close_multi_topics_pre_redirect')) ? eval($hook) : null;

		redirect(forum_link($forum_url['moderate_forum'], $fid), $forum_page['redirect_msg']);
	}
	// Or just one in $_GET
	else
	{
		$topic_id = ($action) ? intval($_GET['close']) : intval($_GET['open']);
		if ($topic_id < 1)
			message($lang_common['Bad request']);

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
		$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

		$subject = $forum_db->result($result);

		if (!$subject)
		{
			message($lang_common['Bad request']);
		}

		$query = array(
			'UPDATE'	=> 'topics',
			'SET'		=> 'closed='.$action,
			'WHERE'		=> 'id='.$topic_id.' AND forum_id='.$fid
		);

		($hook = get_hook('mr_open_close_single_topic_qr_open_close_topic')) ? eval($hook) : null;
		$forum_db->query_build($query) or error(__FILE__, __LINE__);

		$forum_page['redirect_msg'] = ($action) ? $lang_misc['Close topic redirect'] : $lang_misc['Open topic redirect'];

		$forum_flash->add_info($forum_page['redirect_msg']);

		($hook = get_hook('mr_open_close_single_topic_pre_redirect')) ? eval($hook) : null;

		redirect(forum_link($forum_url['topic'], array($topic_id, sef_friendly($subject))), $forum_page['redirect_msg']);
	}
}


// Stick a topic
else if (isset($_GET['stick']))
{
	$stick = intval($_GET['stick']);
	if ($stick < 1)
		message($lang_common['Bad request']);

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
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	$subject = $forum_db->result($result);

	if (!$subject)
	{
		message($lang_common['Bad request']);
	}

	$query = array(
		'UPDATE'	=> 'topics',
		'SET'		=> 'sticky=1',
		'WHERE'		=> 'id='.$stick.' AND forum_id='.$fid
	);

	($hook = get_hook('mr_stick_topic_qr_stick_topic')) ? eval($hook) : null;
	$forum_db->query_build($query) or error(__FILE__, __LINE__);

	$forum_flash->add_info($lang_misc['Stick topic redirect']);

	($hook = get_hook('mr_stick_topic_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['topic'], array($stick, sef_friendly($subject))), $lang_misc['Stick topic redirect']);
}


// Unstick a topic
else if (isset($_GET['unstick']))
{
	$unstick = intval($_GET['unstick']);
	if ($unstick < 1)
		message($lang_common['Bad request']);

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
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	$subject = $forum_db->result($result);

	if (!$subject)
	{
		message($lang_common['Bad request']);
	}

	$query = array(
		'UPDATE'	=> 'topics',
		'SET'		=> 'sticky=0',
		'WHERE'		=> 'id='.$unstick.' AND forum_id='.$fid
	);

	($hook = get_hook('mr_unstick_topic_qr_unstick_topic')) ? eval($hook) : null;
	$forum_db->query_build($query) or error(__FILE__, __LINE__);

	$forum_flash->add_info($lang_misc['Unstick topic redirect']);

	($hook = get_hook('mr_unstick_topic_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['topic'], array($unstick, sef_friendly($subject))), $lang_misc['Unstick topic redirect']);
}


($hook = get_hook('mr_new_action')) ? eval($hook) : null;


// No specific forum moderation action was specified in the query string, so we'll display the moderate forum view

// If forum is empty
if ($cur_forum['num_topics'] == 0)
	message($lang_common['Bad request']);

// Load the viewforum.php language file
require FORUM_ROOT.'lang/'.$forum_user['language'].'/forum.php';

// Determine the topic offset (based on $_GET['p'])
$forum_page['num_pages'] = ceil($cur_forum['num_topics'] / $forum_user['disp_topics']);

$forum_page['page'] = (!isset($_GET['p']) || !is_numeric($_GET['p']) || $_GET['p'] <= 1 || $_GET['p'] > $forum_page['num_pages']) ? 1 : $_GET['p'];
$forum_page['start_from'] = $forum_user['disp_topics'] * ($forum_page['page'] - 1);
$forum_page['finish_at'] = min(($forum_page['start_from'] + $forum_user['disp_topics']), ($cur_forum['num_topics']));
$forum_page['items_info'] = generate_items_info($lang_misc['Topics'], ($forum_page['start_from'] + 1), $cur_forum['num_topics']);

// Select topics
$query = array(
	'SELECT'	=> 't.id, t.poster, t.subject, t.posted, t.last_post, t.last_post_id, t.last_poster, t.num_views, t.num_replies, t.closed, t.sticky, t.moved_to',
	'FROM'		=> 'topics AS t',
	'WHERE'		=> 'forum_id='.$fid,
	'ORDER BY'	=> 't.sticky DESC, '.(($cur_forum['sort_by'] == '1') ? 't.posted' : 't.last_post').' DESC',
	'LIMIT'		=>	$forum_page['start_from'].', '.$forum_user['disp_topics']
);

// With "has posted" indication
if (!$forum_user['is_guest'] && $forum_config['o_show_dot'] == '1')
{
	$query['SELECT'] .= ', p.poster_id AS has_posted';
	$query['JOINS'][]	= array(
		'LEFT JOIN'		=> 'posts AS p',
		'ON'			=> '(p.poster_id='.$forum_user['id'].' AND p.topic_id=t.id)'
	);

	// Must have same columns as in prev SELECT
	$query['GROUP BY'] = 't.id, t.poster, t.subject, t.posted, t.last_post, t.last_post_id, t.last_poster, t.num_views, t.num_replies, t.closed, t.sticky, t.moved_to, p.poster_id';

	($hook = get_hook('mr_qr_get_has_posted')) ? eval($hook) : null;
}

($hook = get_hook('mr_qr_get_topics')) ? eval($hook) : null;
$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

// Generate paging links
$forum_page['page_post']['paging'] = '<p class="paging"><span class="pages">'.$lang_common['Pages'].'</span> '.paginate($forum_page['num_pages'], $forum_page['page'], $forum_url['moderate_forum'], $lang_common['Paging separator'], $fid).'</p>';

// Navigation links for header and page numbering for title/meta description
if ($forum_page['page'] < $forum_page['num_pages'])
{
	$forum_page['nav']['last'] = '<link rel="last" href="'.forum_sublink($forum_url['moderate_forum'], $forum_url['page'], $forum_page['num_pages'], $fid).'" title="'.$lang_common['Page'].' '.$forum_page['num_pages'].'" />';
	$forum_page['nav']['next'] = '<link rel="next" href="'.forum_sublink($forum_url['moderate_forum'], $forum_url['page'], ($forum_page['page'] + 1), $fid).'" title="'.$lang_common['Page'].' '.($forum_page['page'] + 1).'" />';
}
if ($forum_page['page'] > 1)
{
	$forum_page['nav']['prev'] = '<link rel="prev" href="'.forum_sublink($forum_url['moderate_forum'], $forum_url['page'], ($forum_page['page'] - 1), $fid).'" title="'.$lang_common['Page'].' '.($forum_page['page'] - 1).'" />';
	$forum_page['nav']['first'] = '<link rel="first" href="'.forum_link($forum_url['moderate_forum'], $fid).'" title="'.$lang_common['Page'].' 1" />';
}

// Setup form
$forum_page['fld_count'] = 0;
$forum_page['form_action'] = forum_link($forum_url['moderate_forum'], $fid);

// Setup breadcrumbs
$forum_page['crumbs'] = array(
	array($forum_config['o_board_title'], forum_link($forum_url['index'])),
	array($cur_forum['forum_name'], forum_link($forum_url['forum'], array($fid, sef_friendly($cur_forum['forum_name'])))),
	sprintf($lang_misc['Moderate forum head'], forum_htmlencode($cur_forum['forum_name']))
);

// Setup main heading
if ($forum_page['num_pages'] > 1)
	$forum_page['main_head_pages'] = sprintf($lang_common['Page info'], $forum_page['page'], $forum_page['num_pages']);

$forum_page['main_head_options']['select_all'] = '<span '.(empty($forum_page['main_head_options']) ? ' class="first-item"' : '').'><span class="select-all js_link" data-check-form="mr-topic-actions-form">'.$lang_misc['Select all'].'</span></span>';
$forum_page['main_foot_options']['select_all'] = '<span '.(empty($forum_page['main_foot_options']) ? ' class="first-item"' : '').'><span class="select-all js_link" data-check-form="mr-topic-actions-form">'.$lang_misc['Select all'].'</span></span>';

($hook = get_hook('mr_topic_actions_pre_header_load')) ? eval($hook) : null;

define('FORUM_PAGE', 'modforum');
require FORUM_ROOT.'header.php';

$view_forum_main = 'moderate/modforum';

ob_start();
include view($view_forum_layout);
$tpl_main = forum_trim(ob_get_contents());
ob_end_clean();

require FORUM_ROOT.'footer.php';
