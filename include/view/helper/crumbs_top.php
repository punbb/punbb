<?php
namespace punbb;
?>

<?php if (FORUM_PAGE != 'index') { ?>
	<div id="brd-crumbs-top" class="crumbs">
		<p>
			<?= generate_crumbs(false) ?>
		</p>
	</div>
<?php } ?>
