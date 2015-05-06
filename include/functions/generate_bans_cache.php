<?php
namespace punbb;

//
// Generate the bans cache PHP script
//
function generate_bans_cache() {
	$return = ($hook = get_hook('ch_fn_generate_bans_cache_start')) ? eval($hook) : null;
	if ($return != null) {
		return;
	}

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
	$result = db()->query_build($query) or error(__FILE__, __LINE__);

	$output = array();
	while ($cur_ban = db()->fetch_assoc($result)) {
		$output[] = $cur_ban;
	}

	// Output ban list as PHP code
	if (!cache()->set('cache_bans',
		'<?php
			return ' . var_export($output, true) . ';'
		)) {
		error('Unable to write bans cache file to cache directory.<br />
			Please make sure PHP has write access to the directory \'cache\'.',
			__FILE__, __LINE__);
	}
}
