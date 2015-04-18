<?php
namespace punbb;

require FORUM_ROOT . 'header.php';

// render view
$_tmp_fname = PUNBB::get('theme')->path[user()->style] . '/render.php';
if (file_exists($_tmp_fname)) {
	// use from style folder
	include $_tmp_fname;
}
else {
	// use default
	include view($forum_layout);
}

require FORUM_ROOT . 'footer.php';
