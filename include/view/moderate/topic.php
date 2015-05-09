<?php
namespace punbb;
?>

<div id="topic<?php echo $cur_topic['id'] ?>" class="main-item<?php echo $forum_page['item_style'] ?>">
	<span class="icon <?php echo implode(' ', $forum_page['item_status']) ?>"><!-- --></span>
	<div class="item-subject">
		<?php echo implode("\n\t\t\t\t\t", $forum_page['item_body']['subject'])."\n" ?>
	</div>
	<ul class="item-info">
		<?php echo implode("\n\t\t\t\t\t", $forum_page['item_body']['info'])."\n" ?>
	</ul>
</div>