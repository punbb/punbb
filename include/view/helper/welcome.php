<?php

global $forum_user, $lang_common;

return $forum_user['is_guest']?
	('<p id="welcome"><span>'.$lang_common['Not logged in'].'</span> <span>'.
		$lang_common['Login nag'].'</span></p>') :
	('<p id="welcome"><span>'.sprintf($lang_common['Logged in as'], '<strong>'.
		forum_htmlencode($forum_user['username']).'</strong>').'</span></p>');
