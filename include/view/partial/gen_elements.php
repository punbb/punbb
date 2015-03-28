<?php

// Forum page id and classes
if (!defined('FORUM_PAGE_TYPE'))
{
	if (substr(FORUM_PAGE, 0, 5) == 'admin')
		define('FORUM_PAGE_TYPE', 'admin-page');
	else
	{
		if (!empty($forum_page['page_post']))
			define('FORUM_PAGE_TYPE', 'paged-page');
		else if (!empty($forum_page['main_menu']))
			define('FORUM_PAGE_TYPE', 'menu-page');
		else
			define('FORUM_PAGE_TYPE', 'basic-page');
	}
}

$view_forum_page =
	$gen_elements['<!-- forum_page -->'] = 'id="brd-'.FORUM_PAGE.'" class="brd-page '.FORUM_PAGE_TYPE.'"';

// Skip link
$view_forum_skip =
	$gen_elements['<!-- forum_skip -->'] = '<p id="brd-access"><a href="#brd-main">'.$lang_common['Skip to content'].'</a></p>';

// Forum Title
$view_forum_title =
	$gen_elements['<!-- forum_title -->'] = '<p id="brd-title"><a href="'.forum_link($forum_url['index']).'">'.forum_htmlencode($forum_config['o_board_title']).'</a></p>';

// Forum Description
$view_forum_desc =
	$gen_elements['<!-- forum_desc -->'] = ($forum_config['o_board_desc'] != '') ? '<p id="brd-desc">'.forum_htmlencode($forum_config['o_board_desc']).'</p>' : '';

// Main Navigation
$view_forum_navlinks =
	$gen_elements['<!-- forum_navlinks -->'] = '<ul>'."\n\t\t".generate_navlinks()."\n\t".'</ul>';

// Announcement
$view_forum_announcement =
	$gen_elements['<!-- forum_announcement -->'] = ($forum_config['o_announcement'] == '1' && $forum_user['g_read_board'] == '1') ? '<div id="brd-announcement" class="gen-content">'.($forum_config['o_announcement_heading'] != '' ? "\n\t".'<h1 class="hn"><span>'.$forum_config['o_announcement_heading'].'</span></h1>' : '')."\n\t".'<div class="content">'.$forum_config['o_announcement_message'].'</div>'."\n".'</div>'."\n" : '';

// Flash messages
$view_forum_messages =
	$gen_elements['<!-- forum_messages -->'] = '<div id="brd-messages" class="brd">'.$forum_flash->show(true).'</div>'."\n";

// Maintenance Warning
$view_forum_maint =
	$gen_elements['<!-- forum_maint -->'] = ($forum_user['g_id'] == FORUM_ADMIN && $forum_config['o_maintenance'] == '1') ? '<p id="maint-alert" class="warn">'.sprintf($lang_common['Maintenance warning'], '<a href="'.forum_link($forum_url['admin_settings_maintenance']).'">'.$lang_common['Maintenance mode'].'</a>').'</p>' : '';

($hook = get_hook('hd_gen_elements')) ? eval($hook) : null;
