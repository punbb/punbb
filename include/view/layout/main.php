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

	<div id="brd-wrap" class="brd">
	<div <?= helper('page_attrs') ?>>
	<div id="brd-head" class="gen-content">
		<?= helper('skip_content') ?>
		<?= helper('title') ?>
		<?= helper('description') ?>
	</div>
	<div id="brd-navlinks" class="gen-content">
		<?= helper('navlinks') ?>
		<?= $view_forum_admod ?>
	</div>
	<div id="brd-visit" class="gen-content">
		<?= helper('welcome') ?>
		<?= helper('visit_links') ?>
	</div>

	<?= helper('announcement') ?>

	<div class="hr"><hr /></div>
	<div id="brd-main">
		<?= $view_forum_main_title ?>
		<?= $view_forum_crumbs_top ?>
		<?= $view_forum_main_menu ?>
		<?= $view_forum_main_pagepost_top ?>

		<?php include view($view_forum_main) ?>

		<?= $view_forum_pagepost_end ?>
		<?= $view_forum_crumbs_end ?>
	</div>

	<?php
	if (!empty($view_show_qpost)) {
		include view('viewtopic/qpost');
	}
	?>

	<?php include view('index/info') ?>

	<div class="hr"><hr /></div>
	<div id="brd-about">
		<?php include view('partial/about') ?>
	</div>

	<?php include view('partial/debug') ?>

	</div>
	</div>

	<?php include view('partial/javascript') ?>

	<center>test: <?= date('Y-m-d H:i:s') ?></center>
</body>
</html>
