<?php

function view($name) {
	if ($name == '') {
		$name = 'blank';
	}
	return FORUM_ROOT . 'include/view/' . $name . '.php';
}

function helper($name) {
	return include FORUM_ROOT . 'include/view/helper/' . $name . '.php';
}