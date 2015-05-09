<?php
namespace punbb;
?>

<div id="brd-stats" class="gen-content">
	<h2 class="hn"><span><?= __('Statistics', 'index') ?></span></h2>
	<ul>
		<?php foreach ($stats_list as $v) { ?>
			<?= $v ?>
		<?php } ?>
	</ul>
</div>