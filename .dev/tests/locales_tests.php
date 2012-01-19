<?php

	define('FORUM_ROOT', '../../');

	// List arrays to compare
	$structure = array(
		'lang_admin_bans' => 'admin_bans.php',
		'lang_admin_categories' => 'admin_categories.php',
		'lang_admin_censoring' => 'admin_censoring.php',
		'lang_admin_common' => 'admin_common.php',
		'lang_admin_ext' => 'admin_ext.php',
		'lang_admin_forums' => 'admin_forums.php',
		'lang_admin_groups' => 'admin_groups.php',
		'lang_admin_index' => 'admin_index.php',
		'lang_admin_prune' => 'admin_prune.php',
		'lang_admin_ranks' => 'admin_ranks.php',
		'lang_admin_reindex' => 'admin_reindex.php',
		'lang_admin_reports' => 'admin_reports.php',
		'lang_admin_settings' => 'admin_settings.php',
		'lang_admin_users' => 'admin_users.php',
		'lang_common' => 'common.php',
		'lang_delete' => 'delete.php',
		'lang_forum' => 'forum.php',
		'lang_help' => 'help.php',
		'lang_index' => 'index.php',
		'lang_install' => 'install.php',
		'lang_login' => 'login.php',
		'lang_misc' => 'misc.php',
		'lang_post' => 'post.php',
		'lang_profile' => 'profile.php',
		'lang_search' => 'search.php',
		'lang_topic' => 'topic.php',
		'lang_url_replace' => 'url_replace.php',
		'lang_ul' => 'userlist.php'
	);

	// Default etalon locale
	$locales = array('English');

	// Read list of installed locales
	$dirs = dir(FORUM_ROOT.'lang');
	while (($dir = $dirs->read()) !== false)
	{
		if ($dir != '.' && $dir != '..' && $dir != 'English' && is_dir(FORUM_ROOT.'lang/'.$dir) && file_exists(FORUM_ROOT.'lang/'.$dir.'/common.php'))
			$locales[] = $dir;
	}
	$dirs->close();

	// Check for installed locales
	if (sizeof($locales) < 2)
		exit('No additional locales installed.');

	// Make data to compare
	foreach ($structure as $array => $file) {
		$structure[$array] = array();

		foreach ($locales as $lang) {
			$structure[$array][$lang] = array('file' => $file, 'data' => array());

			include FORUM_ROOT.'lang/'.$lang.'/'.$file;

			if (isset($GLOBALS[$array])) {
				$structure[$array][$lang]['data'] = array_keys($GLOBALS[$array]);

				unset($GLOBALS[$array]);
			}
		}
	}

	// Compare locales data
	foreach ($structure as $array => $langs) {
		$etalon = array_shift($langs);

		foreach ($langs as $lang => $items) {
			$missing = array_diff($etalon['data'], $items['data']);
			$unneeded = array_diff($items['data'], $etalon['data']);

			if (sizeof($missing) || sizeof($unneeded))
				echo '<h4>%FORUM_ROOT%/lang/'.$lang.'/'.$items['file'].':</h4>';

			if (sizeof($missing))
				echo '<ul><li><b>$'.$array.'</b> has missing keys:</li><ul><li>'.implode('</li><li>', $missing).'</li></ul></ul>';

			if (sizeof($unneeded))
				echo '<ul><li><b>$'.$array.'</b> has unneeded keys:</li><ul><li>'.implode('</li><li>', $unneeded).'</li></ul></ul>';
		}
	}

?>
