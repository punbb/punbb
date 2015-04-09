<?php
namespace punbb;

global $forum_page;
?>

<h1 class="main-title">
	<?php if (isset($forum_page['main_title'])) { ?>
		<?= $forum_page['main_title'] ?>
	<?php } else { ?>
		<?= forum_htmlencode(is_array($last_crumb = end($forum_page['crumbs'])) ?
			reset($last_crumb) : $last_crumb) ?>
	<?php } ?>
	<?php if (isset($forum_page['main_head_pages'])) { ?>
		<small><?= $forum_page['main_head_pages'] ?></small>
	<?php } ?>
</h1>
