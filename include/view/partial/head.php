<?php

// Is this a page that we want search index spiders to index?
if (!defined('FORUM_ALLOW_INDEX'))
	$forum_head['robots'] = '<meta name="ROBOTS" content="NOINDEX, FOLLOW" />';
else
	$forum_head['descriptions'] = '<meta name="description" content="'.generate_crumbs(true).$lang_common['Title separator'].forum_htmlencode($forum_config['o_board_desc']).'" />';

// Should we output a MicroID? http://microid.org/
if (strpos(FORUM_PAGE, 'profile') === 0)
	$forum_head['microid'] = '<meta name="microid" content="mailto+http:sha1:'.sha1(sha1('mailto:'.$user['email']).sha1(forum_link($forum_url['user'], $id))).'" />';

$forum_head['title'] = '<title>'.generate_crumbs(true).'</title>';

// Should we output feed links?
if (FORUM_PAGE == 'index')
{
	$forum_head['rss'] = '<link rel="alternate" type="application/rss+xml" href="'.forum_link($forum_url['index_rss']).'" title="RSS" />';
	$forum_head['atom'] = '<link rel="alternate" type="application/atom+xml" href="'.forum_link($forum_url['index_atom']).'" title="ATOM" />';
}
else if (FORUM_PAGE == 'viewforum')
{
	$forum_head['rss'] = '<link rel="alternate" type="application/rss+xml" href="'.forum_link($forum_url['forum_rss'], $id).'" title="RSS" />';
	$forum_head['atom'] = '<link rel="alternate" type="application/atom+xml" href="'.forum_link($forum_url['forum_atom'], $id).'" title="ATOM" />';
}
else if (FORUM_PAGE == 'viewtopic')
{
	$forum_head['rss'] = '<link rel="alternate" type="application/rss+xml" href="'.forum_link($forum_url['topic_rss'], $id).'" title="RSS" />';
	$forum_head['atom'] = '<link rel="alternate" type="application/atom+xml" href="'.forum_link($forum_url['topic_atom'], $id).'" title="ATOM" />';
}

// If there are other page navigation links (first, next, prev and last)
if (!empty($forum_page['nav']))
	$forum_head['nav'] = implode("\n", $forum_page['nav']);

if ($forum_user['g_read_board'] == '1' && $forum_user['g_search'] == '1')
{
	$forum_head['search'] = '<link rel="search" type="text/html" href="'.forum_link($forum_url['search']).'" title="'.$lang_common['Search'].'" />';
	$forum_head['opensearch'] = '<link rel="search" type="application/opensearchdescription+xml" href="'.forum_link($forum_url['opensearch']).'" title="'.forum_htmlencode($forum_config['o_board_title']).'" />';
}

$forum_head['author'] = '<link rel="author" type="text/html" href="'.forum_link($forum_url['users']).'" title="'.$lang_common['User list'].'" />';

ob_start();

// Include stylesheets
require FORUM_ROOT.'style/'.$forum_user['style'].'/'.$forum_user['style'].'.php';

$head_temp = forum_trim(ob_get_contents());
$num_temp = 0;

foreach (explode("\n", $head_temp) as $style_temp)
	$forum_head['style'.$num_temp++] = $style_temp;

ob_end_clean();

($hook = get_hook('hd_head')) ? eval($hook) : null;

// Render CSS from forum_loader
echo implode("\n", $forum_head).$forum_loader->render_css();
