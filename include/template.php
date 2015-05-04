<?php
namespace punbb;

class PhpTemplate {

	function render($vars = array()) {
		$GLOBALS = array_merge($GLOBALS, $vars);
		extract($GLOBALS, EXTR_SKIP | EXTR_REFS);
		//extract($vars, EXTR_SKIP | EXTR_REFS);

		include 'header.php';
		include $this->view($template);
		include 'footer.php';
	}

	function view($name) {
		if ($name == '') {
			$name = 'blank';
		}
		// use from style folder
		$fname = theme()->path[user()->style] . '/' . $name . '.php';
		if (!file_exists($fname)) {
			// use default
			$fname =  __DIR__ . '/view/' . $name . '.php';
		}
		return $fname;
	}

	function helper($name, $vars = array()) {
		extract($vars, EXTR_SKIP | EXTR_REFS);

		// use from style folder
		$fname = theme()->path[user()->style] . '/helper/' . $name . '.php';
		if (!file_exists($fname)) {
			// use default
			$fname = __DIR__ . '/view/helper/' . $name . '.php';
		}
		include $fname;
	}

}
