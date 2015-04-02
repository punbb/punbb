<?php
global $forum_config;

if ($forum_config['o_board_desc'] != '') { ?>
	<p id="brd-desc">
		<?= forum_htmlencode($forum_config['o_board_desc']) ?>
	</p>
<?php } ?>
