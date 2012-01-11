<?php
/**
 * Caching functions.
 *
 * This file contains all of the functions used to generate the cache files used by the site.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */


// Make sure no one attempts to run this script "directly"
if (!defined('FORUM'))
	exit;


// Safe create or write of cache files
// Use LOCK
function write_cache_file($file, $content)
{
	// Open
	$handle = @fopen($file, 'r+b'); // @ - file may not exist
	if (!$handle)
	{
		$handle = fopen($file, 'wb');
		if (!$handle)
		{
			return false;
		}
	}

	// Lock
	flock($handle, LOCK_EX);
	ftruncate($handle, 0);

	// Write
	if (fwrite($handle, $content) === false)
	{
		// Unlock and close
		flock($handle, LOCK_UN);
		fclose($handle);

		return false;
	}

	// Unlock and close
	flock($handle, LOCK_UN);
	fclose($handle);

	return true;
}


//
// Generate the config cache PHP script
//
function generate_config_cache()
{
	global $forum_db;

	$return = ($hook = get_hook('ch_fn_generate_config_cache_start')) ? eval($hook) : null;
	if ($return != null)
		return;

	// Get the forum config from the DB
	$query = array(
		'SELECT'	=> 'c.*',
		'FROM'		=> 'config AS c'
	);

	($hook = get_hook('ch_fn_generate_config_cache_qr_get_config')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

	$output = array();
	while ($cur_config_item = $forum_db->fetch_assoc($result))
		$output[$cur_config_item['conf_name']] = $cur_config_item['conf_value'];

	// Output config as PHP code
	if (!write_cache_file(FORUM_CACHE_DIR.'cache_config.php', '<?php'."\n\n".'define(\'FORUM_CONFIG_LOADED\', 1);'."\n\n".'$forum_config = '.var_export($output, true).';'."\n\n".'?>'))
	{
		error('Unable to write configuration cache file to cache directory.<br />Please make sure PHP has write access to the directory \'cache\'.', __FILE__, __LINE__);
	}
}


//
// Generate the bans cache PHP script
//
function generate_bans_cache()
{
	global $forum_db;

	$return = ($hook = get_hook('ch_fn_generate_bans_cache_start')) ? eval($hook) : null;
	if ($return != null)
		return;

	// Get the ban list from the DB
	$query = array(
		'SELECT'	=> 'b.*, u.username AS ban_creator_username',
		'FROM'		=> 'bans AS b',
		'JOINS'		=> array(
			array(
				'LEFT JOIN'		=> 'users AS u',
				'ON'			=> 'u.id=b.ban_creator'
			)
		),
		'ORDER BY'	=> 'b.id'
	);

	($hook = get_hook('ch_fn_generate_bans_cache_qr_get_bans')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

	$output = array();
	while ($cur_ban = $forum_db->fetch_assoc($result))
		$output[] = $cur_ban;

	// Output ban list as PHP code
	if (!write_cache_file(FORUM_CACHE_DIR.'cache_bans.php', '<?php'."\n\n".'define(\'FORUM_BANS_LOADED\', 1);'."\n\n".'$forum_bans = '.var_export($output, true).';'."\n\n".'?>'))
	{
		error('Unable to write bans cache file to cache directory.<br />Please make sure PHP has write access to the directory \'cache\'.', __FILE__, __LINE__);
	}
}


//
// Generate the ranks cache PHP script
//
function generate_ranks_cache()
{
	global $forum_db;

	$return = ($hook = get_hook('ch_fn_generate_ranks_cache_start')) ? eval($hook) : null;
	if ($return != null)
		return;

	// Get the rank list from the DB
	$query = array(
		'SELECT'	=> 'r.*',
		'FROM'		=> 'ranks AS r',
		'ORDER BY'	=> 'r.min_posts'
	);

	($hook = get_hook('ch_fn_generate_ranks_cache_qr_get_ranks')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

	$output = array();
	while ($cur_rank = $forum_db->fetch_assoc($result))
		$output[] = $cur_rank;

	// Output ranks list as PHP code
	if (!write_cache_file(FORUM_CACHE_DIR.'cache_ranks.php', '<?php'."\n\n".'define(\'FORUM_RANKS_LOADED\', 1);'."\n\n".'$forum_ranks = '.var_export($output, true).';'."\n\n".'?>'))
	{
		error('Unable to write ranks cache file to cache directory.<br />Please make sure PHP has write access to the directory \'cache\'.', __FILE__, __LINE__);
	}
}


//
// Generate the forum stats cache PHP script
//
function generate_stats_cache()
{
	global $forum_db;

	$stats = array();

	$return = ($hook = get_hook('ch_fn_generate_stats_cache_start')) ? eval($hook) : null;
	if ($return != null)
		return;

	// Collect some statistics from the database
	$query = array(
		'SELECT'	=> 'COUNT(u.id) - 1',
		'FROM'		=> 'users AS u',
		'WHERE'		=> 'u.group_id != '.FORUM_UNVERIFIED
	);

	($hook = get_hook('ch_fn_generate_stats_cache_qr_get_user_count')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	$stats['total_users'] = $forum_db->result($result);


	// Get last registered user info
	$query = array(
		'SELECT'	=> 'u.id, u.username',
		'FROM'		=> 'users AS u',
		'WHERE'		=> 'u.group_id != '.FORUM_UNVERIFIED,
		'ORDER BY'	=> 'u.registered DESC',
		'LIMIT'		=> '1'
	);

	($hook = get_hook('ch_fn_generate_stats_cache_qr_get_newest_user')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	$stats['last_user'] = $forum_db->fetch_assoc($result);

	// Get num topics and posts
	$query = array(
		'SELECT'	=> 'SUM(f.num_topics) AS num_topics, SUM(f.num_posts) AS num_posts',
		'FROM'		=> 'forums AS f'
	);

	($hook = get_hook('ch_fn_generate_stats_cache_qr_get_post_stats')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

	$stats_topics_and_posts = $forum_db->fetch_assoc($result);
	$stats['total_topics'] = $stats_topics_and_posts['num_topics'];
	$stats['total_posts'] = $stats_topics_and_posts['num_posts'];

	$stats['cached'] = time();

	// Output ranks list as PHP code
	if (!write_cache_file(FORUM_CACHE_DIR.'cache_stats.php', '<?php'."\n\n".'if (!defined(\'FORUM_STATS_LOADED\')) define(\'FORUM_STATS_LOADED\', 1);'."\n\n".'$forum_stats = '.var_export($stats, true).';'."\n\n".'?>'))
	{
		error('Unable to write stats cache file to cache directory.<br />Please make sure PHP has write access to the directory \'cache\'.', __FILE__, __LINE__);
	}

	unset($stats);
}


//
// Clean stats cache PHP scripts
//
function clean_stats_cache()
{
	$cache_file = FORUM_CACHE_DIR.'cache_stats.php';
	if (file_exists($cache_file))
	{
		unlink($cache_file);
	}
}


//
// Generate the censor cache PHP script
//
function generate_censors_cache()
{
	global $forum_db;

	$return = ($hook = get_hook('ch_fn_generate_censors_cache_start')) ? eval($hook) : null;
	if ($return != null)
		return;

	// Get the censor list from the DB
	$query = array(
		'SELECT'	=> 'c.*',
		'FROM'		=> 'censoring AS c',
		'ORDER BY'	=> 'c.search_for'
	);

	($hook = get_hook('ch_fn_generate_censors_cache_qr_get_censored_words')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

	$output = array();
	while ($cur_censor = $forum_db->fetch_assoc($result))
		$output[] = $cur_censor;

	// Output censors list as PHP code
	if (!write_cache_file(FORUM_CACHE_DIR.'cache_censors.php', '<?php'."\n\n".'define(\'FORUM_CENSORS_LOADED\', 1);'."\n\n".'$forum_censors = '.var_export($output, true).';'."\n\n".'?>'))
	{
		error('Unable to write censor cache file to cache directory.<br />Please make sure PHP has write access to the directory \'cache\'.', __FILE__, __LINE__);
	}
}


//
// Generate quickjump cache PHP scripts
//
function generate_quickjump_cache($group_id = false)
{
	global $forum_db, $lang_common, $forum_url, $forum_config, $forum_user, $base_url;

	$return = ($hook = get_hook('ch_fn_generate_quickjump_cache_start')) ? eval($hook) : null;
	if ($return != null)
		return;

	$groups = array();

	// If a group_id was supplied, we generate the quickjump cache for that group only
	if ($group_id !== false)
		$groups[0] = $group_id;
	else
	{
		// A group_id was not supplied, so we generate the quickjump cache for all groups
		$query = array(
			'SELECT'	=> 'g.g_id',
			'FROM'		=> 'groups AS g'
		);

		($hook = get_hook('ch_fn_generate_quickjump_cache_qr_get_groups')) ? eval($hook) : null;
		$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

		while ($cur_group = $forum_db->fetch_assoc($result))
		{
			$groups[] = $cur_group['g_id'];
		}
	}

	// Loop through the groups in $groups and output the cache for each of them
	foreach ($groups as $group_id)
	{
		$output = '<?php'."\n\n".'if (!defined(\'FORUM\')) exit;'."\n".'define(\'FORUM_QJ_LOADED\', 1);'."\n".'$forum_id = isset($forum_id) ? $forum_id : 0;'."\n\n".'?>';
		$output .= '<form id="qjump" method="get" accept-charset="utf-8" action="'.$base_url.'/viewforum.php">'."\n\t".'<div class="frm-fld frm-select">'."\n\t\t".'<label for="qjump-select"><span><?php echo $lang_common[\'Jump to\'] ?>'.'</span></label><br />'."\n\t\t".'<span class="frm-input"><select id="qjump-select" name="id">'."\n";

		// Get the list of categories and forums from the DB
		$query = array(
			'SELECT'	=> 'c.id AS cid, c.cat_name, f.id AS fid, f.forum_name, f.redirect_url',
			'FROM'		=> 'categories AS c',
			'JOINS'		=> array(
				array(
					'INNER JOIN'	=> 'forums AS f',
					'ON'			=> 'c.id=f.cat_id'
				),
				array(
					'LEFT JOIN'		=> 'forum_perms AS fp',
					'ON'			=> '(fp.forum_id=f.id AND fp.group_id='.$group_id.')'
				)
			),
			'WHERE'		=> 'fp.read_forum IS NULL OR fp.read_forum=1',
			'ORDER BY'	=> 'c.disp_position, c.id, f.disp_position'
		);

		($hook = get_hook('ch_fn_generate_quickjump_cache_qr_get_cats_and_forums')) ? eval($hook) : null;
		$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

		$forums = array();
		while ($cur_forum = $forum_db->fetch_assoc($result))
		{
			$forums[] = $cur_forum;
		}

		$cur_category = 0;
		$forum_count = 0;
		$sef_friendly_names = array();
		foreach ($forums as $cur_forum)
		{
			($hook = get_hook('ch_fn_generate_quickjump_cache_forum_loop_start')) ? eval($hook) : null;

			if ($cur_forum['cid'] != $cur_category)	// A new category since last iteration?
			{
				if ($cur_category)
					$output .= "\t\t\t".'</optgroup>'."\n";

				$output .= "\t\t\t".'<optgroup label="'.forum_htmlencode($cur_forum['cat_name']).'">'."\n";
				$cur_category = $cur_forum['cid'];
			}

			$sef_friendly_names[$cur_forum['fid']] = sef_friendly($cur_forum['forum_name']);
			$redirect_tag = ($cur_forum['redirect_url'] != '') ? ' &gt;&gt;&gt;' : '';
			$output .= "\t\t\t\t".'<option value="'.$cur_forum['fid'].'"<?php echo ($forum_id == '.$cur_forum['fid'].') ? \' selected="selected"\' : \'\' ?>>'.forum_htmlencode($cur_forum['forum_name']).$redirect_tag.'</option>'."\n";
			$forum_count++;
		}

		$output .= "\t\t\t".'</optgroup>'."\n\t\t".'</select>'."\n\t\t".'<input type="submit" id="qjump-submit" value="<?php echo $lang_common[\'Go\'] ?>" /></span>'."\n\t".'</div>'."\n".'</form>'."\n";
		$output_js = "\n".'(function () {'."\n\t".'var forum_quickjump_url = "'.forum_link($forum_url['forum']).'";'."\n\t".'var sef_friendly_url_array = new Array('.count($forums).');';

		foreach ($sef_friendly_names as $forum_id => $forum_name)
			$output_js .= "\n\t".'sef_friendly_url_array['.$forum_id.'] = "'.forum_htmlencode($forum_name).'";';

		// Add Load Event
		$output_js .= "\n\n\t".'PUNBB.common.addDOMReadyEvent(function () { PUNBB.common.attachQuickjumpRedirect(forum_quickjump_url, sef_friendly_url_array); });'."\n".'}());';

		if ($forum_count < 2)
			$output = '<?php'."\n\n".'if (!defined(\'FORUM\')) exit;'."\n".'define(\'FORUM_QJ_LOADED\', 1);';
		else
			$output .= '<?php'."\n\n".'$forum_javascript_quickjump_code = <<<EOL'.$output_js."\nEOL;\n\n".'$forum_loader->add_js($forum_javascript_quickjump_code, array(\'type\' => \'inline\', \'weight\' => 60, \'group\' => FORUM_JS_GROUP_SYSTEM));'."\n".'?>'."\n";

		// Output quickjump as PHP code
		if (!write_cache_file(FORUM_CACHE_DIR.'cache_quickjump_'.$group_id.'.php', $output))
		{
			error('Unable to write quickjump cache file to cache directory.<br />Please make sure PHP has write access to the directory \'cache\'.', __FILE__, __LINE__);
		}
	}
}


//
// Clean quickjump cache PHP scripts
//
function clean_quickjump_cache($group_id = false)
{
	global $forum_db;

	$return = ($hook = get_hook('ch_fn_clean_quickjump_cache_start')) ? eval($hook) : null;
	if ($return != null)
		return;

	$groups = array();

	// If a group_id was supplied, we generate the quickjump cache for that group only
	if ($group_id !== false)
		$groups[0] = $group_id;
	else
	{
		// A group_id was not supplied, so we generate the quickjump cache for all groups
		$query = array(
			'SELECT'	=> 'g.g_id',
			'FROM'		=> 'groups AS g'
		);

		($hook = get_hook('ch_fn_clean_quickjump_cache_qr_get_groups')) ? eval($hook) : null;
		$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

		while ($cur_group = $forum_db->fetch_assoc($result))
		{
			$groups[] = $cur_group['g_id'];
		}
	}

	// Loop through the groups in $groups and output the cache for each of them
	foreach ($groups as $group_id)
	{
		// Output quickjump as PHP code
		$qj_cache_file = FORUM_CACHE_DIR.'cache_quickjump_'.$group_id.'.php';
		if (file_exists($qj_cache_file))
		{
			unlink($qj_cache_file);
		}
	}
}



//
// Generate the hooks cache PHP script
//
function generate_hooks_cache()
{
	global $forum_db, $forum_config, $base_url;

	$return = ($hook = get_hook('ch_fn_generate_hooks_cache_start')) ? eval($hook) : null;
	if ($return != null)
		return;

	// Get the hooks from the DB
	$query = array(
		'SELECT'	=> 'eh.id, eh.code, eh.extension_id, e.dependencies',
		'FROM'		=> 'extension_hooks AS eh',
		'JOINS'		=> array(
			array(
				'INNER JOIN'	=> 'extensions AS e',
				'ON'			=> 'e.id=eh.extension_id'
			)
		),
		'WHERE'		=> 'e.disabled=0',
		'ORDER BY'	=> 'eh.priority, eh.installed'
	);

	($hook = get_hook('ch_fn_generate_hooks_cache_qr_get_hooks')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

	$output = array();
	while ($cur_hook = $forum_db->fetch_assoc($result))
	{
		$load_ext_info = '$GLOBALS[\'ext_info_stack\'][] = array('."\n".
			'\'id\'				=> \''.$cur_hook['extension_id'].'\','."\n".
			'\'path\'			=> FORUM_ROOT.\'extensions/'.$cur_hook['extension_id'].'\','."\n".
			'\'url\'			=> $GLOBALS[\'base_url\'].\'/extensions/'.$cur_hook['extension_id'].'\','."\n".
			'\'dependencies\'	=> array ('."\n";

		$dependencies = explode('|', substr($cur_hook['dependencies'], 1, -1));
		foreach ($dependencies as $cur_dependency)
		{
			// This happens if there are no dependencies because explode ends up returning an array with one empty element
			if (empty($cur_dependency))
				continue;

			$load_ext_info .= '\''.$cur_dependency.'\'	=> array('."\n".
				'\'id\'				=> \''.$cur_dependency.'\','."\n".
				'\'path\'			=> FORUM_ROOT.\'extensions/'.$cur_dependency.'\','."\n".
				'\'url\'			=> $GLOBALS[\'base_url\'].\'/extensions/'.$cur_dependency.'\'),'."\n";
		}

		$load_ext_info .= ')'."\n".');'."\n".'$ext_info = $GLOBALS[\'ext_info_stack\'][count($GLOBALS[\'ext_info_stack\']) - 1];';
		$unload_ext_info = 'array_pop($GLOBALS[\'ext_info_stack\']);'."\n".'$ext_info = empty($GLOBALS[\'ext_info_stack\']) ? array() : $GLOBALS[\'ext_info_stack\'][count($GLOBALS[\'ext_info_stack\']) - 1];';

		$output[$cur_hook['id']][] = $load_ext_info."\n\n".$cur_hook['code']."\n\n".$unload_ext_info."\n";
	}

	// Output hooks as PHP code
	if (!write_cache_file(FORUM_CACHE_DIR.'cache_hooks.php', '<?php'."\n\n".'define(\'FORUM_HOOKS_LOADED\', 1);'."\n\n".'$forum_hooks = '.var_export($output, true).';'."\n\n".'?>'))
	{
		error('Unable to write hooks cache file to cache directory.<br />Please make sure PHP has write access to the directory \'cache\'.', __FILE__, __LINE__);
	}
}


//
// Generate the updates cache PHP script
//
function generate_updates_cache()
{
	global $forum_db, $forum_config;

	$return = ($hook = get_hook('ch_fn_generate_updates_cache_start')) ? eval($hook) : null;
	if ($return != null)
		return;

	// Get a list of installed hotfix extensions
	$query = array(
		'SELECT'	=> 'e.id',
		'FROM'		=> 'extensions AS e',
		'WHERE'		=> 'e.id LIKE \'hotfix_%\''
	);

	($hook = get_hook('ch_fn_generate_updates_cache_qr_get_hotfixes')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

	$hotfixes = array();
	while ($cur_ext_hotfix = $forum_db->fetch_assoc($result))
	{
		$hotfixes[] = urlencode($cur_ext_hotfix['id']);
	}

	// Contact the punbb.informer.com updates service
	$result = get_remote_file('http://punbb.informer.com/update/?type=xml&version='.urlencode($forum_config['o_cur_version']).'&hotfixes='.implode(',', $hotfixes), 8);

	// Make sure we got everything we need
	if ($result != null && strpos($result['content'], '</updates>') !== false)
	{
		if (!defined('FORUM_XML_FUNCTIONS_LOADED'))
			require FORUM_ROOT.'include/xml.php';

		$output = xml_to_array(forum_trim($result['content']));
		$output = current($output);

		if (!empty($output['hotfix']) && is_array($output['hotfix']) && !is_array(current($output['hotfix'])))
			$output['hotfix'] = array($output['hotfix']);

		$output['cached'] = time();
		$output['fail'] = false;
	}
	else	// If the update check failed, set the fail flag
		$output = array('cached' => time(), 'fail' => true);

	// This hook could potentially (and responsibly) be used by an extension to do its own little update check
	($hook = get_hook('ch_fn_generate_updates_cache_write')) ? eval($hook) : null;

	// Output update status as PHP code
	if (!write_cache_file(FORUM_CACHE_DIR.'cache_updates.php', '<?php'."\n\n".'if (!defined(\'FORUM_UPDATES_LOADED\')) define(\'FORUM_UPDATES_LOADED\', 1);'."\n\n".'$forum_updates = '.var_export($output, true).';'."\n\n".'?>'))
	{
		error('Unable to write updates cache file to cache directory.<br />Please make sure PHP has write access to the directory \'cache\'.', __FILE__, __LINE__);
	}
}

function generate_ext_versions_cache($inst_exts, $repository_urls, $repository_url_by_extension)
{
	$forum_ext_last_versions = array();
	$forum_ext_repos = array();

	foreach (array_unique(array_merge($repository_urls, $repository_url_by_extension)) as $url)
	{
		// Get repository timestamp
		$remote_file = get_remote_file($url.'/timestamp', 2);
		$repository_timestamp = empty($remote_file['content']) ? '' : forum_trim($remote_file['content']);
		unset($remote_file);
		if (!is_numeric($repository_timestamp))
			continue;

		if (!isset($forum_ext_repos[$url]['timestamp']))
			$forum_ext_repos[$url]['timestamp'] = $repository_timestamp;

		if ($forum_ext_repos[$url]['timestamp'] <= $repository_timestamp)
		{
			foreach ($inst_exts as $ext)
			{
				$remote_file = get_remote_file($url.'/'.$ext['id'].'/lastversion', 2);
				$version = empty($remote_file['content']) ? '' : forum_trim($remote_file['content']);
				unset($remote_file);
				if (empty($version) || !preg_match('~^[0-9a-zA-Z\. +-]+$~u', $version))
					continue;

				$forum_ext_repos[$url]['extension_versions'][$ext['id']] = $version;

				// If key with current extension exist in array, compare it with version in repository
				if (!isset($forum_ext_last_versions[$ext['id']]) || (version_compare($forum_ext_last_versions[$ext['id']]['version'], $version, '<')))
				{
					$forum_ext_last_versions[$ext['id']] = array('version' => $version, 'repo_url' => $url);

					$remote_file = get_remote_file($url.'/'.$ext['id'].'/lastchanges', 2);
					$last_changes = empty($remote_file['content']) ? '' : forum_trim($remote_file['content']);
					unset($remote_file);
					if (!empty($last_changes))
						$forum_ext_last_versions[$ext['id']]['changes'] = $last_changes;
				}
			}

			// Write timestamp to cache
			$forum_ext_repos[$url]['timestamp'] = $repository_timestamp;
		}
	}

	if (array_keys($forum_ext_last_versions) != array_keys($inst_exts))
		foreach ($inst_exts as $ext)
			if (!in_array($ext['id'], array_keys($forum_ext_last_versions)))
				$forum_ext_last_versions[$ext['id']] = array('version' => $ext['version'], 'repo_url' => '', 'changes' => '');

	($hook = get_hook('ch_generate_ext_versions_cache_check_repository')) ? eval($hook) : null;

	// Output config as PHP code
	if (!write_cache_file(FORUM_CACHE_DIR.'cache_ext_version_notifications.php', '<?php'."\n\n".'if (!defined(\'FORUM_EXT_VERSIONS_LOADED\')) define(\'FORUM_EXT_VERSIONS_LOADED\', 1);'."\n\n".'$forum_ext_repos = '.var_export($forum_ext_repos, true).';'."\n\n".' $forum_ext_last_versions = '.var_export($forum_ext_last_versions, true).";\n\n".'$forum_ext_versions_update_cache = '.time().";\n\n".'?>'))
	{
		error('Unable to write configuration cache file to cache directory.<br />Please make sure PHP has write access to the directory \'cache\'.', __FILE__, __LINE__);
	}
}

define('FORUM_CACHE_FUNCTIONS_LOADED', 1);
