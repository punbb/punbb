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
	require FORUM_ROOT . '.dev/tests/lang/langTest.php';

	if (!defined('FORUM_DEBUG')) {
		define('FORUM_DEBUG', 1);
	}

	// Strip out "bad" UTF-8 characters
	forum_remove_bad_characters();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PunBB 1.4 dev</title>
<link rel="stylesheet" type="text/css" href="<?php echo FORUM_ROOT ?>style/Oxygen/Oxygen.min.css" />
<style>
	.locale-tests-results {
		padding: .5em 1em 1em;
	}

	.locale-tests-results h3 {
		font-size: 1.2em;
		color: #D93315 !important;
		font-weight: normal;
	}

	.locale-tests-results ul {
		list-style-type: none;
	}

	.failed em {
		color: red;
		font-style: normal;
	}

	.ok em {
		color: green;
		font-style: normal;
	}

	.phpunit-test-results {
		list-style-type: none;
	}
</style>
</head>
<body>
	<div id="brd-install" class="brd-page">
		<div id="brd-wrap" class="brd">

			<div id="brd-head" class="gen-content">
				<p id="brd-title"><strong>PunBB 1.4 dev</strong></p>
				<p id="brd-desc">Test suite for PunBB 1.4</p>
			</div>


			<div id="brd-main" class="main">
				<div class="main-head">
					<h1 class="hn"><span>Locales tests</span></h1>
				</div>

				<!-- TEST PASSES -->
				<?php
					$langTest = new LangTest();
					$langTest->run();
				?>
			</div>
		</div>
	</div>
</body>
</html>
