<?php
/**
 * External syndication script
 *
 * Allows forum content to be syndicated outside of the site in various formats
 * (ie: RSS, Atom, XML, HTML).
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */
namespace punbb;

// TODO separate to different files
// /feed/atom/index.php
// /feed/rss/index.php
// ...

/***********************************************************************

	INSTRUCTIONS

	This script is used to include information about your board from
	pages outside the forums and to syndicate news about recent
	discussions via RSS/Atom/XML. The script can display a list of
	recent discussions, a list of active users or a collection of
	general board statistics. The script can be called directly via
	an URL, from a PHP include command or through the use of Server
	Side Includes (SSI).

	The scripts behaviour is controlled via variables supplied in the
	URL to the script. The different variables are: action (what to
	do), show (how many items to display), fid (the ID or ID's of
	the forum(s) to poll for topics), nfid (the ID or ID's of forums
	that should be excluded), tid (the ID of the topic from which to
	display posts) and type (output as HTML or RSS). The only
	mandatory variable is action. Possible/default values are:

		action: feed - show most recent topics/posts (HTML or RSS)
				online - show users online (HTML)
				online_full - as above, but includes a full list (HTML)
				stats - show board statistics (HTML)

		type:   rss - output as RSS 2.0
				atom - output as Atom 1.0
				xml - output as XML
				html - output as HTML (<li>'s)

		fid:    One or more forum ID's (comma-separated). If ignored,
				topics from all readable forums will be pulled.

		nfid:   One or more forum ID's (comma-separated) that are to be
				excluded. E.g. the ID of a a test forum.

		tid:    A topic ID from which to show posts. If a tid is supplied,
				fid and nfid are ignored.

		show:   Any integer value between 1 and 50. The default is 15.

		sort:	posted - sort topics by posted time (default)
				last_post - sort topics by last post

/***********************************************************************/

define('FORUM_QUIET_VISIT', 1);

require __DIR__ . '/vendor/autoload.php';

($hook = get_hook('ex_start')) ? eval($hook) : null;

// The length at which topic subjects will be truncated (for HTML output)
if (!defined('FORUM_EXTERN_MAX_SUBJECT_LENGTH'))
	define('FORUM_EXTERN_MAX_SUBJECT_LENGTH', 30);

// If we're a guest and we've sent a username/pass, we can try to authenticate using those details
if (user()->is_guest && isset($_SERVER['PHP_AUTH_USER'])) {
	authenticate_user($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
}

if (user()->g_read_board == '0') {
	http_authenticate_user();
	exit(__('No view'));
}

$action = isset($_GET['action']) ? $_GET['action'] : 'feed';
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'posted';

//
// Sends the proper headers for Basic HTTP Authentication
//
function http_authenticate_user() {
	if (!user()->is_guest) {
		return;
	}
	header('WWW-Authenticate: Basic realm="' .
		config()->o_board_title . ' External Syndication"');
	header('HTTP/1.0 401 Unauthorized');
}

//
// Output $feed as RSS 2.0
//
function output_rss($feed)
{
	// Send XML/no cache headers
	header('Content-Type: text/xml; charset=utf-8');
	header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');

	echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
	echo '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">'."\n";
	echo "\t".'<channel>'."\n";
	echo "\t\t".'<title><![CDATA['.escape_cdata($feed['title']).']]></title>'."\n";
	echo "\t\t".'<link>'.$feed['link'].'</link>'."\n";
	echo "\t\t".'<atom:link href="'.forum_htmlencode(get_current_url()).'" rel="self" type="application/rss+xml" />'."\n";
	echo "\t\t".'<description><![CDATA['.escape_cdata($feed['description']).']]></description>'."\n";
	echo "\t\t".'<lastBuildDate>'.gmdate('r', count($feed['items']) ? $feed['items'][0]['pubdate'] : time()).'</lastBuildDate>'."\n";

	if (config()->o_show_version == '1') {
		echo "\t\t".'<generator>PunBB ' . config()->o_cur_version . '</generator>'."\n";
	}
	else {
		echo "\t\t".'<generator>PunBB</generator>'."\n";
	}

	($hook = get_hook('ex_add_new_rss_info')) ? eval($hook) : null;

	foreach ($feed['items'] as $item)
	{
		echo "\t\t".'<item>'."\n";
		echo "\t\t\t".'<title><![CDATA['.escape_cdata($item['title']).']]></title>'."\n";
		echo "\t\t\t".'<link>'.$item['link'].'</link>'."\n";
		echo "\t\t\t".'<description><![CDATA['.escape_cdata($item['description']).']]></description>'."\n";
		echo "\t\t\t".'<author><![CDATA['.(isset($item['author']['email']) ? escape_cdata($item['author']['email']) : 'null@example.com').' ('.escape_cdata($item['author']['name']).')]]></author>'."\n";
		echo "\t\t\t".'<pubDate>'.gmdate('r', $item['pubdate']).'</pubDate>'."\n";
		echo "\t\t\t".'<guid>'.$item['link'].'</guid>'."\n";

		($hook = get_hook('ex_add_new_rss_item_info')) ? eval($hook) : null;

		echo "\t\t".'</item>'."\n";
	}

	echo "\t".'</channel>'."\n";
	echo '</rss>'."\n";
}


//
// Output $feed as Atom 1.0
//
function output_atom($feed)
{
	// Send XML/no cache headers
	header('Content-Type: text/xml; charset=utf-8');
	header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');

	echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
	echo '<feed xmlns="http://www.w3.org/2005/Atom">'."\n";

	echo "\t".'<title type="html"><![CDATA['.escape_cdata($feed['title']).']]></title>'."\n";
	echo "\t".'<link rel="self" href="'.forum_htmlencode(get_current_url()).'" />'."\n";
	echo "\t".'<updated>'.gmdate('Y-m-d\TH:i:s\Z', count($feed['items']) ? $feed['items'][0]['pubdate'] : time()).'</updated>'."\n";

	if (config()->o_show_version == '1') {
		echo "\t".'<generator version="' . config()->o_cur_version . '">PunBB</generator>'."\n";
	}
	else {
		echo "\t".'<generator>PunBB</generator>'."\n";
	}

	($hook = get_hook('ex_add_new_atom_info')) ? eval($hook) : null;

	echo "\t".'<id>'.$feed['link'].'</id>'."\n";

	$content_tag = ($feed['type'] == 'posts') ? 'content' : 'summary';

	foreach ($feed['items'] as $item)
	{
		echo "\t\t".'<entry>'."\n";
		echo "\t\t\t".'<title type="html"><![CDATA['.escape_cdata($item['title']).']]></title>'."\n";
		echo "\t\t\t".'<link rel="alternate" href="'.$item['link'].'" />'."\n";
		echo "\t\t\t".'<'.$content_tag.' type="html"><![CDATA['.escape_cdata($item['description']).']]></'.$content_tag.'>'."\n";
		echo "\t\t\t".'<author>'."\n";
		echo "\t\t\t\t".'<name><![CDATA['.escape_cdata($item['author']['name']).']]></name>'."\n";

		if (isset($item['author']['email']))
			echo "\t\t\t\t".'<email><![CDATA['.escape_cdata($item['author']['email']).']]></email>'."\n";

		if (isset($item['author']['uri']))
			echo "\t\t\t\t".'<uri>'.$item['author']['uri'].'</uri>'."\n";

		echo "\t\t\t".'</author>'."\n";
		echo "\t\t\t".'<updated>'.gmdate('Y-m-d\TH:i:s\Z', $item['pubdate']).'</updated>'."\n";

		($hook = get_hook('ex_add_new_atom_item_info')) ? eval($hook) : null;

		echo "\t\t\t".'<id>'.$item['link'].'</id>'."\n";
		echo "\t\t".'</entry>'."\n";
	}

	echo '</feed>'."\n";
}


//
// Output $feed as XML
//
function output_xml($feed)
{
	// Send XML/no cache headers
	header('Content-Type: application/xml; charset=utf-8');
	header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');

	echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
	echo '<source>'."\n";
	echo "\t".'<url>'.$feed['link'].'</url>'."\n";

	($hook = get_hook('ex_add_new_xml_info')) ? eval($hook) : null;

	$forum_tag = ($feed['type'] == 'posts') ? 'post' : 'topic';

	foreach ($feed['items'] as $item)
	{
		echo "\t".'<'.$forum_tag.' id="'.$item['id'].'">'."\n";

		echo "\t\t".'<title><![CDATA['.escape_cdata($item['title']).']]></title>'."\n";
		echo "\t\t".'<link>'.$item['link'].'</link>'."\n";
		echo "\t\t".'<content><![CDATA['.escape_cdata($item['description']).']]></content>'."\n";
		echo "\t\t".'<author>'."\n";
		echo "\t\t\t".'<name><![CDATA['.escape_cdata($item['author']['name']).']]></name>'."\n";

		if (isset($item['author']['email']))
			echo "\t\t\t".'<email><![CDATA['.escape_cdata($item['author']['email']).']]></email>'."\n";

		if (isset($item['author']['uri']))
			echo "\t\t\t".'<uri>'.$item['author']['uri'].'</uri>'."\n";

		echo "\t\t".'</author>'."\n";
		echo "\t\t".'<posted>'.gmdate('r', $item['pubdate']).'</posted>'."\n";

		($hook = get_hook('ex_add_new_xml_item_info')) ? eval($hook) : null;

		echo "\t".'</'.$forum_tag.'>'."\n";
	}

	echo '</source>'."\n";
}


//
// Output $feed as HTML (using <li> tags)
//
function output_html($feed)
{

	// Send the Content-type header in case the web server is setup to send something else
	header('Content-type: text/html; charset=utf-8');
	header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');

	foreach ($feed['items'] as $item)
	{
		if (utf8_strlen($item['title']) > FORUM_EXTERN_MAX_SUBJECT_LENGTH)
			$subject_truncated = forum_htmlencode(forum_trim(utf8_substr($item['title'], 0, (FORUM_EXTERN_MAX_SUBJECT_LENGTH - 5)))).'…';
		else
			$subject_truncated = forum_htmlencode($item['title']);

		echo '<li><a href="'.$item['link'].'" title="'.forum_htmlencode($item['title']).'">'.$subject_truncated.'</a></li>'."\n";
	}
}

// Show recent discussions
if ($action == 'feed')
{
	// Determine what type of feed to output
	$type = isset($_GET['type']) && in_array($_GET['type'], array('html', 'rss', 'atom', 'xml')) ? $_GET['type'] : 'html';

	$show = isset($_GET['show']) ? intval($_GET['show']) : 15;
	if ($show < 1 || $show > 50)
		$show = 15;

	($hook = get_hook('ex_set_syndication_type')) ? eval($hook) : null;

	// Was a topic ID supplied?
	if (isset($_GET['tid']))
	{
		$tid = intval($_GET['tid']);

		// Fetch topic subject
		$query = array(
			'SELECT'	=> 't.subject, t.first_post_id',
			'FROM'		=> 'topics AS t',
			'JOINS'		=> array(
				array(
					'LEFT JOIN'		=> 'forum_perms AS fp',
					'ON'			=> '(fp.forum_id=t.forum_id AND fp.group_id='.user()->g_id.')'
				)
			),
			'WHERE'		=> '(fp.read_forum IS NULL OR fp.read_forum=1) AND t.moved_to IS NULL and t.id='.$tid
		);

		($hook = get_hook('ex_qr_get_topic_data')) ? eval($hook) : null;
		$result = db()->query_build($query) or error(__FILE__, __LINE__);

		$cur_topic = db()->fetch_assoc($result);
		if (!$cur_topic)
		{
			http_authenticate_user();
			exit(__('Bad request'));
		}

		if (!defined('FORUM_PARSER_LOADED'))
			require FORUM_ROOT.'include/parser.php';

		if (config()->o_censoring == '1') {
			$cur_topic['subject'] = censor_words($cur_topic['subject']);
		}

		// Setup the feed
		$feed = array(
			'title'		=>	config()->o_board_title .
				__('Title separator') . $cur_topic['subject'],
			'link'			=>	forum_link($forum_url['topic'], array($tid, sef_friendly($cur_topic['subject']))),
			'description'	=>	sprintf(__('RSS description topic'), $cur_topic['subject']),
			'items'			=>	array(),
			'type'			=>	'posts'
		);

		// Fetch $show posts
		$query = array(
			'SELECT'	=> 'p.id, p.poster, p.message, p.hide_smilies, p.posted, p.poster_id, u.email_setting, u.email, p.poster_email',
			'FROM'		=> 'posts AS p',
			'JOINS'		=> array(
				array(
					'INNER JOIN'	=> 'users AS u',
					'ON'			=> 'u.id = p.poster_id'
				)
			),
			'WHERE'		=> 'p.topic_id='.$tid,
			'ORDER BY'	=> 'p.posted DESC',
			'LIMIT'		=> $show
		);
		($hook = get_hook('ex_qr_get_posts')) ? eval($hook) : null;
		$result = db()->query_build($query) or error(__FILE__, __LINE__);

		while ($cur_post = db()->fetch_assoc($result))
		{
			if (config()->o_censoring == '1')
				$cur_post['message'] = censor_words($cur_post['message']);

			$cur_post['message'] = parse_message($cur_post['message'], $cur_post['hide_smilies']);

			$item = array(
				'id'			=>	$cur_post['id'],
				'title'			=>	$cur_topic['first_post_id'] == $cur_post['id'] ?
					$cur_topic['subject'] : __('RSS reply') . $cur_topic['subject'],
				'link'			=>	forum_link($forum_url['post'], $cur_post['id']),
				'description'	=>	$cur_post['message'],
				'author'		=>	array(
					'name'	=> $cur_post['poster'],
				),
				'pubdate'		=>	$cur_post['posted']
			);

			if ($cur_post['poster_id'] > 1) {
				if ($cur_post['email_setting'] == '0' && !user()->is_guest) {
					$item['author']['email'] = $cur_post['email'];
				}

				$item['author']['uri'] = forum_link($forum_url['user'], $cur_post['poster_id']);
			}
			else if ($cur_post['poster_email'] != '' && !user()->is_guest) {
				$item['author']['email'] = $cur_post['poster_email'];
			}

			$feed['items'][] = $item;

			($hook = get_hook('ex_modify_cur_post_item')) ? eval($hook) : null;
		}

		($hook = get_hook('ex_pre_topic_output')) ? eval($hook) : null;

		$output_func = 'punbb\\output_'.$type;
		$output_func($feed);
	}
	else
	{
		$forum_name = '';

		if (!defined('FORUM_PARSER_LOADED'))
			require FORUM_ROOT.'include/parser.php';

		// Were any forum ID's supplied?
		if (isset($_GET['fid']) && is_scalar($_GET['fid']) && $_GET['fid'] != '')
		{
			$fids = explode(',', forum_trim($_GET['fid']));
			$fids = array_map('intval', $fids);

			if (!empty($fids))
				$forum_sql = ' AND t.forum_id IN('.implode(',', $fids).')';

			if (count($fids) == 1)
			{
				// Fetch forum name
				$query = array(
					'SELECT'	=> 'f.forum_name',
					'FROM'		=> 'forums AS f',
					'JOINS'		=> array(
						array(
							'LEFT JOIN'		=> 'forum_perms AS fp',
							'ON'			=> '(fp.forum_id=f.id AND fp.group_id='.user()->g_id.')'
						)
					),
					'WHERE'		=> '(fp.read_forum IS NULL OR fp.read_forum=1) AND f.id='.$fids[0]
				);

				$result = db()->query_build($query) or error(__FILE__, __LINE__);
				$forum_name_in_db = db()->result($result);
				if (!is_null($forum_name_in_db) && $forum_name_in_db !== false)
					$forum_name = __('Title separator') . $forum_name_in_db;
			}
		}

		// Any forum ID's to exclude?
		if (isset($_GET['nfid']) && is_scalar($_GET['nfid']) && $_GET['nfid'] != '')
		{
			$nfids = explode(',', forum_trim($_GET['nfid']));
			$nfids = array_map('intval', $nfids);

			if (!empty($nfids))
				$forum_sql = ' AND t.forum_id NOT IN('.implode(',', $nfids).')';
		}

		// Setup the feed
		$feed = array(
			'title'			=>	config()->o_board_title . $forum_name,
			'link'			=>	forum_link($forum_url['index']),
			'description'	=>	sprintf(__('RSS description'), config()->o_board_title),
			'items'			=>	array(),
			'type'			=>	'topics'
		);

		// Fetch $show topics
		$query = array(
			'SELECT'	=> 't.id, t.poster, t.posted, t.subject, p.message, p.hide_smilies, u.email_setting, u.email, p.poster_id, p.poster_email',
			'FROM'		=> 'topics AS t',
			'JOINS'		=> array(
				array(
					'INNER JOIN'	=> 'posts AS p',
					'ON'			=> 'p.id = t.first_post_id'
				),
				array(
					'INNER JOIN'	=> 'users AS u',
					'ON'			=> 'u.id = p.poster_id'
				),
				array(
					'LEFT JOIN'		=> 'forum_perms AS fp',
					'ON'			=> '(fp.forum_id = t.forum_id AND fp.group_id = '.user()->g_id.')'
				)
			),
			'WHERE'		=> '(fp.read_forum IS NULL OR fp.read_forum = 1) AND t.moved_to IS NULL',
			'ORDER BY'	=> (($sort_by == 'last_post') ? 't.last_post' : 't.posted').' DESC',
			'LIMIT'		=> $show
		);

		if (isset($forum_sql))
			$query['WHERE'] .= $forum_sql;

		($hook = get_hook('ex_qr_get_topics')) ? eval($hook) : null;
		$result = db()->query_build($query) or error(__FILE__, __LINE__);
		while ($cur_topic = db()->fetch_assoc($result))
		{
			if (config()->o_censoring == '1') {
				$cur_topic['subject'] = censor_words($cur_topic['subject']);
				$cur_topic['message'] = censor_words($cur_topic['message']);
			}

			$cur_topic['message'] = parse_message($cur_topic['message'], $cur_topic['hide_smilies']);

			$item = array(
				'id'			=>	$cur_topic['id'],
				'title'			=>	$cur_topic['subject'],
				'link'			=>	forum_link($forum_url['topic_new_posts'], array($cur_topic['id'], sef_friendly($cur_topic['subject']))),
				'description'	=>	$cur_topic['message'],
				'author'		=>	array(
					'name'			=> $cur_topic['poster']
				),
				'pubdate'		=>	$cur_topic['posted']
			);

			if ($cur_topic['poster_id'] > 1)
			{
				if ($cur_topic['email_setting'] == '0' && !user()->is_guest) {
					$item['author']['email'] = $cur_topic['email'];
				}

				$item['author']['uri'] = forum_link($forum_url['user'], $cur_topic['poster_id']);
			}
			else if ($cur_topic['poster_email'] != '' && !user()->is_guest) {
				$item['author']['email'] = $cur_topic['poster_email'];
			}

			$feed['items'][] = $item;

			($hook = get_hook('ex_modify_cur_topic_item')) ? eval($hook) : null;
		}

		($hook = get_hook('ex_pre_forum_output')) ? eval($hook) : null;

		$output_func = 'punbb\\output_'.$type;
		$output_func($feed);
	}

	exit;
}

// Show users online
else if ($action == 'online' || $action == 'online_full')
{
	// Fetch users online info and generate strings for output
	$num_guests = $num_users = 0;
	$users = array();

	$query = array(
		'SELECT'	=> 'o.user_id, o.ident',
		'FROM'		=> 'online AS o',
		'WHERE'		=> 'o.idle=0',
		'ORDER BY'	=> 'o.ident'
	);

	($hook = get_hook('ex_qr_get_users_online')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);
	while ($forum_user_online = db()->fetch_assoc($result))
	{
		if ($forum_user_online['user_id'] > 1)
		{
			$users[] = user()->g_view_users == '1'?
				'<a href="'.forum_link($forum_url['user'], $forum_user_online['user_id']).'">'.forum_htmlencode($forum_user_online['ident']).'</a>' : forum_htmlencode($forum_user_online['ident']);
			++$num_users;
		}
		else
			++$num_guests;
	}

	($hook = get_hook('ex_pre_online_output')) ? eval($hook) : null;
	// Send the Content-type header in case the web server is setup to send something else
	header('Content-type: text/html; charset=utf-8');
	header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');


	echo __('Guests online', 'index') . ': ' . forum_number_format($num_guests) . '<br />' . "\n";

	if ($_GET['action'] == 'online_full' && !empty($users))
		echo __('Users online', 'index') . ': ' .
			implode(__('Online list separator', 'index'), $users).'<br />'."\n";
	else
		echo __('Users online', 'index') . ': ' . forum_number_format($num_users).'<br />'."\n";

	exit;
}

// Show board statistics
else if ($action == 'stats')
{
	// Collect some statistics from the database
	$query = array(
		'SELECT'	=> 'COUNT(u.id) - 1',
		'FROM'		=> 'users AS u',
		'WHERE'		=> 'u.group_id != '.FORUM_UNVERIFIED
	);

	($hook = get_hook('ex_qr_get_user_count')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);
	$stats['total_users'] = db()->result($result);

	$query = array(
		'SELECT'	=> 'u.id, u.username',
		'FROM'		=> 'users AS u',
		'WHERE'		=> 'u.group_id != '.FORUM_UNVERIFIED,
		'ORDER BY'	=> 'u.registered DESC',
		'LIMIT'		=> '1'
	);

	($hook = get_hook('ex_qr_get_newest_user')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);
	$stats['last_user'] = db()->fetch_assoc($result);

	$query = array(
		'SELECT'	=> 'SUM(f.num_topics), SUM(f.num_posts)',
		'FROM'		=> 'forums AS f'
	);

	($hook = get_hook('ex_qr_get_post_stats')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);
	list($stats['total_topics'], $stats['total_posts']) = db()->fetch_row($result);

	// Send the Content-type header in case the web server is setup to send something else
	header('Content-type: text/html; charset=utf-8');
	header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');

	($hook = get_hook('ex_pre_stats_output')) ? eval($hook) : null;

	echo sprintf(__('No of users', 'index'), forum_number_format($stats['total_users'])).'<br />'."\n";
	echo sprintf(__('Newest user', 'index'), '<a href="'.forum_link($forum_url['user'], $stats['last_user']['id']).'">'.forum_htmlencode($stats['last_user']['username']).'</a>').'<br />'."\n";
	echo sprintf(__('No of topics', 'index'), forum_number_format($stats['total_topics'])).'<br />'."\n";
	echo sprintf(__('No of posts', 'index'), forum_number_format($stats['total_posts'])).'<br />'."\n";

	exit;
}


($hook = get_hook('ex_new_action')) ? eval($hook) : null;

// If we end up here, the script was called with some wacky parameters
exit(__('Bad request'));
