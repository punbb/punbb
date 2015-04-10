<?php
namespace punbb;

global $forum_url;
?>

<p id="brd-title">
	<a href="<?= forum_link($forum_url['index']) ?>"><?=
		forum_htmlencode(config()['o_board_title']) ?></a>
</p>
