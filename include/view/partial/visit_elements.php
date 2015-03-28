<?php

if ($forum_user['is_guest'])
	$view_forum_welcome =
		$visit_elements['<!-- forum_welcome -->'] = '<p id="welcome"><span>'.$lang_common['Not logged in'].'</span> <span>'.$lang_common['Login nag'].'</span></p>';
else
	$view_forum_welcome =
		$visit_elements['<!-- forum_welcome -->'] = '<p id="welcome"><span>'.sprintf($lang_common['Logged in as'], '<strong>'.forum_htmlencode($forum_user['username']).'</strong>').'</span></p>';

if ($forum_user['g_read_board'] == '1' && $forum_user['g_search'] == '1')
{
	$visit_links = array();

	if (!$forum_user['is_guest'])
		$visit_links['newposts'] = '<span id="visit-new"'.(empty($visit_links) ? ' class="first-item"' : '').'><a href="'.forum_link($forum_url['search_new']).'" title="'.$lang_common['New posts title'].'">'.$lang_common['New posts'].'</a></span>';

	$visit_links['recent'] = '<span id="visit-recent"'.(empty($visit_links) ? ' class="first-item"' : '').'><a href="'.forum_link($forum_url['search_recent']).'" title="'.$lang_common['Active topics title'].'">'.$lang_common['Active topics'].'</a></span>';
	$visit_links['unanswered'] = '<span id="visit-unanswered"'.(empty($visit_links) ? ' class="first-item"' : '').'><a href="'.forum_link($forum_url['search_unanswered']).'" title="'.$lang_common['Unanswered topics title'].'">'.$lang_common['Unanswered topics'].'</a></span>';
}

($hook = get_hook('hd_visit_elements')) ? eval($hook) : null;

$view_forum_visit =
	$visit_elements['<!-- forum_visit -->'] = (!empty($visit_links)) ? '<p id="visit-links" class="options">'.implode(' ', $visit_links).'</p>' : '';
