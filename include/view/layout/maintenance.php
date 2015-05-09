<?php
namespace punbb;
?><!DOCTYPE html>
<!--[if lt IE 7 ]> <html class="oldie ie6" <?php template()->helper('local') ?>> <![endif]-->
<!--[if IE 7 ]>    <html class="oldie ie7" <?php template()->helper('local') ?>> <![endif]-->
<!--[if IE 8 ]>    <html class="oldie ie8" <?php template()->helper('local') ?>> <![endif]-->
<!--[if gt IE 8]><!--> <html <?php template()->helper('local') ?>> <!--<![endif]-->
<head>
<meta charset="utf-8" />
<?php include template()->view('partial/head') ?>
</head>
<body>
<div id="brd-wrap" class="brd-page">
	<div id="brd-maint" class="brd">

		<?php include template()->view($main_view) ?>

	</div>
</div>

<?php include template()->view('partial/javascript') ?>

</body>
</html>
