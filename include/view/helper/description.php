<?php
namespace punbb;

if (config()->o_board_desc != '') { ?>
	<p id="brd-desc">
		<?= forum_htmlencode(config()->o_board_desc) ?>
	</p>
<?php } ?>
