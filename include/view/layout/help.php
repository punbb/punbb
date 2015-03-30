<!DOCTYPE html>
<!--[if lt IE 7 ]> <html class="oldie ie6" <?= $view_forum_local ?>> <![endif]-->
<!--[if IE 7 ]>    <html class="oldie ie7" <?= $view_forum_local ?>> <![endif]-->
<!--[if IE 8 ]>    <html class="oldie ie8" <?= $view_forum_local ?>> <![endif]-->
<!--[if gt IE 8]><!--> <html <?= $view_forum_local ?>> <!--<![endif]-->
<head>
<head>
<meta charset="utf-8" />
<?= $view_forum_head ?>
</head>
<body>
<?= $view_forum_messages ?>
<div <?= $view_forum_page ?> class="brd-page">
<div id="brd-wrap" class="brd">

<?php include view($view_forum_main) ?>

</div>
</div>
<?= $view_forum_javascript ?>
</body>
</html>
