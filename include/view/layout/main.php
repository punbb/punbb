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

	<?php template()->helper('messages') ?>

	<div id="brd-wrap" class="brd">
		<div <?php template()->helper('page_attrs') ?>>
			<div id="brd-head" class="gen-content">
				<?php template()->helper('skip_content') ?>
				<?php template()->helper('title') ?>
				<?php template()->helper('description') ?>
			</div>
			<div id="brd-navlinks" class="gen-content">
				<?php template()->helper('navlinks') ?>
				<?php include template()->view('partial/admod') ?>
			</div>
			<div id="brd-visit" class="gen-content">
				<?php template()->helper('welcome') ?>
				<?php template()->helper('visit_links') ?>
			</div>

			<?php template()->helper('announcement') ?>

			<div class="hr"><hr /></div>
			<div id="brd-main">
				<?php template()->helper('main_title', ['main_title' => $main_title]) ?>

				<?php template()->helper('crumbs_top', ['crumbs' => $crumbs]) ?>

				<?php template()->helper('main_menu', [
					'main_menu' => $main_menu
				]) ?>

				<?php template()->helper('pagepost_top') ?>

				<?php include template()->view($main_view) ?>

				<?php template()->helper('pagepost_end') ?>

				<?php template()->helper('crumbs_end', ['crumbs' => $crumbs]) ?>
			</div>

			<?php
			if (!empty($show_qpost)) {
				include template()->view('viewtopic/qpost');
			}
			?>

			<?php include template()->view('index/info') ?>

			<div class="hr"><hr /></div>
			<div id="brd-about">
				<?php include template()->view('partial/about') ?>
			</div>

			<?php include template()->view('partial/debug') ?>

		</div>
	</div>

	<?php include template()->view('partial/javascript') ?>

</body>
</html>
