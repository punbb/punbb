<?php
namespace punbb;
?>

<?php if (FORUM_PAGE != 'index') { ?>
	<div id="brd-crumbs-top" class="crumbs">
		<p>
			<?php template()->helper('crumbs', [
				'crumbs' => $crumbs,
				'reverse' => false
			]) ?>
		</p>
	</div>
<?php } ?>
