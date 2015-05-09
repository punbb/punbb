<?php
namespace punbb;

//
// Clean quickjump cache PHP scripts
//
function clean_quickjump_cache($group_id = false) {
	$return = ($hook = get_hook('ch_fn_clean_quickjump_cache_start')) ? eval($hook) : null;
	if ($return != null) {
		return;
	}

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
		$result = db()->query_build($query) or error(__FILE__, __LINE__);

		while ($cur_group = db()->fetch_assoc($result))
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
