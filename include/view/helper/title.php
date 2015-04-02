<?php
global $forum_url, $forum_config;
?>

<p id="brd-title">
	<a href="<?= forum_link($forum_url['index']) ?>"><?=
		forum_htmlencode($forum_config['o_board_title']) ?></a>
</p>
