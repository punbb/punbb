<?php
namespace punbb;

($hook = get_hook('fn_message_output_start')) ? eval($hook) : null;

?>
<div class="main-head">
	<?php if (!empty($forum_page['main_head_options'])) { ?>
		<p class="options">
			<?php foreach ($forum_page['main_head_options'] as $v) { ?>
				<?= $v ?>
			<?php } ?>
		</p>
	<?php	} ?>
	<h2 class="hn"><span><?= $heading ?></span></h2>
</div>

<div class="main-content main-message">
	<p>
		<?= $message ?>
		<?php if ($link != '') { ?>
			<span><?= $link ?></span>
		<?php } ?>
	</p>
</div>
<?php

($hook = get_hook('fn_message_output_end')) ? eval($hook) : null;
