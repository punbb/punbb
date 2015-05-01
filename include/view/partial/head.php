<?php
namespace punbb;

// globals:
// $forum_url
// $forum_page

$config = config();
$user = user();
$assets = assets();
$fstyles = theme()->path[$user->style] . '/' . $user->style . '.php';

if (FORUM_PAGE == 'redirect') {
	$head['refresh'] = '<meta http-equiv="refresh" content="'.
		$config->o_redirect_delay . ';URL=' .
		str_replace(array('<', '>', '"'), array('&lt;', '&gt;', '&quot;'), $destination_url) . '" />';
	$head['title'] = '<title>' . __('Redirecting') .
		__('Title separator') . forum_htmlencode($config->o_board_title) . '</title>';

	// Include stylesheets
	require $fstyles;

	$head_temp = forum_trim(ob_get_contents());
	$num_temp = 0;
	foreach (explode("\n", $head_temp) as $style_temp) {
		$head['style' . $num_temp++] = $style_temp;
	}

	($hook = get_hook('fn_redirect_head')) ? eval($hook) : null;
	echo implode("\n", $head) . $assets->render_css();
}
else if (FORUM_PAGE == 'maintenance') {
	require $fstyles;
	echo $assets->render_css();
}
else {
	// Is this a page that we want search index spiders to index?
	if (!defined('FORUM_ALLOW_INDEX')) {
		$head['robots'] = '<meta name="ROBOTS" content="NOINDEX, FOLLOW" />';
	}
	else {
		$head['descriptions'] = '<meta name="description" content="' .
			generate_crumbs(true). __('Title separator') .
			forum_htmlencode($config->o_board_desc) . '" />';
	}

	// Should we output a MicroID? http://microid.org/
	if (strpos(FORUM_PAGE, 'profile') === 0) {
		$head['microid'] = '<meta name="microid" content="mailto+http:sha1:' .
			sha1(sha1('mailto:' . $user->email) .
			sha1(link('user', $id))) . '" />';
	}

	$head['title'] = '<title>' . generate_crumbs(true) . '</title>';

	// Should we output feed links?
	if (FORUM_PAGE == 'index') 	{
		$head['rss'] = '<link rel="alternate" type="application/rss+xml" href="' .
			link('index_rss').'" title="RSS" />';
		$head['atom'] = '<link rel="alternate" type="application/atom+xml" href="' .
			link('index_atom').'" title="ATOM" />';
	}
	else if (FORUM_PAGE == 'viewforum') {
		$head['rss'] = '<link rel="alternate" type="application/rss+xml" href="' .
			link('forum_rss', $id) . '" title="RSS" />';
		$head['atom'] = '<link rel="alternate" type="application/atom+xml" href="' .
			link('forum_atom', $id) . '" title="ATOM" />';
	}
	else if (FORUM_PAGE == 'viewtopic') {
		$head['rss'] = '<link rel="alternate" type="application/rss+xml" href="' .
			link('topic_rss', $id) . '" title="RSS" />';
		$head['atom'] = '<link rel="alternate" type="application/atom+xml" href="' .
			link('topic_atom', $id) . '" title="ATOM" />';
	}

	// If there are other page navigation links (first, next, prev and last)
	if (!empty($forum_page['nav'])) {
		$head['nav'] = implode("\n", $forum_page['nav']);
	}

	if ($user->g_read_board == '1' && $user->g_search == '1') {
		$head['search'] = '<link rel="search" type="text/html" href="' .
			link('search') . '" title="' . __('Search') . '" />';
		$head['opensearch'] =
			'<link rel="search" type="application/opensearchdescription+xml" href="' .
			link('opensearch') . '" title="' .
			forum_htmlencode($config->o_board_title) . '" />';
	}

	$head['author'] = '<link rel="author" type="text/html" href="' .
		link('users') . '" title="' . __('User list') . '" />';

	ob_start();
	// Include stylesheets
	require $fstyles;

	$head_temp = forum_trim(ob_get_contents());
	$num_temp = 0;
	foreach (explode("\n", $head_temp) as $style_temp) {
		$head['style' . $num_temp++] = $style_temp;
	}
	ob_end_clean();

	($hook = get_hook('hd_head')) ? eval($hook) : null;

	// Render CSS from forum_loader
	echo implode("\n", $head) . $assets->render_css();
}
