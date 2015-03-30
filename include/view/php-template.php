<?php

function view($name) {
	if ($name == '') {
		$name = 'blank';
	}
	return FORUM_ROOT . 'include/view/' . $name . '.php';
}
