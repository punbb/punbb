<?php
/**
 * Lists the topics in the specified forum.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */
namespace punbb;

require __DIR__ . '/vendor/pautoload.php';

($hook = get_hook('vf_start')) ? eval($hook) : null;

if (user()->g_read_board == '0') {
	message(__('No view'));
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id < 1)
	message(__('Bad request'));

// Fetch some info about the forum
$query = array(
	'SELECT'	=> 'f.forum_name, f.redirect_url, f.moderators, f.num_topics, f.sort_by, fp.post_topics',
	'FROM'		=> 'forums AS f',
	'JOINS'		=> array(
		array(
			'LEFT JOIN'		=> 'forum_perms AS fp',
			'ON'			=> '(fp.forum_id=f.id AND fp.group_id='.user()->g_id.')'
		)
	),
	'WHERE'		=> '(fp.read_forum IS NULL OR fp.read_forum=1) AND f.id='.$id
);

if (!user()->is_guest && config()->o_subscriptions == '1')
{
	$query['SELECT'] .= ', fs.user_id AS is_subscribed';
	$query['JOINS'][] = array(
		'LEFT JOIN'	=> 'forum_subscriptions AS fs',
		'ON'		=> '(f.id=fs.forum_id AND fs.user_id='.user()->id.')'
	);
}

($hook = get_hook('vf_qr_get_forum_info')) ? eval($hook) : null;
$result = db()->query_build($query) or error(__FILE__, __LINE__);
$cur_forum = db()->fetch_assoc($result);

if (!$cur_forum)
	message(__('Bad request'));


($hook = get_hook('vf_modify_forum_info')) ? eval($hook) : null;

// Is this a redirect forum? In that case, redirect!
if ($cur_forum['redirect_url'] != '')
{
	($hook = get_hook('vf_redirect_forum_pre_redirect')) ? eval($hook) : null;

	header('Location: '.$cur_forum['redirect_url']);
	exit;
}

// Sort out who the moderators are and if we are currently a moderator (or an admin)
$mods_array = ($cur_forum['moderators'] != '') ? unserialize($cur_forum['moderators']) : array();
$forum_page['is_admmod'] = (user()->g_id == FORUM_ADMIN || (user()->g_moderator == '1' && array_key_exists(user()->username, $mods_array))) ? true : false;

// Sort out whether or not this user can post
user()->may_post = (($cur_forum['post_topics'] == '' && user()->g_post_topics == '1') || $cur_forum['post_topics'] == '1' || $forum_page['is_admmod']) ? true : false;

// Get topic/forum tracking data
if (!user()->is_guest)
	$tracked_topics = get_tracked_topics();

// Determine the topic offset (based on $_GET['p'])
$forum_page['num_pages'] = ceil($cur_forum['num_topics'] / user()->disp_topics);
$forum_page['page'] = (!isset($_GET['p']) || !is_numeric($_GET['p']) || $_GET['p'] <= 1 || $_GET['p'] > $forum_page['num_pages']) ? 1 : $_GET['p'];
$forum_page['start_from'] = user()->disp_topics * ($forum_page['page'] - 1);
$forum_page['finish_at'] = min(($forum_page['start_from'] + user()->disp_topics), ($cur_forum['num_topics']));
$forum_page['items_info'] = generate_items_info(__('Topics', 'forum'), ($forum_page['start_from'] + 1), $cur_forum['num_topics']);

($hook = get_hook('vf_modify_page_details')) ? eval($hook) : null;

// Navigation links for header and page numbering for title/meta description
if ($forum_page['page'] < $forum_page['num_pages'])
{
	$forum_page['nav']['last'] = '<link rel="last" href="'.forum_sublink($forum_url['forum'], $forum_url['page'], $forum_page['num_pages'], array($id, sef_friendly($cur_forum['forum_name']))).'" title="'.
		__('Page') . ' ' . $forum_page['num_pages'].'" />';
	$forum_page['nav']['next'] = '<link rel="next" href="'.forum_sublink($forum_url['forum'], $forum_url['page'], ($forum_page['page'] + 1), array($id, sef_friendly($cur_forum['forum_name']))).'" title="'.
		__('Page') . ' ' . ($forum_page['page'] + 1).'" />';
}
if ($forum_page['page'] > 1)
{
	$forum_page['nav']['prev'] = '<link rel="prev" href="'.forum_sublink($forum_url['forum'], $forum_url['page'], ($forum_page['page'] - 1), array($id, sef_friendly($cur_forum['forum_name']))).'" title="'.
		__('Page') . ' ' . ($forum_page['page'] - 1).'" />';
	$forum_page['nav']['first'] = '<link rel="first" href="'.forum_link($forum_url['forum'], array($id, sef_friendly($cur_forum['forum_name']))).'" title="'.
	__('Page') . ' 1" />';
}


// 1. Retrieve the topics id
$query = array(
	'SELECT'	=> 't.id',
	'FROM'		=> 'topics AS t',
	'WHERE'		=> 't.forum_id='.$id,
	'ORDER BY'	=> 't.sticky DESC, '.(($cur_forum['sort_by'] == '1') ? 't.posted' : 't.last_post').' DESC',
	'LIMIT'		=> $forum_page['start_from'].', '.user()->disp_topics
);

($hook = get_hook('vt_qr_get_topics_id')) ? eval($hook) : null;
$result = db()->query_build($query) or error(__FILE__, __LINE__);

$topics_id = $topics = array();
while ($row = db()->fetch_assoc($result)) {
	$topics_id[] = $row['id'];
}

// If there are topics id in this forum
if (!empty($topics_id))
{
	/*
	 * Fetch list of topics
	 * EXT DEVELOPERS
	 * If you modify SELECT of this query - than add same columns in next query (has posted) in GROUP BY
	*/
	$query = array(
		'SELECT'	=> 't.id, t.poster, t.subject, t.posted, t.first_post_id, t.last_post, t.last_post_id, t.last_poster, t.num_views, t.num_replies, t.closed, t.sticky, t.moved_to',
		'FROM'		=> 'topics AS t',
		'WHERE'		=> 't.id IN ('.implode(',', $topics_id).')',
		'ORDER BY'	=> 't.sticky DESC, '.(($cur_forum['sort_by'] == '1') ? 't.posted' : 't.last_post').' DESC',
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
		$query['GROUP BY'] = 't.id, t.poster, t.subject, t.posted, t.first_post_id, t.last_post, t.last_post_id, t.last_poster, t.num_views, t.num_replies, t.closed, t.sticky, t.moved_to, p.poster_id';

		($hook = get_hook('vf_qr_get_has_posted')) ? eval($hook) : null;
	}

	($hook = get_hook('vf_qr_get_topics')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);

	while ($cur_topic = db()->fetch_assoc($result))
	{
		$topics[] = $cur_topic;
	}
}

// Generate paging/posting links
$forum_page['page_post']['paging'] = '<p class="paging"><span class="pages">'.
	__('Pages').'</span> '.paginate($forum_page['num_pages'], $forum_page['page'], $forum_url['forum'],
		__('Paging separator'), array($id, sef_friendly($cur_forum['forum_name']))).'</p>';

if (user()->may_post)
	$forum_page['page_post']['posting'] = '<p class="posting"><a class="newpost" href="'.forum_link($forum_url['new_topic'], $id).'"><span>'.
	__('Post topic', 'forum') . '</span></a></p>';
else if (user()->is_guest)
	$forum_page['page_post']['posting'] = '<p class="posting">'.
	sprintf(__('Login to post', 'forum'), '<a href="'.forum_link($forum_url['login']).'">'.
		__('login').'</a>', '<a href="'.forum_link($forum_url['register']).'">'.__('register').'</a>').'</p>';
else
	$forum_page['page_post']['posting'] = '<p class="posting">'.
	__('No permission', 'forum') . '</p>';

// Setup main options
$forum_page['main_head_options'] = $forum_page['main_foot_options'] = array();

if (!empty($topics))
	$forum_page['main_head_options']['feed'] = '<span class="feed first-item"><a class="feed" href="'.forum_link($forum_url['forum_rss'], $id).'">'.
	__('RSS forum feed', 'forum') . '</a></span>';

if (!user()->is_guest && config()->o_subscriptions == '1')
{
	if ($cur_forum['is_subscribed'])
		$forum_page['main_head_options']['unsubscribe'] = '<span><a class="sub-option" href="'.forum_link($forum_url['forum_unsubscribe'], array($id, generate_form_token('forum_unsubscribe'.$id.user()->id))).'"><em>'.
		__('Unsubscribe', 'forum') . '</em></a></span>';
	else
		$forum_page['main_head_options']['subscribe'] = '<span><a class="sub-option" href="'.forum_link($forum_url['forum_subscribe'], array($id, generate_form_token('forum_subscribe'.$id.user()->id))).'" title="'.
		__('Subscribe info', 'forum') . '">' . __('Subscribe', 'forum') . '</a></span>';
}

if (!user()->is_guest && !empty($topics))
{
	$forum_page['main_foot_options']['mark_read'] = '<span class="first-item"><a href="'.forum_link($forum_url['mark_forum_read'], array($id, generate_form_token('markforumread'.$id.user()->id))).'">'.
		__('Mark forum read', 'forum') . '</a></span>';

	if ($forum_page['is_admmod'])
		$forum_page['main_foot_options']['moderate'] = '<span'.(empty($forum_page['main_foot_options']) ? ' class="first-item"' : '').'><a href="'.forum_sublink($forum_url['moderate_forum'], $forum_url['page'], $forum_page['page'], $id).'">'.
		__('Moderate forum', 'forum') . '</a></span>';
}

// Setup breadcrumbs
$forum_page['crumbs'] = array(
	array(config()->o_board_title, forum_link($forum_url['index'])),
	$cur_forum['forum_name']
);

// Setup main header
$forum_page['main_title'] = '<a class="permalink" href="'.forum_link($forum_url['forum'], array($id, sef_friendly($cur_forum['forum_name']))).'" rel="bookmark" title="'.
	__('Permalink forum', 'forum') . '">'.forum_htmlencode($cur_forum['forum_name']).'</a>';

if ($forum_page['num_pages'] > 1)
	$forum_page['main_head_pages'] = sprintf(__('Page info'), $forum_page['page'], $forum_page['num_pages']);

($hook = get_hook('vf_pre_header_load')) ? eval($hook) : null;

define('FORUM_ALLOW_INDEX', 1);

define('FORUM_PAGE', 'viewforum');
$forum_id = $id;

$forum_main_view = 'viewforum/main';
template()->render();
