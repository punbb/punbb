<?php
namespace punbb;

//
// Generate the forum stats cache PHP script
//
function generate_stats_cache() {
	$stats = array();

	$return = ($hook = get_hook('ch_fn_generate_stats_cache_start')) ? eval($hook) : null;
	if ($return != null) {
		return;
	}

	// Collect some statistics from the database
	$query = array(
		'SELECT'	=> 'COUNT(u.id) - 1',
		'FROM'		=> 'users AS u',
		'WHERE'		=> 'u.group_id != '.FORUM_UNVERIFIED
	);

	($hook = get_hook('ch_fn_generate_stats_cache_qr_get_user_count')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);
	$stats['total_users'] = db()->result($result);


	// Get last registered user info
	$query = array(
		'SELECT'	=> 'u.id, u.username',
		'FROM'		=> 'users AS u',
		'WHERE'		=> 'u.group_id != '.FORUM_UNVERIFIED,
		'ORDER BY'	=> 'u.registered DESC',
		'LIMIT'		=> '1'
	);

	($hook = get_hook('ch_fn_generate_stats_cache_qr_get_newest_user')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);
	$stats['last_user'] = db()->fetch_assoc($result);

	// Get num topics and posts
	$query = array(
		'SELECT'	=> 'SUM(f.num_topics) AS num_topics, SUM(f.num_posts) AS num_posts',
		'FROM'		=> 'forums AS f'
	);

	($hook = get_hook('ch_fn_generate_stats_cache_qr_get_post_stats')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);

	$stats_topics_and_posts = db()->fetch_assoc($result);
	$stats['total_topics'] = $stats_topics_and_posts['num_topics'];
	$stats['total_posts'] = $stats_topics_and_posts['num_posts'];

	$stats['cached'] = time();

	// Output ranks list as PHP code
	if (!cache()->set('cache_stats',
		'<?php

		return '.var_export($stats, true).';'
	)) {
		error('Unable to write stats cache file to cache directory.<br />
			Please make sure PHP has write access to the directory \'cache\'.',
			__FILE__, __LINE__);
	}

	unset($stats);
}
