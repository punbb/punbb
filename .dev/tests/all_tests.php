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

	if (!defined('FORUM_DEBUG')) {
		define('FORUM_DEBUG', 1);
	}

	require_once('PHPUnit/PHPUnit.php');

	// Tests
    require_once('functions/utf8_test.php');
	require_once('functions/array_insert_test.php');
	require_once('functions/tools_test.php');


	// Strip out "bad" UTF-8 characters
	forum_remove_bad_characters();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Unit tests suite for PunBB 1.4</title>
<link rel="stylesheet" type="text/css" href="<?php echo FORUM_ROOT ?>style/Oxygen/Oxygen.css" />
<link rel="stylesheet" type="text/css" href="<?php echo FORUM_ROOT ?>style/Oxygen/Oxygen_cs.css" />
<!--[if lte IE 6]><link rel="stylesheet" type="text/css" href="<?php echo FORUM_ROOT ?>style/Oxygen/Oxygen_ie6.css" /><![endif]-->
<!--[if IE 7]><link rel="stylesheet" type="text/css" href="<?php echo FORUM_ROOT ?>style/Oxygen/Oxygen_ie7.css" /><![endif]-->
<style>
	.main-content pre {
		padding: 1em;
	}

	.failed h2 {
		color: red;
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

	$result = PHPUnit::run($suite);

	echo $result->toHTML();
?>
				</div>


<?php

	// FAILURE?
	if (!$result->wasSuccessful()):
?>
				<!-- FAILED PASSES -->
				<div class="main-subhead failed">
					<h2 class="hn"><span>FAILED TESTS</span></h2>
				</div>
				<div class="main-content main-frm">
					<?php echo $result->reportFailureListing(TRUE); ?>
				</div>
<?php
	endif;
?>


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
