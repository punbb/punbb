<?php
/**
 * Loads common functions used in the administration panel.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */
namespace punbb;

// Make sure no one attempts to run this script "directly"
if (!defined('FORUM')) {
	exit;
}

// exit if not admin area
if (substr($_SERVER['SCRIPT_NAME'],
			1, strpos($_SERVER['SCRIPT_NAME'], '/', 1) - 1) != 'admin') {
	return;
}

//
// Delete topics from $forum_id that are "older than" $prune_date (if $prune_sticky is 1, sticky topics will also be deleted)
//
function prune($forum_id, $prune_sticky, $prune_date)
{
	global $db_type;

	$return = ($hook = get_hook('ca_fn_prune_start')) ? eval($hook) : null;
	if ($return != null)
		return;

	// Fetch topics to prune
	$query = array(
		'SELECT'	=> 't.id',
		'FROM'		=> 'topics AS t',
		'WHERE'		=> 't.forum_id='.$forum_id
	);

	if ($prune_date != -1)
		$query['WHERE'] .= ' AND last_post<'.$prune_date;
	if (!$prune_sticky)
		$query['WHERE'] .= ' AND sticky=\'0\'';

	($hook = get_hook('ca_fn_prune_qr_get_topics_to_prune')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);

	$topic_ids = array();
	while ($row = db()->fetch_row($result))
		$topic_ids[] = $row[0];

	if (!empty($topic_ids))
	{
		$topic_ids = implode(',', $topic_ids);

		// Fetch posts to prune (used lated for updating the search index)
		$query = array(
			'SELECT'	=> 'p.id',
			'FROM'		=> 'posts AS p',
			'WHERE'		=> 'p.topic_id IN('.$topic_ids.')'
		);

		($hook = get_hook('ca_fn_prune_qr_get_posts_to_prune')) ? eval($hook) : null;
		$result = db()->query_build($query) or error(__FILE__, __LINE__);

		$post_ids = array();
		while ($row = db()->fetch_row($result))
			$post_ids[] = $row[0];

		// Delete topics
		$query = array(
			'DELETE'	=> 'topics',
			'WHERE'		=> 'id IN('.$topic_ids.')'
		);

		($hook = get_hook('ca_fn_prune_qr_prune_topics')) ? eval($hook) : null;
		db()->query_build($query) or error(__FILE__, __LINE__);

		// Delete posts
		$query = array(
			'DELETE'	=> 'posts',
			'WHERE'		=> 'topic_id IN('.$topic_ids.')'
		);

		($hook = get_hook('ca_fn_prune_qr_prune_posts')) ? eval($hook) : null;
		db()->query_build($query) or error(__FILE__, __LINE__);

		// Delete subscriptions
		$query = array(
			'DELETE'	=> 'subscriptions',
			'WHERE'		=> 'topic_id IN('.$topic_ids.')'
		);

		($hook = get_hook('ca_fn_prune_qr_prune_subscriptions')) ? eval($hook) : null;
		db()->query_build($query) or error(__FILE__, __LINE__);

		// We removed a bunch of posts, so now we have to update the search index
		if (!defined('FORUM_SEARCH_IDX_FUNCTIONS_LOADED'))
			require FORUM_ROOT.'include/search_idx.php';

		strip_search_index($post_ids);
	}
}


// Add config value to forum config table
// Warning!
// This function dont refresh config cache - use "forum_clear_cache()" if
// call this function outside install/uninstall extension manifest section
function forum_config_add($name, $value)
{
	if (!empty($name) && empty(config()->$name)) {
		$query = array(
			'INSERT'	=> 'conf_name, conf_value',
			'INTO'		=> 'config',
			'VALUES'	=> '\''.$name.'\', \''.$value.'\''
		);
		db()->query_build($query) or error(__FILE__, __LINE__);
	}
}


// Remove config value from forum config table
// Warning!
// This function dont refresh config cache - use "forum_clear_cache()" if
// call this function outside install/uninstall extension manifest section
function forum_config_remove($name)
{
	if (is_array($name) && count($name) > 0)
	{
		if (!function_exists('clean_conf_names'))
		{
			function clean_conf_names($n)
			{
				return '\''.db()->escape($n).'\'';
			}
		}

		$name = array_map('clean_conf_names', $name);

		$query = array(
			'DELETE'	=> 'config',
			'WHERE'		=> 'conf_name in ('.implode(',', $name).')',
		);
		db()->query_build($query) or error(__FILE__, __LINE__);
	}
	else if (!empty($name))
	{
		$query = array(
			'DELETE'	=> 'config',
			'WHERE'		=> 'conf_name=\''.db()->escape($name).'\''
		);
		db()->query_build($query) or error(__FILE__, __LINE__);
	}
}


($hook = get_hook('ca_new_function')) ? eval($hook) : null;
