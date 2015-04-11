<?php
/**
 * Lists the posts in the specified topic.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */
namespace punbb;

require __DIR__ . '/vendor/pautoload.php';

($hook = get_hook('vt_start')) ? eval($hook) : null;

if (user()['g_read_board'] == '0')
	message(__('No view'));

$action = isset($_GET['action']) ? $_GET['action'] : null;
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$pid = isset($_GET['pid']) ? intval($_GET['pid']) : 0;
if ($id < 1 && $pid < 1)
	message(__('Bad request'));


// If a post ID is specified we determine topic ID and page number so we can redirect to the correct message
if ($pid)
{
	$query = array(
		'SELECT'	=> 'p.topic_id, p.posted',
		'FROM'		=> 'posts AS p',
		'WHERE'		=> 'p.id='.$pid
	);

	($hook = get_hook('vt_qr_get_post_info')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);
	$topic_info = db()->fetch_assoc($result);

	if (!$topic_info)
	{
		message(__('Bad request'));
	}

	$id = $topic_info['topic_id'];

	// Determine on what page the post is located (depending on forum_user['disp_posts'])
	$query = array(
		'SELECT'	=> 'COUNT(p.id)',
		'FROM'		=> 'posts AS p',
		'WHERE'		=> 'p.topic_id='.$topic_info['topic_id'].' AND p.posted<'.$topic_info['posted']
	);

	($hook = get_hook('vt_qr_get_post_page')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);
	$num_posts = db()->result($result) + 1;

	$_GET['p'] = ceil($num_posts / user()['disp_posts']);
}

// If action=new, we redirect to the first new post (if any)
else if ($action == 'new')
{
	if (!user()['is_guest'])
	{
		// We need to check if this topic has been viewed recently by the user
		$tracked_topics = get_tracked_topics();
		$last_viewed = isset($tracked_topics['topics'][$id]) ? $tracked_topics['topics'][$id] : user()['last_visit'];

		($hook = get_hook('vt_find_new_post')) ? eval($hook) : null;

		$query = array(
			'SELECT'	=> 'MIN(p.id)',
			'FROM'		=> 'posts AS p',
			'WHERE'		=> 'p.topic_id='.$id.' AND p.posted>'.$last_viewed
		);

		($hook = get_hook('vt_qr_get_first_new_post')) ? eval($hook) : null;
		$result = db()->query_build($query) or error(__FILE__, __LINE__);
		$first_new_post_id = db()->result($result);

		if ($first_new_post_id)
		{
			header('Location: '.str_replace('&amp;', '&', forum_link($forum_url['post'], $first_new_post_id)));
			exit;
		}
	}

	header('Location: '.str_replace('&amp;', '&', forum_link($forum_url['topic_last_post'], $id)));
	exit;
}

// If action=last, we redirect to the last post
else if ($action == 'last')
{
	$query = array(
		'SELECT'	=> 't.last_post_id',
		'FROM'		=> 'topics AS t',
		'WHERE'		=> 't.id='.$id
	);

	($hook = get_hook('vt_qr_get_last_post')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);
	$last_post_id = db()->result($result);

	if ($last_post_id)
	{
		header('Location: '.str_replace('&amp;', '&', forum_link($forum_url['post'], $last_post_id)));
		exit;
	}
}


// Fetch some info about the topic
$query = array(
	'SELECT'	=> 't.subject, t.first_post_id, t.closed, t.num_replies, t.sticky, f.id AS forum_id, f.forum_name, f.moderators, fp.post_replies',
	'FROM'		=> 'topics AS t',
	'JOINS'		=> array(
		array(
			'INNER JOIN'	=> 'forums AS f',
			'ON'			=> 'f.id=t.forum_id'
		),
		array(
			'LEFT JOIN'		=> 'forum_perms AS fp',
			'ON'			=> '(fp.forum_id=f.id AND fp.group_id='.user()['g_id'].')'
		)
	),
	'WHERE'		=> '(fp.read_forum IS NULL OR fp.read_forum=1) AND t.id='.$id.' AND t.moved_to IS NULL'
);

if (!user()['is_guest'] && config()['o_subscriptions'] == '1')
{
	$query['SELECT'] .= ', s.user_id AS is_subscribed';
	$query['JOINS'][] = array(
		'LEFT JOIN'	=> 'subscriptions AS s',
		'ON'		=> '(t.id=s.topic_id AND s.user_id='.user()['id'].')'
	);
}

($hook = get_hook('vt_qr_get_topic_info')) ? eval($hook) : null;
$result = db()->query_build($query) or error(__FILE__, __LINE__);
$cur_topic = db()->fetch_assoc($result);

if (!$cur_topic)
{
	message(__('Bad request'));
}

($hook = get_hook('vt_modify_topic_info')) ? eval($hook) : null;

// Sort out who the moderators are and if we are currently a moderator (or an admin)
$mods_array = ($cur_topic['moderators'] != '') ? unserialize($cur_topic['moderators']) : array();
$forum_page['is_admmod'] = (user()['g_id'] == FORUM_ADMIN || (user()['g_moderator'] == '1' && array_key_exists(user()['username'], $mods_array))) ? true : false;

// Can we or can we not post replies?
if ($cur_topic['closed'] == '0' || $forum_page['is_admmod']) {
	$_PUNBB['user']['may_post'] = (($cur_topic['post_replies'] == '' && user()['g_post_replies'] == '1') || $cur_topic['post_replies'] == '1' || $forum_page['is_admmod']) ? true : false;
}
else {
	$_PUNBB['user']['may_post'] = false;
}

// Add/update this topic in our list of tracked topics
if (!user()['is_guest'])
{
	$tracked_topics = get_tracked_topics();
	$tracked_topics['topics'][$id] = time();
	set_tracked_topics($tracked_topics);
}

// Determine the post offset (based on $_GET['p'])
$forum_page['num_pages'] = ceil(($cur_topic['num_replies'] + 1) / user()['disp_posts']);
$forum_page['page'] = (!isset($_GET['p']) || !is_numeric($_GET['p']) || $_GET['p'] <= 1 || $_GET['p'] > $forum_page['num_pages']) ? 1 : $_GET['p'];
$forum_page['start_from'] = user()['disp_posts'] * ($forum_page['page'] - 1);
$forum_page['finish_at'] = min(($forum_page['start_from'] + user()['disp_posts']), ($cur_topic['num_replies'] + 1));
$forum_page['items_info'] = generate_items_info(__('Posts', 'topic'), ($forum_page['start_from'] + 1), ($cur_topic['num_replies'] + 1));

($hook = get_hook('vt_modify_page_details')) ? eval($hook) : null;

// Navigation links for header and page numbering for title/meta description
if ($forum_page['page'] < $forum_page['num_pages'])
{
	$forum_page['nav']['last'] = '<link rel="last" href="'.forum_sublink($forum_url['topic'], $forum_url['page'], $forum_page['num_pages'], array($id, sef_friendly($cur_topic['subject']))).'" title="'.
		__('Page') . ' ' . $forum_page['num_pages'].'" />';
	$forum_page['nav']['next'] = '<link rel="next" href="'.forum_sublink($forum_url['topic'], $forum_url['page'], ($forum_page['page'] + 1), array($id, sef_friendly($cur_topic['subject']))).'" title="'.
		__('Page') . ' ' . ($forum_page['page'] + 1).'" />';
}
if ($forum_page['page'] > 1)
{
	$forum_page['nav']['prev'] = '<link rel="prev" href="'.forum_sublink($forum_url['topic'], $forum_url['page'], ($forum_page['page'] - 1), array($id, sef_friendly($cur_topic['subject']))).'" title="'.
		__('Page') . ' ' . ($forum_page['page'] - 1).'" />';
	$forum_page['nav']['first'] = '<link rel="first" href="'.forum_link($forum_url['topic'], array($id, sef_friendly($cur_topic['subject']))).'" title="'.
		__('Page').' 1" />';
}

if (config()['o_censoring'] == '1')
	$cur_topic['subject'] = censor_words($cur_topic['subject']);

// Generate paging and posting links
$forum_page['page_post']['paging'] = '<p class="paging"><span class="pages">'.
	__('Pages').'</span> '.paginate($forum_page['num_pages'], $forum_page['page'], $forum_url['topic'],
		__('Paging separator'), array($id, sef_friendly($cur_topic['subject']))).'</p>';

if (user()['may_post'])
	$forum_page['page_post']['posting'] = '<p class="posting"><a class="newpost" href="'.forum_link($forum_url['new_reply'], $id).'"><span>'.
	__('Post reply', 'topic') . '</span></a></p>';
else if (user()['is_guest'])
	$forum_page['page_post']['posting'] = '<p class="posting">'.
		sprintf(__('Login to post', 'topic'), '<a href="'.forum_link($forum_url['login']).'">'.
		__('login').'</a>', '<a href="'.forum_link($forum_url['register']).'">'.
		__('register').'</a>').'</p>';
else if ($cur_topic['closed'] == '1')
	$forum_page['page_post']['posting'] = '<p class="posting">'.
		__('Topic closed info', 'topic') . '</p>';
else
	$forum_page['page_post']['posting'] = '<p class="posting">'.
		__('No permission', 'topic') . '</p>';

// Setup main options
$forum_page['main_title'] = __('Topic options', 'topic');
$forum_page['main_head_options'] = array(
	'rss' => '<span class="feed first-item"><a class="feed" href="'.forum_link($forum_url['topic_rss'], $id).'">'.
		__('RSS topic feed', 'topic') . '</a></span>'
);

if (!user()['is_guest'] && config()['o_subscriptions'] == '1')
{
	if ($cur_topic['is_subscribed'])
		$forum_page['main_head_options']['unsubscribe'] = '<span><a class="sub-option" href="'.forum_link($forum_url['unsubscribe'], array($id, generate_form_token('unsubscribe'.$id.user()['id']))).'"><em>'.
			__('Unsubscribe', 'topic') . '</em></a></span>';
	else
		$forum_page['main_head_options']['subscribe'] = '<span><a class="sub-option" href="'.forum_link($forum_url['subscribe'], array($id, generate_form_token('subscribe'.$id.user()['id']))).'" title="'.
			__('Subscribe info', 'topic') . '">'.
			__('Subscribe', 'topic') . '</a></span>';
}

if ($forum_page['is_admmod'])
{
	$forum_page['main_foot_options'] = array(
		'move' => '<span class="first-item"><a class="mod-option" href="'.forum_link($forum_url['move'], array($cur_topic['forum_id'], $id)).'">'.
			__('Move', 'topic') . '</a></span>',
		'delete' => '<span><a class="mod-option" href="'.forum_link($forum_url['delete'], $cur_topic['first_post_id']).'">'.
			__('Delete topic', 'topic') . '</a></span>',
		'close' => (($cur_topic['closed'] == '1') ? '<span><a class="mod-option" href="'.forum_link($forum_url['open'], array($cur_topic['forum_id'], $id, generate_form_token('open'.$id))).'">'.
			__('Open', 'topic') . '</a></span>' : '<span><a class="mod-option" href="'.forum_link($forum_url['close'], array($cur_topic['forum_id'], $id, generate_form_token('close'.$id))).'">'.
			__('Close', 'topic') . '</a></span>'),
		'sticky' => (($cur_topic['sticky'] == '1') ? '<span><a class="mod-option" href="'.forum_link($forum_url['unstick'], array($cur_topic['forum_id'], $id, generate_form_token('unstick'.$id))).'">'.
			__('Unstick', 'topic') . '</a></span>' : '<span><a class="mod-option" href="'.forum_link($forum_url['stick'], array($cur_topic['forum_id'], $id, generate_form_token('stick'.$id))).'">'.
			__('Stick', 'topic') . '</a></span>')
	);

	if ($cur_topic['num_replies'] != 0)
		$forum_page['main_foot_options']['moderate_topic'] = '<span><a class="mod-option" href="'.forum_sublink($forum_url['moderate_topic'], $forum_url['page'], $forum_page['page'], array($cur_topic['forum_id'], $id)).'">'.
		__('Moderate topic', 'topic') . '</a></span>';
}

// Setup breadcrumbs
$forum_page['crumbs'] = array(
	array(config()['o_board_title'], forum_link($forum_url['index'])),
	array($cur_topic['forum_name'], forum_link($forum_url['forum'], array($cur_topic['forum_id'], sef_friendly($cur_topic['forum_name'])))),
	$cur_topic['subject']
);

// Setup main heading
$forum_page['main_title'] = (($cur_topic['closed'] == '1') ?
	__('Topic closed', 'topic') . ' ' : '').'<a class="permalink" href="'.forum_link($forum_url['topic'], array($id, sef_friendly($cur_topic['subject']))).'" rel="bookmark" title="'.
	__('Permalink topic', 'topic') . '">'.forum_htmlencode($cur_topic['subject']).'</a>';

if ($forum_page['num_pages'] > 1)
	$forum_page['main_head_pages'] = sprintf(__('Page info'), $forum_page['page'], $forum_page['num_pages']);

($hook = get_hook('vt_pre_header_load')) ? eval($hook) : null;

// Allow indexing if this is a permalink
if (!$pid)
	define('FORUM_ALLOW_INDEX', 1);

define('FORUM_PAGE', 'viewtopic');

// Display quick post if enabled
if (config()['o_quickpost'] == '1' &&
	!user()['is_guest'] &&
	($cur_topic['post_replies'] == '1' || ($cur_topic['post_replies'] == '' && user()['g_post_replies'] == '1')) &&
	($cur_topic['closed'] == '0' || $forum_page['is_admmod']))
{
	$view_show_qpost = 1;
}

// Increment "num_views" for topic
if (config()['o_topic_views'] == '1')
{
	$query = array(
		'UPDATE'	=> 'topics',
		'SET'		=> 'num_views=num_views+1',
		'WHERE'		=> 'id='.$id,
	);

	($hook = get_hook('vt_qr_increment_num_views')) ? eval($hook) : null;
	db()->query_build($query) or error(__FILE__, __LINE__);
}

$forum_id = $cur_topic['forum_id'];

if (!defined('FORUM_PARSER_LOADED'))
	require FORUM_ROOT.'include/parser.php';

// 1. Retrieve the posts ids
$query = array(
	'SELECT'	=> 'p.id',
	'FROM'		=> 'posts AS p',
	'WHERE'		=> 'p.topic_id='.$id,
	'ORDER BY'	=> 'p.id',
	'LIMIT'		=> $forum_page['start_from'].','.user()['disp_posts']
);

($hook = get_hook('vt_qr_get_posts_id')) ? eval($hook) : null;
$result = db()->query_build($query) or error(__FILE__, __LINE__);
$posts_id = array();
while ($row = db()->fetch_assoc($result)) {
	$posts_id[] = $row['id'];
}

if (!empty($posts_id))
{
	// 2. Retrieve the posts (and their respective poster/online status) by known id`s
	$query = array(
		'SELECT'	=> 'u.email, u.title, u.url, u.location, u.signature, u.email_setting, u.num_posts, u.registered, u.admin_note, u.avatar, u.avatar_width, u.avatar_height, p.id, p.poster AS username, p.poster_id, p.poster_ip, p.poster_email, p.message, p.hide_smilies, p.posted, p.edited, p.edited_by, g.g_id, g.g_user_title, o.user_id AS is_online',
		'FROM'		=> 'posts AS p',
		'JOINS'		=> array(
			array(
				'INNER JOIN'	=> 'users AS u',
				'ON'			=> 'u.id=p.poster_id'
			),
			array(
				'INNER JOIN'	=> 'groups AS g',
				'ON'			=> 'g.g_id=u.group_id'
			),
			array(
				'LEFT JOIN'		=> 'online AS o',
				'ON'			=> '(o.user_id=u.id AND o.user_id!=1 AND o.idle=0)'
			),
		),
		'WHERE'		=> 'p.id IN ('.implode(',', $posts_id).')',
		'ORDER BY'	=> 'p.id'
	);

	($hook = get_hook('vt_qr_get_posts')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);
}

$forum_main_view = 'viewtopic/main';
include FORUM_ROOT . 'include/render.php';
