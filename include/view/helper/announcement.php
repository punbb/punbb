<?php
namespace punbb;

if (config()->o_announcement == '1' && user()['g_read_board'] == '1') { ?>
	<div id="brd-announcement" class="gen-content">
		<?php if (config()->o_announcement_heading != '') { ?>
			<h1 class="hn"><span><?= config()->o_announcement_heading ?></span></h1>
		<?php } ?>
		<div class="content">
			<?= config()->o_announcement_message ?>
		</div>
	</div>
<?php } ?>
