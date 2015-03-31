<?php

global $forum_config, $forum_user;

return ($forum_config['o_announcement'] == '1' && $forum_user['g_read_board'] == '1') ?
	'<div id="brd-announcement" class="gen-content">'.
		($forum_config['o_announcement_heading'] != '' ?
			"\n\t".'<h1 class="hn"><span>'.
				$forum_config['o_announcement_heading'].
			'</span></h1>' : '')."\n\t".'<div class="content">'.
		$forum_config['o_announcement_message'].'</div>'."\n".'</div>'."\n" : '';
