<?php

require FORUM_ROOT . 'header.php';

ob_start();
include view($view_forum_layout);
$tpl_main = forum_trim(ob_get_contents());
ob_end_clean();

require FORUM_ROOT . 'footer.php';