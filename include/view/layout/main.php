<!DOCTYPE html>
<!--[if lt IE 7 ]> <html class="oldie ie6" <?= $view_forum_local ?>> <![endif]-->
<!--[if IE 7 ]>    <html class="oldie ie7" <?= $view_forum_local ?>> <![endif]-->
<!--[if IE 8 ]>    <html class="oldie ie8" <?= $view_forum_local ?>> <![endif]-->
<!--[if gt IE 8]><!--> <html <?= $view_forum_local ?>> <!--<![endif]-->
<head>
<meta charset="utf-8" />
<?= $view_forum_head ?>
</head>
<body>
	<?= $view_forum_messages ?>
	<div id="brd-wrap" class="brd">
	<div <?= $view_forum_page ?>>
	<div id="brd-head" class="gen-content">
		<?= $view_forum_skip ?>
		<?= $view_forum_title ?>
		<?= $view_forum_desc ?>
	</div>
	<div id="brd-navlinks" class="gen-content">
		<?= $view_forum_navlinks ?>
		<?= $view_forum_admod ?>
	</div>
	<div id="brd-visit" class="gen-content">
		<?= $view_forum_welcome ?>
		<?= $view_forum_visit ?>
	</div>
	<?= $view_forum_announcement ?>
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
		<?= $view_forum_qpost ?>

		<?php include view('index/info') ?>

	<div class="hr"><hr /></div>
	<div id="brd-about">
		<?= $view_forum_about ?>
	</div>
		<?= $view_forum_debug ?>
	</div>
	</div>
	<?= $view_forum_javascript ?>

	<center>test: <?= date('Y-m-d H:i:s') ?></center>
</body>
</html>
