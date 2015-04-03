<!DOCTYPE html>
<!--[if lt IE 7 ]> <html class="oldie ie6" <?= helper('local') ?>> <![endif]-->
<!--[if IE 7 ]>    <html class="oldie ie7" <?= helper('local') ?>> <![endif]-->
<!--[if IE 8 ]>    <html class="oldie ie8" <?= helper('local') ?>> <![endif]-->
<!--[if gt IE 8]><!--> <html <?= helper('local') ?>> <!--<![endif]-->
<head>
<meta charset="utf-8" />
<?php include view('partial/head') ?>
</head>
<body>

<?= helper('messages') ?>

<div id="brd-wrap" class="brd-page">
	<div id="brd-redirect" class="brd">

		<?php include view($forum_main_view) ?>

		<?php include view('partial/debug') ?>

	</div>
</div>

<?php include view('partial/javascript') ?>

</body>
</html>
