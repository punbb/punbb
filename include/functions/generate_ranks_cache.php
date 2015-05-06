<?php
namespace punbb;

//
// Generate the ranks cache PHP script
//
function generate_ranks_cache() {
	$return = ($hook = get_hook('ch_fn_generate_ranks_cache_start')) ? eval($hook) : null;
	if ($return != null) {
		return;
	}

	// Get the rank list from the DB
	$query = array(
		'SELECT'	=> 'r.*',
		'FROM'		=> 'ranks AS r',
		'ORDER BY'	=> 'r.min_posts'
	);

	($hook = get_hook('ch_fn_generate_ranks_cache_qr_get_ranks')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);

	$output = array();
	while ($cur_rank = db()->fetch_assoc($result)) {
		$output[] = $cur_rank;
	}

	// Output ranks list as PHP code
	if (!cache()->set('cache_ranks',
		'<?php

		return '.var_export($output, true).';'
	)) {
		error('Unable to write ranks cache file to cache directory.<br />
			Please make sure PHP has write access to the directory \'cache\'.',
			__FILE__, __LINE__);
	}
}
