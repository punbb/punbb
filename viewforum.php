<?php
/**
 * Lists the topics in the specified forum.
 *
 * @copyright (C) 2008-2011 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */


if (!defined('FORUM_ROOT'))
	define('FORUM_ROOT', './');
require FORUM_ROOT.'include/common.php';

($hook = get_hook('vf_start')) ? eval($hook) : null;

if ($forum_user['g_read_board'] == '0')
	message($lang_common['No view']);

// Load the viewforum.php language file
require FORUM_ROOT.'lang/'.$forum_user['language'].'/forum.php';


$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id < 1)
	message($lang_common['Bad request']);


// Fetch some info about the forum
$query = array(
	'SELECT'	=> 'f.forum_name, f.redirect_url, f.moderators, f.num_topics, f.sort_by, fp.post_topics',
	'FROM'		=> 'forums AS f',
	'JOINS'		=> array(
		array(
			'LEFT JOIN'		=> 'forum_perms AS fp',
			'ON'			=> '(fp.forum_id=f.id AND fp.group_id='.$forum_user['g_id'].')'
		)
	),
	'WHERE'		=> '(fp.read_forum IS NULL OR fp.read_forum=1) AND f.id='.$id
);

($hook = get_hook('vf_qr_get_forum_info')) ? eval($hook) : null;
$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
$cur_forum = $forum_db->fetch_assoc($result);

if (!$cur_forum)
	message($lang_common['Bad request']);


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
$forum_page['is_admmod'] = ($forum_user['g_id'] == FORUM_ADMIN || ($forum_user['g_moderator'] == '1' && array_key_exists($forum_user['username'], $mods_array))) ? true : false;

// Sort out whether or not this user can post
$forum_user['may_post'] = (($cur_forum['post_topics'] == '' && $forum_user['g_post_topics'] == '1') || $cur_forum['post_topics'] == '1' || $forum_page['is_admmod']) ? true : false;

// Get topic/forum tracking data
if (!$forum_user['is_guest'])
	$tracked_topics = get_tracked_topics();

// Determine the topic offset (based on $_GET['p'])
$forum_page['num_pages'] = ceil($cur_forum['num_topics'] / $forum_user['disp_topics']);
$forum_page['page'] = (!isset($_GET['p']) || !is_numeric($_GET['p']) || $_GET['p'] <= 1 || $_GET['p'] > $forum_page['num_pages']) ? 1 : $_GET['p'];
$forum_page['start_from'] = $forum_user['disp_topics'] * ($forum_page['page'] - 1);
$forum_page['finish_at'] = min(($forum_page['start_from'] + $forum_user['disp_topics']), ($cur_forum['num_topics']));
$forum_page['items_info'] = generate_items_info($lang_forum['Topics'], ($forum_page['start_from'] + 1), $cur_forum['num_topics']);

($hook = get_hook('vf_modify_page_details')) ? eval($hook) : null;

// Navigation links for header and page numbering for title/meta description
if ($forum_page['page'] < $forum_page['num_pages'])
{
	$forum_page['nav']['last'] = '<link rel="last" href="'.forum_sublink($forum_url['forum'], $forum_url['page'], $forum_page['num_pages'], array($id, sef_friendly($cur_forum['forum_name']))).'" title="'.$lang_common['Page'].' '.$forum_page['num_pages'].'" />';
	$forum_page['nav']['next'] = '<link rel="next" href="'.forum_sublink($forum_url['forum'], $forum_url['page'], ($forum_page['page'] + 1), array($id, sef_friendly($cur_forum['forum_name']))).'" title="'.$lang_common['Page'].' '.($forum_page['page'] + 1).'" />';
}
if ($forum_page['page'] > 1)
{
	$forum_page['nav']['prev'] = '<link rel="prev" href="'.forum_sublink($forum_url['forum'], $forum_url['page'], ($forum_page['page'] - 1), array($id, sef_friendly($cur_forum['forum_name']))).'" title="'.$lang_common['Page'].' '.($forum_page['page'] - 1).'" />';
	$forum_page['nav']['first'] = '<link rel="first" href="'.forum_link($forum_url['forum'], array($id, sef_friendly($cur_forum['forum_name']))).'" title="'.$lang_common['Page'].' 1" />';
}


/*
 * Fetch list of topics
 * EXT DEVELOPERS
 * If you modify SELECT of this query - than add same columns in next query (has posted) in GROUP BY
*/
$query = array(
	'SELECT'	=> 't.id, t.poster, t.subject, t.posted, t.first_post_id, t.last_post, t.last_post_id, t.last_poster, t.num_views, t.num_replies, t.closed, t.sticky, t.moved_to',
	'FROM'		=> 'topics AS t',
	'WHERE'		=> 't.forum_id='.$id,
	'ORDER BY'	=> 't.sticky DESC, '.(($cur_forum['sort_by'] == '1') ? 't.posted' : 't.last_post').' DESC',
	'LIMIT'		=> $forum_page['start_from'].', '.$forum_user['disp_topics']
);

// With "has posted" indication
if (!$forum_user['is_guest'] && $forum_config['o_show_dot'] == '1')
{
	$query['SELECT'] .= ', COALESCE(p.id, 0) AS has_posted';
	$query['JOINS'][]	= array(
		'LEFT JOIN'		=> 'posts AS p',
		'ON'			=> '(p.poster_id='.$forum_user['id'].' AND p.topic_id=t.id)'
	);

	// Must have same columns as in prev SELECT
	$query['GROUP BY'] = 't.id, t.poster, t.subject, t.posted, t.first_post_id, t.last_post, t.last_post_id, t.last_poster, t.num_views, t.num_replies, t.closed, t.sticky, t.moved_to';

	($hook = get_hook('vf_qr_get_has_posted')) ? eval($hook) : null;
}

($hook = get_hook('vf_qr_get_topics')) ? eval($hook) : null;
$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

$topics = array();
while ($cur_topic = $forum_db->fetch_assoc($result))
{
	$topics[] = $cur_topic;
}

// Generate paging/posting links
$forum_page['page_post']['paging'] = '<p class="paging"><span class="pages">'.$lang_common['Pages'].'</span> '.paginate($forum_page['num_pages'], $forum_page['page'], $forum_url['forum'], $lang_common['Paging separator'], array($id, sef_friendly($cur_forum['forum_name']))).'</p>';

if ($forum_user['may_post'])
	$forum_page['page_post']['posting'] = '<p class="posting"><a class="newpost" href="'.forum_link($forum_url['new_topic'], $id).'"><span>'.$lang_forum['Post topic'].'</span></a></p>';
else if ($forum_user['is_guest'])
	$forum_page['page_post']['posting'] = '<p class="posting">'.sprintf($lang_forum['Login to post'], '<a href="'.forum_link($forum_url['login']).'">'.$lang_common['login'].'</a>', '<a href="'.forum_link($forum_url['register']).'">'.$lang_common['register'].'</a>').'</p>';
else
	$forum_page['page_post']['posting'] = '<p class="posting">'.$lang_forum['No permission'].'</p>';

// Setup main options
$forum_page['main_head_options'] = array(
	'feed'	=> '<span class="feed first-item"><a class="feed" href="'.forum_link($forum_url['forum_rss'], $id).'">'.$lang_forum['RSS forum feed'].'</a></span>'
);

$forum_page['main_foot_options'] = array();
if (!$forum_user['is_guest'] && !empty($topics))
{
	$forum_page['main_foot_options']['mark_read'] = '<span class="first-item"><a href="'.forum_link($forum_url['mark_forum_read'], array($id, generate_form_token('markforumread'.$id.$forum_user['id']))).'">'.$lang_forum['Mark forum read'].'</a></span>';

	if ($forum_page['is_admmod'])
		$forum_page['main_foot_options']['moderate'] = '<span'.(empty($forum_page['main_foot_options']) ? ' class="first-item"' : '').'><a href="'.forum_sublink($forum_url['moderate_forum'], $forum_url['page'], $forum_page['page'], $id).'">'.$lang_forum['Moderate forum'].'</a></span>';
}

// Setup breadcrumbs
$forum_page['crumbs'] = array(
	array($forum_config['o_board_title'], forum_link($forum_url['index'])),
	array($cur_forum['forum_name'], forum_link($forum_url['forum'], array($id, sef_friendly($cur_forum['forum_name']))))
);

// Setup main header
$forum_page['main_title'] = '<a class="permalink" href="'.forum_link($forum_url['forum'], array($id, sef_friendly($cur_forum['forum_name']))).'" rel="bookmark" title="'.$lang_forum['Permalink forum'].'">'.forum_htmlencode($cur_forum['forum_name']).'</a>';

if ($forum_page['num_pages'] > 1)
	$forum_page['main_head_pages'] = sprintf($lang_common['Page info'], $forum_page['page'], $forum_page['num_pages']);

($hook = get_hook('vf_pre_header_load')) ? eval($hook) : null;

define('FORUM_ALLOW_INDEX', 1);

define('FORUM_PAGE', 'viewforum');
require FORUM_ROOT.'header.php';

// START SUBST - <!-- forum_main -->
ob_start();

$forum_page['item_header'] = array();
$forum_page['item_header']['subject']['title'] = '<strong class="subject-title">'.$lang_forum['Topics'].'</strong>';
$forum_page['item_header']['info']['replies'] = '<strong class="info-replies">'.$lang_forum['replies'].'</strong>';

if ($forum_config['o_topic_views'] == '1')
	$forum_page['item_header']['info']['views'] = '<strong class="info-views">'.$lang_forum['views'].'</strong>';

$forum_page['item_header']['info']['lastpost'] = '<strong class="info-lastpost">'.$lang_forum['last post'].'</strong>';

($hook = get_hook('vf_main_output_start')) ? eval($hook) : null;

// If there are topics in this forum
if (!empty($topics))
{

?>
	<div class="main-head">
<?php

	if (!empty($forum_page['main_head_options']))
		echo "\n\t\t".'<p class="options">'.implode(' ', $forum_page['main_head_options']).'</p>';

?>
		<h2 class="hn"><span><?php echo $forum_page['items_info'] ?></span></h2>
	</div>
	<div class="main-subhead">
		<p class="item-summary<?php echo ($forum_config['o_topic_views'] == '1') ? ' forum-views' : ' forum-noview' ?>"><span><?php printf($lang_forum['Forum subtitle'], implode(' ', $forum_page['item_header']['subject']), implode(', ', $forum_page['item_header']['info'])) ?></span></p>
	</div>
	<div id="forum<?php echo $id ?>" class="main-content main-forum<?php echo ($forum_config['o_topic_views'] == '1') ? ' forum-views' : ' forum-noview' ?>">
<?php

	($hook = get_hook('vf_pre_topic_loop_start')) ? eval($hook) : null;

	$forum_page['item_count'] = 0;

	foreach ($topics as $cur_topic)
	{
		($hook = get_hook('vf_topic_loop_start')) ? eval($hook) : null;

		++$forum_page['item_count'];

		// Start from scratch
		$forum_page['item_subject'] = $forum_page['item_body'] = $forum_page['item_status'] = $forum_page['item_nav'] = $forum_page['item_title'] = $forum_page['item_title_status'] = array();

		if ($forum_config['o_censoring'] == '1')
			$cur_topic['subject'] = censor_words($cur_topic['subject']);

		$forum_page['item_subject']['starter'] = '<span class="item-starter">'.sprintf($lang_forum['Topic starter'], forum_htmlencode($cur_topic['poster'])).'</span>';

		if ($cur_topic['moved_to'] != null)
		{
			$forum_page['item_status']['moved'] = 'moved';
			$forum_page['item_title']['link'] = '<span class="item-status"><em class="moved">'.sprintf($lang_forum['Item status'], $lang_forum['Moved']).'</em></span> <a href="'.forum_link($forum_url['topic'], array($cur_topic['moved_to'], sef_friendly($cur_topic['subject']))).'">'.forum_htmlencode($cur_topic['subject']).'</a>';

			// Combine everything to produce the Topic heading
			$forum_page['item_body']['subject']['title'] = '<h3 class="hn"><span class="item-num">'.forum_number_format($forum_page['start_from'] + $forum_page['item_count']).'</span>'.$forum_page['item_title']['link'].'</h3>';

			($hook = get_hook('vf_topic_loop_moved_topic_pre_item_subject_merge')) ? eval($hook) : null;

			$forum_page['item_body']['info']['replies'] = '<li class="info-replies"><span class="label">'.$lang_forum['No replies info'].'</span></li>';

			if ($forum_config['o_topic_views'] == '1')
				$forum_page['item_body']['info']['views'] = '<li class="info-views"><span class="label">'.$lang_forum['No views info'].'</span></li>';

			$forum_page['item_body']['info']['lastpost'] = '<li class="info-lastpost"><span class="label">'.$lang_forum['No lastpost info'].'</span></li>';
		}
		else
		{
			// Assemble the Topic heading

			// Should we display the dot or not? :)
			if (!$forum_user['is_guest'] && $forum_config['o_show_dot'] == '1' && $cur_topic['has_posted'] > 0)
			{
				$forum_page['item_title']['posted'] = '<span class="posted-mark">'.$lang_forum['You posted indicator'].'</span>';
				$forum_page['item_status']['posted'] = 'posted';
			}

			if ($cur_topic['sticky'] == '1')
			{
				$forum_page['item_title_status']['sticky'] = '<em class="sticky">'.$lang_forum['Sticky'].'</em>';
				$forum_page['item_status']['sticky'] = 'sticky';
			}

			if ($cur_topic['closed'] == '1')
			{
				$forum_page['item_title_status']['closed'] = '<em class="closed">'.$lang_forum['Closed'].'</em>';
				$forum_page['item_status']['closed'] = 'closed';
			}

			($hook = get_hook('vf_topic_loop_normal_topic_pre_item_title_status_merge')) ? eval($hook) : null;

			if (!empty($forum_page['item_title_status']))
				$forum_page['item_title']['status'] = '<span class="item-status">'.sprintf($lang_forum['Item status'], implode(', ', $forum_page['item_title_status'])).'</span>';

			$forum_page['item_title']['link'] = '<a href="'.forum_link($forum_url['topic'], array($cur_topic['id'], sef_friendly($cur_topic['subject']))).'">'.forum_htmlencode($cur_topic['subject']).'</a>';

			($hook = get_hook('vf_topic_loop_normal_topic_pre_item_title_merge')) ? eval($hook) : null;

			$forum_page['item_body']['subject']['title'] = '<h3 class="hn"><span class="item-num">'.forum_number_format($forum_page['start_from'] + $forum_page['item_count']).'</span> '.implode(' ', $forum_page['item_title']).'</h3>';

			if (empty($forum_page['item_status']))
				$forum_page['item_status']['normal'] = 'normal';

			$forum_page['item_pages'] = ceil(($cur_topic['num_replies'] + 1) / $forum_user['disp_posts']);

			if ($forum_page['item_pages'] > 1)
				$forum_page['item_nav']['pages'] = '<span>'.$lang_forum['Pages'].'&#160;</span>'.paginate($forum_page['item_pages'], -1, $forum_url['topic'], $lang_common['Page separator'], array($cur_topic['id'], sef_friendly($cur_topic['subject'])));

			// Does this topic contain posts we haven't read? If so, tag it accordingly.
			if (!$forum_user['is_guest'] && $cur_topic['last_post'] > $forum_user['last_visit'] && (!isset($tracked_topics['topics'][$cur_topic['id']]) || $tracked_topics['topics'][$cur_topic['id']] < $cur_topic['last_post']) && (!isset($tracked_topics['forums'][$id]) || $tracked_topics['forums'][$id] < $cur_topic['last_post']))
			{
				$forum_page['item_nav']['new'] = '<em class="item-newposts"><a href="'.forum_link($forum_url['topic_new_posts'], array($cur_topic['id'], sef_friendly($cur_topic['subject']))).'">'.$lang_forum['New posts'].'</a></em>';
				$forum_page['item_status']['new'] = 'new';
			}

			($hook = get_hook('vf_topic_loop_normal_topic_pre_item_nav_merge')) ? eval($hook) : null;

			if (!empty($forum_page['item_nav']))
				$forum_page['item_subject']['nav'] = '<span class="item-nav">'.sprintf($lang_forum['Topic navigation'], implode('&#160;&#160;', $forum_page['item_nav'])).'</span>';

			// Assemble the Topic subject

			$forum_page['item_body']['info']['replies'] = '<li class="info-replies"><strong>'.forum_number_format($cur_topic['num_replies']).'</strong> <span class="label">'.(($cur_topic['num_replies'] == 1) ? $lang_forum['reply'] : $lang_forum['replies']).'</span></li>';

			if ($forum_config['o_topic_views'] == '1')
				$forum_page['item_body']['info']['views'] = '<li class="info-views"><strong>'.forum_number_format($cur_topic['num_views']).'</strong> <span class="label">'.(($cur_topic['num_views'] == 1) ? $lang_forum['view'] : $lang_forum['views']).'</span></li>';

			$forum_page['item_body']['info']['lastpost'] = '<li class="info-lastpost"><span class="label">'.$lang_forum['Last post'].'</span> <strong><a href="'.forum_link($forum_url['post'], $cur_topic['last_post_id']).'">'.format_time($cur_topic['last_post']).'</a></strong> <cite>'.sprintf($lang_forum['by poster'], forum_htmlencode($cur_topic['last_poster'])).'</cite></li>';
		}

		($hook = get_hook('vf_row_pre_item_subject_merge')) ? eval($hook) : null;

		$forum_page['item_body']['subject']['desc'] = '<p>'.implode(' ', $forum_page['item_subject']).'</p>';

		($hook = get_hook('vf_row_pre_item_status_merge')) ? eval($hook) : null;

		$forum_page['item_style'] = (($forum_page['item_count'] % 2 != 0) ? ' odd' : ' even').(($forum_page['item_count'] == 1) ? ' main-first-item' : '').((!empty($forum_page['item_status'])) ? ' '.implode(' ', $forum_page['item_status']) : '');

		($hook = get_hook('vf_row_pre_display')) ? eval($hook) : null;

?>
		<div id="topic<?php echo $cur_topic['id'] ?>" class="main-item<?php echo $forum_page['item_style'] ?>">
			<span class="icon <?php echo implode(' ', $forum_page['item_status']) ?>"><!-- --></span>
			<div class="item-subject">
				<?php echo implode("\n\t\t\t\t", $forum_page['item_body']['subject'])."\n" ?>
			</div>
			<ul class="item-info">
				<?php echo implode("\n\t\t\t\t", $forum_page['item_body']['info'])."\n" ?>
			</ul>
		</div>
<?php

	}

?>
	</div>
	<div class="main-foot">
<?php

	if (!empty($forum_page['main_foot_options']))
		echo "\n\t\t\t".'<p class="options">'.implode(' ', $forum_page['main_foot_options']).'</p>';

?>
		<h2 class="hn"><span><?php echo $forum_page['items_info'] ?></span></h2>
	</div>
<?php

}
// Else there are no topics in this forum
else
{
	$forum_page['item_body']['subject']['title'] = '<h3 class="hn">'.$lang_forum['No topics'].'</h3>';
	$forum_page['item_body']['subject']['desc'] = '<p>'.$lang_forum['First topic nag'].'</p>';

	($hook = get_hook('vf_no_results_row_pre_display')) ? eval($hook) : null;

?>
	<div class="main-head">
		<h2 class="hn"><span><?php echo $lang_forum['Empty forum'] ?></span></h2>
	</div>
	<div id="forum<?php echo $id ?>" class="main-content main-forum">
		<div class="main-item empty main-first-item">
			<span class="icon empty"><!-- --></span>
			<div class="item-subject">
				<?php echo implode("\n\t\t\t\t", $forum_page['item_body']['subject'])."\n" ?>
			</div>
		</div>
	</div>
	<div class="main-foot">
		<h2 class="hn"><span><?php echo $lang_forum['Empty forum'] ?></span></h2>
	</div>
<?php

}

($hook = get_hook('vf_end')) ? eval($hook) : null;

$tpl_temp = forum_trim(ob_get_contents());
$tpl_main = str_replace('<!-- forum_main -->', $tpl_temp, $tpl_main);
ob_end_clean();
// END SUBST - <!-- forum_main -->

$forum_id = $id;

require FORUM_ROOT.'footer.php';
