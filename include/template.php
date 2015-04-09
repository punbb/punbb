<?php
namespace punbb;

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

function helper($name, $vars = array()) {
	global $forum_user;
	extract($vars, EXTR_SKIP | EXTR_REFS);

	// use from style folder
	$fname = FORUM_ROOT . 'style/' . $forum_user['style'] . '/helper/' . $name . '.php';
	if (!file_exists($fname)) {
		// use default
		$fname = FORUM_ROOT . 'include/view/helper/' . $name . '.php';
	}
	include $fname;
}
