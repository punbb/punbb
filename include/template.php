<?php
namespace punbb;

function view($name) {
	if ($name == '') {
		$name = 'blank';
	}
	// use from style folder
	$fname = FORUM_ROOT . 'style/' . user()->style . '/' . $name . '.php';
	if (!file_exists($fname)) {
		// use default
		$fname =  FORUM_ROOT . 'include/view/' . $name . '.php';
	}

	return $fname;
}

function helper($name, $vars = array()) {
	extract($vars, EXTR_SKIP | EXTR_REFS);

	// use from style folder
	$fname = FORUM_ROOT . 'style/' . user()->style . '/helper/' . $name . '.php';
	if (!file_exists($fname)) {
		// use default
		$fname = FORUM_ROOT . 'include/view/helper/' . $name . '.php';
	}
	include $fname;
}
