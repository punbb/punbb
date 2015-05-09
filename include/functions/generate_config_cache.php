<?php
namespace punbb;

//
// Generate the config cache PHP script
//
function generate_config_cache() {
	// Get the forum config from the DB
	$query = array(
		'SELECT'	=> 'c.*',
		'FROM'		=> 'config AS c'
	);

	$result = db()->query_build($query) or error(__FILE__, __LINE__);
	$output = array();
	while ($cur_config_item = db()->fetch_assoc($result)) {
		$output[$cur_config_item['conf_name']] = $cur_config_item['conf_value'];
	}

	// Output config as PHP code
	if (!cache()->set('cache_config',
		'<?php' .
			"\n\n".
			'return '.var_export($output, true).';'.
			"\n\n"
		)) {
		error('Unable to write configuration cache file to cache directory.<br />
			Please make sure PHP has write access to the directory \'cache\'.',
			__FILE__, __LINE__);
	}
}
