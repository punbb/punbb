<?php

global $forum_page;

return (!empty($forum_page['main_menu'])) ?
	'<div class="main-menu gen-content">'."\n\t".'<ul>'."\n\t\t".
	implode("\n\t\t", $forum_page['main_menu'])."\n\t".'</ul>'."\n".'</div>' : '';
