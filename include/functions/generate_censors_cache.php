<?php
namespace punbb;

//
// Generate the censor cache PHP script
//
function generate_censors_cache() {
	$return = ($hook = get_hook('ch_fn_generate_censors_cache_start')) ? eval($hook) : null;
	if ($return != null) {
		return;
	}

	// Get the censor list from the DB
	$query = array(
		'SELECT'	=> 'c.*',
		'FROM'		=> 'censoring AS c',
		'ORDER BY'	=> 'c.search_for'
	);

	($hook = get_hook('ch_fn_generate_censors_cache_qr_get_censored_words')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);

	$output = array();
	while ($cur_censor = db()->fetch_assoc($result)) {
		$output[] = $cur_censor;
	}

	// Output censors list as PHP code
	if (!cache()->set('cache_censors',
		'<?php' .
			"\n\n" .
			'$forum_censors = '.var_export($output, true).';'.
			"\n\n	return 1;")) {
		error('Unable to write censor cache file to cache directory.<br />
			Please make sure PHP has write access to the directory \'cache\'.',
			__FILE__, __LINE__);
	}
}
