<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<meta charset="utf-8" />
	<title>Error - <?= forum_htmlencode(config()->o_board_title) ?></title>
	<style>
		strong	{ font-weight: bold; }
		body	{ margin: 50px; font: 85%/150% verdana, arial, sans-serif; color: #222; max-width: 55em; }
		h1		{ color: #a00000; font-weight: normal; font-size: 1.45em; }
		code	{ font-family: monospace, sans-serif; }
		.error_line { color: #999; font-size: .95em; }
	</style>
</head>
<body>
	<h1><?= forum_htmlencode(__('Forum error header')) ?></h1>
<?php
	if (isset($message)) {
		echo '<p>'.$message.'</p>'."\n";
	}
	else {
		echo '<p>'.forum_htmlencode(__('Forum error description')).'</p>'."\n";
	}

	if ($num_args > 1) {
		if (defined('FORUM_DEBUG')) {
			$db_error = isset($GLOBALS['forum_db']) ? $GLOBALS['forum_db']->error() : array();
			if (!empty($db_error['error_msg']))
			{
				echo '<p><strong>'.forum_htmlencode(__('Forum error db reported')).'</strong> '.forum_htmlencode($db_error['error_msg']).(($db_error['error_no']) ? ' (Errno: '.$db_error['error_no'].')' : '').'.</p>'."\n";

				if ($db_error['error_sql'] != '')
					echo '<p><strong>'.forum_htmlencode(__('Forum error db query')).'</strong> <code>'.forum_htmlencode($db_error['error_sql']).'</code></p>'."\n";
			}

			if (isset($file) && isset($line))
				echo '<p class="error_line">'.forum_htmlencode(sprintf(__('Forum error location'), $line, $file)).'</p>'."\n";
		}
	}
?>
</body>
</html>