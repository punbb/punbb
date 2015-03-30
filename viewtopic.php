<?php
/**
 * Lists the posts in the specified topic.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */


if (!defined('FORUM_ROOT'))
	define('FORUM_ROOT', './');
require FORUM_ROOT.'include/common.php';

($hook = get_hook('vt_start')) ? eval($hook) : null;

if ($forum_user['g_read_board'] == '0')
	message($lang_common['No view']);

// Load the viewtopic.php language file
require FORUM_ROOT.'lang/'.$forum_user['language'].'/topic.php';


$action = isset($_GET['action']) ? $_GET['action'] : null;
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$pid = isset($_GET['pid']) ? intval($_GET['pid']) : 0;
if ($id < 1 && $pid < 1)
	message($lang_common['Bad request']);


// If a post ID is specified we determine topic ID and page number so we can redirect to the correct message
if ($pid)
{
	$query = array(
		'SELECT'	=> 'p.topic_id, p.posted',
		'FROM'		=> 'posts AS p',
		'WHERE'		=> 'p.id='.$pid
	);

	($hook = get_hook('vt_qr_get_post_info')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	$topic_info = $forum_db->fetch_assoc($result);

	if (!$topic_info)
	{
		message($lang_common['Bad request']);
	}

	$id = $topic_info['topic_id'];

	// Determine on what page the post is located (depending on $forum_user['disp_posts'])
	$query = array(
		'SELECT'	=> 'COUNT(p.id)',
		'FROM'		=> 'posts AS p',
		'WHERE'		=> 'p.topic_id='.$topic_info['topic_id'].' AND p.posted<'.$topic_info['posted']
	);

	($hook = get_hook('vt_qr_get_post_page')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	$num_posts = $forum_db->result($result) + 1;

	$_GET['p'] = ceil($num_posts / $forum_user['disp_posts']);
}

// If action=new, we redirect to the first new post (if any)
else if ($action == 'new')
{
	if (!$forum_user['is_guest'])
	{
		// We need to check if this topic has been viewed recently by the user
		$tracked_topics = get_tracked_topics();
		$last_viewed = isset($tracked_topics['topics'][$id]) ? $tracked_topics['topics'][$id] : $forum_user['last_visit'];

		($hook = get_hook('vt_find_new_post')) ? eval($hook) : null;

		$query = array(
			'SELECT'	=> 'MIN(p.id)',
			'FROM'		=> 'posts AS p',
			'WHERE'		=> 'p.topic_id='.$id.' AND p.posted>'.$last_viewed
		);

		($hook = get_hook('vt_qr_get_first_new_post')) ? eval($hook) : null;
		$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
		$first_new_post_id = $forum_db->result($result);

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
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	$last_post_id = $forum_db->result($result);

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
			'ON'			=> '(fp.forum_id=f.id AND fp.group_id='.$forum_user['g_id'].')'
		)
	),
	'WHERE'		=> '(fp.read_forum IS NULL OR fp.read_forum=1) AND t.id='.$id.' AND t.moved_to IS NULL'
);

if (!$forum_user['is_guest'] && $forum_config['o_subscriptions'] == '1')
{
	$query['SELECT'] .= ', s.user_id AS is_subscribed';
	$query['JOINS'][] = array(
		'LEFT JOIN'	=> 'subscriptions AS s',
		'ON'		=> '(t.id=s.topic_id AND s.user_id='.$forum_user['id'].')'
	);
}

($hook = get_hook('vt_qr_get_topic_info')) ? eval($hook) : null;
$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
$cur_topic = $forum_db->fetch_assoc($result);

if (!$cur_topic)
{
	message($lang_common['Bad request']);
}

($hook = get_hook('vt_modify_topic_info')) ? eval($hook) : null;

// Sort out who the moderators are and if we are currently a moderator (or an admin)
$mods_array = ($cur_topic['moderators'] != '') ? unserialize($cur_topic['moderators']) : array();
$forum_page['is_admmod'] = ($forum_user['g_id'] == FORUM_ADMIN || ($forum_user['g_moderator'] == '1' && array_key_exists($forum_user['username'], $mods_array))) ? true : false;

// Can we or can we not post replies?
if ($cur_topic['closed'] == '0' || $forum_page['is_admmod'])
	$forum_user['may_post'] = (($cur_topic['post_replies'] == '' && $forum_user['g_post_replies'] == '1') || $cur_topic['post_replies'] == '1' || $forum_page['is_admmod']) ? true : false;
else
	$forum_user['may_post'] = false;

// Add/update this topic in our list of tracked topics
if (!$forum_user['is_guest'])
{
	$tracked_topics = get_tracked_topics();
	$tracked_topics['topics'][$id] = time();
	set_tracked_topics($tracked_topics);
}

// Determine the post offset (based on $_GET['p'])
$forum_page['num_pages'] = ceil(($cur_topic['num_replies'] + 1) / $forum_user['disp_posts']);
$forum_page['page'] = (!isset($_GET['p']) || !is_numeric($_GET['p']) || $_GET['p'] <= 1 || $_GET['p'] > $forum_page['num_pages']) ? 1 : $_GET['p'];
$forum_page['start_from'] = $forum_user['disp_posts'] * ($forum_page['page'] - 1);
$forum_page['finish_at'] = min(($forum_page['start_from'] + $forum_user['disp_posts']), ($cur_topic['num_replies'] + 1));
$forum_page['items_info'] = generate_items_info($lang_topic['Posts'], ($forum_page['start_from'] + 1), ($cur_topic['num_replies'] + 1));

($hook = get_hook('vt_modify_page_details')) ? eval($hook) : null;

// Navigation links for header and page numbering for title/meta description
if ($forum_page['page'] < $forum_page['num_pages'])
{
	$forum_page['nav']['last'] = '<link rel="last" href="'.forum_sublink($forum_url['topic'], $forum_url['page'], $forum_page['num_pages'], array($id, sef_friendly($cur_topic['subject']))).'" title="'.$lang_common['Page'].' '.$forum_page['num_pages'].'" />';
	$forum_page['nav']['next'] = '<link rel="next" href="'.forum_sublink($forum_url['topic'], $forum_url['page'], ($forum_page['page'] + 1), array($id, sef_friendly($cur_topic['subject']))).'" title="'.$lang_common['Page'].' '.($forum_page['page'] + 1).'" />';
}
if ($forum_page['page'] > 1)
{
	$forum_page['nav']['prev'] = '<link rel="prev" href="'.forum_sublink($forum_url['topic'], $forum_url['page'], ($forum_page['page'] - 1), array($id, sef_friendly($cur_topic['subject']))).'" title="'.$lang_common['Page'].' '.($forum_page['page'] - 1).'" />';
	$forum_page['nav']['first'] = '<link rel="first" href="'.forum_link($forum_url['topic'], array($id, sef_friendly($cur_topic['subject']))).'" title="'.$lang_common['Page'].' 1" />';
}

if ($forum_config['o_censoring'] == '1')
	$cur_topic['subject'] = censor_words($cur_topic['subject']);

// Generate paging and posting links
$forum_page['page_post']['paging'] = '<p class="paging"><span class="pages">'.$lang_common['Pages'].'</span> '.paginate($forum_page['num_pages'], $forum_page['page'], $forum_url['topic'], $lang_common['Paging separator'], array($id, sef_friendly($cur_topic['subject']))).'</p>';

if ($forum_user['may_post'])
	$forum_page['page_post']['posting'] = '<p class="posting"><a class="newpost" href="'.forum_link($forum_url['new_reply'], $id).'"><span>'.$lang_topic['Post reply'].'</span></a></p>';
else if ($forum_user['is_guest'])
	$forum_page['page_post']['posting'] = '<p class="posting">'.sprintf($lang_topic['Login to post'], '<a href="'.forum_link($forum_url['login']).'">'.$lang_common['login'].'</a>', '<a href="'.forum_link($forum_url['register']).'">'.$lang_common['register'].'</a>').'</p>';
else if ($cur_topic['closed'] == '1')
	$forum_page['page_post']['posting'] = '<p class="posting">'.$lang_topic['Topic closed info'].'</p>';
else
	$forum_page['page_post']['posting'] = '<p class="posting">'.$lang_topic['No permission'].'</p>';

// Setup main options
$forum_page['main_title'] = $lang_topic['Topic options'];
$forum_page['main_head_options'] = array(
	'rss' => '<span class="feed first-item"><a class="feed" href="'.forum_link($forum_url['topic_rss'], $id).'">'.$lang_topic['RSS topic feed'].'</a></span>'
);

if (!$forum_user['is_guest'] && $forum_config['o_subscriptions'] == '1')
{
	if ($cur_topic['is_subscribed'])
		$forum_page['main_head_options']['unsubscribe'] = '<span><a class="sub-option" href="'.forum_link($forum_url['unsubscribe'], array($id, generate_form_token('unsubscribe'.$id.$forum_user['id']))).'"><em>'.$lang_topic['Unsubscribe'].'</em></a></span>';
	else
		$forum_page['main_head_options']['subscribe'] = '<span><a class="sub-option" href="'.forum_link($forum_url['subscribe'], array($id, generate_form_token('subscribe'.$id.$forum_user['id']))).'" title="'.$lang_topic['Subscribe info'].'">'.$lang_topic['Subscribe'].'</a></span>';
}

if ($forum_page['is_admmod'])
{
	$forum_page['main_foot_options'] = array(
		'move' => '<span class="first-item"><a class="mod-option" href="'.forum_link($forum_url['move'], array($cur_topic['forum_id'], $id)).'">'.$lang_topic['Move'].'</a></span>',
		'delete' => '<span><a class="mod-option" href="'.forum_link($forum_url['delete'], $cur_topic['first_post_id']).'">'.$lang_topic['Delete topic'].'</a></span>',
		'close' => (($cur_topic['closed'] == '1') ? '<span><a class="mod-option" href="'.forum_link($forum_url['open'], array($cur_topic['forum_id'], $id, generate_form_token('open'.$id))).'">'.$lang_topic['Open'].'</a></span>' : '<span><a class="mod-option" href="'.forum_link($forum_url['close'], array($cur_topic['forum_id'], $id, generate_form_token('close'.$id))).'">'.$lang_topic['Close'].'</a></span>'),
		'sticky' => (($cur_topic['sticky'] == '1') ? '<span><a class="mod-option" href="'.forum_link($forum_url['unstick'], array($cur_topic['forum_id'], $id, generate_form_token('unstick'.$id))).'">'.$lang_topic['Unstick'].'</a></span>' : '<span><a class="mod-option" href="'.forum_link($forum_url['stick'], array($cur_topic['forum_id'], $id, generate_form_token('stick'.$id))).'">'.$lang_topic['Stick'].'</a></span>')
	);

	if ($cur_topic['num_replies'] != 0)
		$forum_page['main_foot_options']['moderate_topic'] = '<span><a class="mod-option" href="'.forum_sublink($forum_url['moderate_topic'], $forum_url['page'], $forum_page['page'], array($cur_topic['forum_id'], $id)).'">'.$lang_topic['Moderate topic'].'</a></span>';
}

// Setup breadcrumbs
$forum_page['crumbs'] = array(
	array($forum_config['o_board_title'], forum_link($forum_url['index'])),
	array($cur_topic['forum_name'], forum_link($forum_url['forum'], array($cur_topic['forum_id'], sef_friendly($cur_topic['forum_name'])))),
	$cur_topic['subject']
);

// Setup main heading
$forum_page['main_title'] = (($cur_topic['closed'] == '1') ? $lang_topic['Topic closed'].' ' : '').'<a class="permalink" href="'.forum_link($forum_url['topic'], array($id, sef_friendly($cur_topic['subject']))).'" rel="bookmark" title="'.$lang_topic['Permalink topic'].'">'.forum_htmlencode($cur_topic['subject']).'</a>';

if ($forum_page['num_pages'] > 1)
	$forum_page['main_head_pages'] = sprintf($lang_common['Page info'], $forum_page['page'], $forum_page['num_pages']);

($hook = get_hook('vt_pre_header_load')) ? eval($hook) : null;

// Allow indexing if this is a permalink
if (!$pid)
	define('FORUM_ALLOW_INDEX', 1);

define('FORUM_PAGE', 'viewtopic');
require FORUM_ROOT.'header.php';

$view_forum_main = 'viewtopic/main';

ob_start();
include view($view_forum_layout);
$tpl_main = forum_trim(ob_get_contents());
ob_end_clean();

// Display quick post if enabled
if ($forum_config['o_quickpost'] == '1' &&
	!$forum_user['is_guest'] &&
	($cur_topic['post_replies'] == '1' || ($cur_topic['post_replies'] == '' && $forum_user['g_post_replies'] == '1')) &&
	($cur_topic['closed'] == '0' || $forum_page['is_admmod']))
{
	$view_show_qpost = 1;
}

// Increment "num_views" for topic
if ($forum_config['o_topic_views'] == '1')
{
	$query = array(
		'UPDATE'	=> 'topics',
		'SET'		=> 'num_views=num_views+1',
		'WHERE'		=> 'id='.$id,
	);

	($hook = get_hook('vt_qr_increment_num_views')) ? eval($hook) : null;
	$forum_db->query_build($query) or error(__FILE__, __LINE__);
}

$forum_id = $cur_topic['forum_id'];

require FORUM_ROOT.'footer.php';
