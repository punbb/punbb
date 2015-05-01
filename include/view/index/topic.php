<?php
namespace punbb;
?>

<div id="forum<?php echo $cur_forum['fid'] ?>" class="main-item<?= $item_style ?>">
	<span class="icon <?php echo implode(' ', $item_status) ?>"></span>
	<div class="item-subject">
		<?php echo implode("\n\t\t\t\t", $item_body['subject'])."\n" ?>
	</div>
	<ul class="item-info">
		<?php echo implode("\n\t\t\t\t", $item_body['info'])."\n" ?>
	</ul>
</div>