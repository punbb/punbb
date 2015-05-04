<?php
namespace punbb;

?>

<h1 class="main-title">
	<?php if (isset($main_title)) { ?>
		<?= $main_title ?>
	<?php } else { ?>
		<?= forum_htmlencode(is_array($last_crumb = end($crumbs))?
			reset($last_crumb) : $last_crumb) ?>
	<?php } ?>
	<?php if (isset($main_head_pages)) { ?>
		<small><?= $main_head_pages ?></small>
	<?php } ?>
</h1>
