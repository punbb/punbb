<?php
namespace punbb;
?><!DOCTYPE html>
<!--[if lt IE 7 ]> <html class="oldie ie6" <?php helper('local') ?>> <![endif]-->
<!--[if IE 7 ]>    <html class="oldie ie7" <?php helper('local') ?>> <![endif]-->
<!--[if IE 8 ]>    <html class="oldie ie8" <?php helper('local') ?>> <![endif]-->
<!--[if gt IE 8]><!--> <html <?php helper('local') ?>> <!--<![endif]-->
<head>
<meta charset="utf-8" />
<?php include view('partial/head') ?>
</head>
<body>

<?php helper('messages') ?>

<div id="brd-wrap" class="brd">
	<div <?php helper('page_attrs') ?>>

		<div id="brd-head" class="gen-content">
			<?php helper('skip_content') ?>
			<?php helper('title') ?>
			<?php helper('description') ?>
		</div>

		<div id="brd-navlinks" class="gen-content">
			<?php helper('navlinks') ?>
			<?php include view('partial/admod') ?>
		</div>

		<div id="brd-visit" class="gen-content">
			<?php helper('welcome') ?>
			<?php helper('visit_links') ?>
		</div>

		<div class="hr"><hr /></div>

		<div id="brd-main">
			<?php helper('main_title') ?>
			<?php helper('crumbs_top') ?>
			<?php helper('pagepost_top') ?>
			<?php helper('admin_menu') ?>

			<?php include view($forum_main_view) ?>

			<?php helper('pagepost_end') ?>
			<?php helper('crumbs_end') ?>
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
