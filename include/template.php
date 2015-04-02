<?php

function view($name) {
	global $forum_user;

	if ($name == '') {
		$name = 'blank';
	}
	// use from style folder
	$fname = FORUM_ROOT . 'style/' . $forum_user['style'] . '/' . $name . '.php';
	if (!file_exists($fname)) {
		// use default
		$fname =  FORUM_ROOT . 'include/view/' . $name . '.php';
	}

	return $fname;
}

function helper($name) {
	global $forum_user;

	// use from style folder
	$fname = FORUM_ROOT . 'style/' . $forum_user['style'] . '/helper/' . $name . '.php';
	if (!file_exists($fname)) {
		// use default
		$fname = FORUM_ROOT . 'include/view/helper/' . $name . '.php';
	}

	ob_start();
	include $fname;
	$tmp_content = ob_get_contents();
	ob_end_clean();

	return $tmp_content;
}
