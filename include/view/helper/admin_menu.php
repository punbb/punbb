<?php

global $forum_page;

if (substr(FORUM_PAGE, 0, 5) == 'admin' && FORUM_PAGE_TYPE != 'paged')
{

$forum_page['admin_sub'] = generate_admin_menu(true);

return '<div class="admin-menu gen-content">'."\n\t".'<ul>'."\n\t\t".
	generate_admin_menu(false)."\n\t".'</ul>'."\n".'</div>' .

	(($forum_page['admin_sub'] != '') ?
	  '<div class="admin-submenu gen-content">'."\n\t".
	  '<ul>'."\n\t\t".$forum_page['admin_sub']."\n\t".'</ul>'."\n".'</div>' : '');
}


