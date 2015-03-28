<?php

// Top breadcrumbs
$view_forum_crumbs_top =
	$main_elements['<!-- forum_crumbs_top -->'] = (FORUM_PAGE != 'index') ? '<div id="brd-crumbs-top" class="crumbs">'."\n\t".'<p>'.generate_crumbs(false).'</p>'."\n".'</div>' : '';

// Bottom breadcrumbs
$view_forum_crumbs_end =
	$main_elements['<!-- forum_crumbs_end -->'] = (FORUM_PAGE != 'index') ? '<div id="brd-crumbs-end" class="crumbs">'."\n\t".'<p>'.generate_crumbs(false).'</p>'."\n".'</div>' : '';
// Main section heading
$view_forum_main_title =
	$main_elements['<!-- forum_main_title -->'] = '<h1 class="main-title">'.((isset($forum_page['main_title'])) ? $forum_page['main_title'] : forum_htmlencode(is_array($last_crumb = end($forum_page['crumbs'])) ? reset($last_crumb) : $last_crumb)).(isset($forum_page['main_head_pages']) ? ' <small>'.$forum_page['main_head_pages'].'</small>' : '').'</h1>'."\n";

// Top pagination and post links
$view_forum_main_pagepost_top =
	$main_elements['<!-- forum_main_pagepost_top -->'] = (!empty($forum_page['page_post'])) ? '<div id="brd-pagepost-top" class="main-pagepost gen-content">'."\n\t".implode("\n\t", $forum_page['page_post'])."\n".'</div>' : '';

// Bottom pagination and postlink
$view_forum_main_pagepost_end =
	$main_elements['<!-- forum_main_pagepost_end -->'] = (!empty($forum_page['page_post'])) ? '<div id="brd-pagepost-end" class="main-pagepost gen-content">'."\n\t".implode("\n\t", $forum_page['page_post'])."\n".'</div>' : '';

// Main section menu e.g. profile menu
$view_forum_main_menu =
	$main_elements['<!-- forum_main_menu -->'] = (!empty($forum_page['main_menu'])) ? '<div class="main-menu gen-content">'."\n\t".'<ul>'."\n\t\t".implode("\n\t\t", $forum_page['main_menu'])."\n\t".'</ul>'."\n".'</div>' : '';

// Main section menu e.g. profile menu
if (substr(FORUM_PAGE, 0, 5) == 'admin' && FORUM_PAGE_TYPE != 'paged')
{
	$view_forum_admin_menu =
		$main_elements['<!-- forum_admin_menu -->'] = '<div class="admin-menu gen-content">'."\n\t".'<ul>'."\n\t\t".generate_admin_menu(false)."\n\t".'</ul>'."\n".'</div>';

	$view_forum_admin_submenu =
		$forum_page['admin_sub'] = generate_admin_menu(true);
			$main_elements['<!-- forum_admin_submenu -->'] = ($forum_page['admin_sub'] != '') ? '<div class="admin-submenu gen-content">'."\n\t".'<ul>'."\n\t\t".$forum_page['admin_sub']."\n\t".'</ul>'."\n".'</div>' : '';
}

($hook = get_hook('hd_main_elements')) ? eval($hook) : null;
