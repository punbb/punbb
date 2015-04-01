<?php

global $forum_page;

return '<h1 class="main-title">'.
	((isset($forum_page['main_title'])) ?
		$forum_page['main_title'] :
		forum_htmlencode(is_array($last_crumb = end($forum_page['crumbs'])) ?
			reset($last_crumb) : $last_crumb)).(isset($forum_page['main_head_pages']) ?
				' <small>'.$forum_page['main_head_pages'].'</small>' : '').'</h1>'."\n";