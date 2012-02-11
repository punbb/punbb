<?php
	if (extension_loaded('xdebug')) {
		xdebug_disable();
	}

	// Forum part
	define('FORUM_QUIET_VISIT', 1);
	define('FORUM_ROOT', './../../');

	// Disable error reporting for uninitialized variables
	error_reporting(E_ALL);

	// Turn off PHP time limit
	@set_time_limit(0);

	// We need some stuff from functions.php
	require FORUM_ROOT.'include/essentials.php';
	require_once FORUM_ROOT.'include/parser.php';
	require FORUM_ROOT.'lang/English/common.php';

	if (!defined('FORUM_DEBUG')) {
		define('FORUM_DEBUG', 1);
	}

	require_once FORUM_ROOT . '.dev/tests/PHPUnit/PHPUnit.php';

	// Tests
    require_once FORUM_ROOT . '.dev/tests/functions/utf8_test.php';
	require_once FORUM_ROOT . '.dev/tests/functions/array_insert_test.php';
	require_once FORUM_ROOT . '.dev/tests/functions/tools_test.php';
	require_once FORUM_ROOT . '.dev/tests/censor_words/censor_words_test.php';
	require_once FORUM_ROOT . '.dev/tests/parser/parser.php';


	// Strip out "bad" UTF-8 characters
	forum_remove_bad_characters();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Unit tests suite for PunBB 1.4</title>
<link rel="stylesheet" type="text/css" href="<?php echo FORUM_ROOT ?>style/Oxygen/Oxygen.min.css" />
<style>
	.main-content pre {
		padding: 1em;
	}

	.failed h2 {
		color: red;
	}

	.phpunit-test-results {
		list-style-type: none;
	}

	.phpunit-test-passed em {
		color: green;
		font-style: normal;
	}

	.phpunit-test-failed em {
		color: red;
		font-style: normal;
	}

	.phpunit-test-failed {
		margin: .7em 0;
	}
</style>
</head>
<body>
	<div id="brd-install" class="brd-page">
		<div id="brd-wrap" class="brd">

			<div id="brd-head" class="gen-content">
				<p id="brd-title"><strong>Unit testing</strong></p>
				<p id="brd-desc">Unit tests suite for PunBB 1.4</p>
			</div>


			<div id="brd-main" class="main">
				<div class="main-head">
					<h1 class="hn"><span>Used PHPUnit version 1.3.3</span></h1>
				</div>

				<!-- TEST PASSES -->
				<div class="main-content main-frm">
<?php

	$suite = new PHPUnit_TestSuite('utf8_Test');

	$suite->addTestSuite('array_insert_Test');
	$suite->addTestSuite('tools_Test');
	$suite->addTestSuite('censor_words_do_Test');
	$suite->addTestSuite('handle_url_tag_Test');
	$suite->addTestSuite('parse_message_Test');


	$result = PHPUnit::run($suite);

	echo $result->toHTML();
?>
				</div>


				<!-- RESULTS -->
				<div class="main-subhead">
					<h2 class="hn"><span>SUMMARY RESULTS</span></h2>
				</div>
				<div class="main-content main-frm">
					<?php echo $result->reportTestSummary(TRUE); ?>
				</div>


			</div>
		</div>
	</div>
</body>
</html>
