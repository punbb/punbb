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
<div id="brd-wrap" class="brd-page">
<div id="brd-maint" class="brd">

<?php include view($view_forum_main) ?>

</div>
</div>

<?php include view('partial/javascript') ?>

</body>
</html>
