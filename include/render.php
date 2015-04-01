<?php

require FORUM_ROOT . 'header.php';

// init theme
$_tmp_fname = FORUM_ROOT . 'style/' . $forum_user['style'] . '/init.php';
if (file_exists($_tmp_fname)) {
	include $_tmp_fname;
}

// render view
$_tmp_fname = FORUM_ROOT . 'style/' . $forum_user['style'] . '/render.php';
if (file_exists($_tmp_fname)) {
	// use from style folder
	include $_tmp_fname;
}
else {
	// use default
	include view($view_forum_layout);
}

require FORUM_ROOT . 'footer.php';
