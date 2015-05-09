<?php
namespace punbb;

//
// Generate the updates cache PHP script
//
function generate_updates_cache() {
	$return = ($hook = get_hook('ch_fn_generate_updates_cache_start')) ? eval($hook) : null;
	if ($return != null) {
		return;
	}

	// Get a list of installed hotfix extensions
	$query = array(
		'SELECT'	=> 'e.id',
		'FROM'		=> 'extensions AS e',
		'WHERE'		=> 'e.id LIKE \'hotfix_%\''
	);

	($hook = get_hook('ch_fn_generate_updates_cache_qr_get_hotfixes')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);

	$hotfixes = array();
	while ($cur_ext_hotfix = db()->fetch_assoc($result))
	{
		$hotfixes[] = urlencode($cur_ext_hotfix['id']);
	}

	// Contact the punbb.informer.com updates service
	$result = get_remote_file('http://punbb.informer.com/update/?type=xml&version='.
		urlencode(config()->o_cur_version).'&hotfixes='.implode(',', $hotfixes), 8);

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
	if (!cache()->set('cache_updates',
		'<?php

		return '.var_export($output, true).';'
	)) {
		error('Unable to write updates cache file to cache directory.<br />
			Please make sure PHP has write access to the directory \'cache\'.',
			__FILE__, __LINE__);
	}
}
