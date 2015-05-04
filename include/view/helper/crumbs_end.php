<?php
namespace punbb;
?>

<?php if (FORUM_PAGE != 'index') { ?>
	<div id="brd-crumbs-end" class="crumbs">
		<p>
			<?php template()->helper('crumbs', ['reverse' => false]) ?>
		</p>
	</div>
<?php } ?>
