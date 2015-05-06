<?php
namespace punbb;

//
// Generate the hooks cache PHP script
//
function generate_hooks_cache() {
	global $base_url;

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

	$result = db()->query_build($query) or error(__FILE__, __LINE__);

	$output = array();
	while ($cur_hook = db()->fetch_assoc($result)) {
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
	if (!cache()->set('cache_hooks',
		'<?php

		return '.var_export($output, true).';'
	)) {
		error('Unable to write hooks cache file to cache directory.<br />
			Please make sure PHP has write access to the directory \'cache\'.',
			__FILE__, __LINE__);
	}
}
