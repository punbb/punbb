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

<div class="hr"><hr /></div>

<div id="brd-main">
	<?= helper('main_title') ?>
	<?= helper('crumbs_top') ?>
	<?= helper('pagepost_top') ?>
	<?= helper('admin_menu') ?>

	<?php include view($view_forum_main) ?>

	<?= helper('pagepost_end') ?>
	<?= helper('crumbs_end') ?>
</div>

<div class="hr"><hr /></div>

<div id="brd-about">
	<?php include view('partial/about') ?>
</div>

<?php include view('partial/debug') ?>

</div>
</div>

<?php include view('partial/javascript') ?>

</body>
</html>
