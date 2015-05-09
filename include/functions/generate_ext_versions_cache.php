<?php
namespace punbb;

function generate_ext_versions_cache($inst_exts, $repository_urls, $repository_url_by_extension) {
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

			    if ((0 === strpos($ext['id'], 'pun_') AND FORUM_PUN_EXTENSION_REPOSITORY_URL != $url) OR
			            ((FALSE === strpos($ext['id'], 'pun_') AND !isset($ext['repo_url'])) OR (isset($ext['repo_url']) AND $ext['repo_url'] != $url)))
			        continue;

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
	if (!cache()->set('cache_ext_version_notifications',
		'<?php' .
		"\n\n" .
		'if (!defined(\'FORUM_EXT_VERSIONS_LOADED\')) define(\'FORUM_EXT_VERSIONS_LOADED\', 1);' .
		"\n\n" .
		'$forum_ext_repos = '.var_export($forum_ext_repos, true).';' .
		"\n\n" .
		' $forum_ext_last_versions = '.var_export($forum_ext_last_versions, true) .
		";\n\n" .
		'$forum_ext_versions_update_cache = '.time().";\n\n"
	)) {
		error('Unable to write configuration cache file to cache directory.<br />
			Please make sure PHP has write access to the directory \'cache\'.',
			__FILE__, __LINE__);
	}
}
