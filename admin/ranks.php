<?php
/**
 * Rank management page.
 *
 * Allows administrators to control the tags given to posters based on their post count.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */
namespace punbb;

require __DIR__ . '/../vendor/pautoload.php';

($hook = get_hook('ark_start')) ? eval($hook) : null;

if (user()->g_id != FORUM_ADMIN) {
	message(__('No permission'));
}

// Add a rank
if (isset($_POST['add_rank'])) {
	$rank = forum_trim($_POST['new_rank']);
	$min_posts = intval($_POST['new_min_posts']);

	if ($rank == '')
		message(__('Title message', 'admin_ranks'));

	if ($min_posts < 0)
		message(__('Min posts message', 'admin_ranks'));

	($hook = get_hook('ark_add_rank_form_submitted')) ? eval($hook) : null;

	// Make sure there isn't already a rank with the same min_posts value
	$query = array(
		'SELECT'	=> 'COUNT(r.id)',
		'FROM'		=> 'ranks AS r',
		'WHERE'		=> 'min_posts='.$min_posts
	);

	($hook = get_hook('ark_add_rank_qr_check_rank_collision')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);

	if (db()->result($result) > 0)
		message(sprintf(__('Min posts occupied message', 'admin_ranks'), $min_posts));

	$query = array(
		'INSERT'	=> 'rank, min_posts',
		'INTO'		=> 'ranks',
		'VALUES'	=> '\''.db()->escape($rank).'\', '.$min_posts
	);

	($hook = get_hook('ark_add_rank_qr_add_rank')) ? eval($hook) : null;
	db()->query_build($query) or error(__FILE__, __LINE__);

	// Regenerate the ranks cache
	require FORUM_ROOT . 'include/cache.php';
	generate_ranks_cache();

	// Add flash message
	flash()->add_info(__('Rank added', 'admin_ranks'));

	($hook = get_hook('ark_add_rank_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['admin_ranks']), __('Rank added', 'admin_ranks'));
}


// Update a rank
else if (isset($_POST['update']))
{
	$id = intval(key($_POST['update']));

	$rank = forum_trim($_POST['rank'][$id]);
	$min_posts = intval($_POST['min_posts'][$id]);

	if ($rank == '')
		message(__('Title message', 'admin_ranks'));

	if ($min_posts < 0)
		message(__('Min posts message', 'admin_ranks'));

	($hook = get_hook('ark_update_form_submitted')) ? eval($hook) : null;

	// Make sure there isn't already a rank with the same min_posts value
	$query = array(
		'SELECT'	=> 'COUNT(r.id)',
		'FROM'		=> 'ranks AS r',
		'WHERE'		=> 'id!='.$id.' AND min_posts='.$min_posts
	);

	($hook = get_hook('ark_update_qr_check_rank_collision')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);

	if (db()->result($result) > 0)
		message(sprintf(__('Min posts occupied message', 'admin_ranks'), $min_posts));

	$query = array(
		'UPDATE'	=> 'ranks',
		'SET'		=> 'rank=\''.db()->escape($rank).'\', min_posts='.$min_posts,
		'WHERE'		=> 'id='.$id
	);

	($hook = get_hook('ark_update_qr_update_rank')) ? eval($hook) : null;
	db()->query_build($query) or error(__FILE__, __LINE__);

	// Regenerate the ranks cache
	require FORUM_ROOT . 'include/cache.php';

	generate_ranks_cache();

	// Add flash message
	flash()->add_info(__('Rank updated', 'admin_ranks'));

	($hook = get_hook('ark_update_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['admin_ranks']), __('Rank updated', 'admin_ranks'));
}


// Remove a rank
else if (isset($_POST['remove']))
{
	$id = intval(key($_POST['remove']));

	($hook = get_hook('ark_remove_form_submitted')) ? eval($hook) : null;

	$query = array(
		'DELETE'	=> 'ranks',
		'WHERE'		=> 'id='.$id
	);

	($hook = get_hook('ark_remove_qr_delete_rank')) ? eval($hook) : null;
	db()->query_build($query) or error(__FILE__, __LINE__);

	// Regenerate the ranks cache
	require FORUM_ROOT . 'include/cache.php';
	generate_ranks_cache();

	// Add flash message
	flash()->add_info(__('Rank removed', 'admin_ranks'));

	($hook = get_hook('ark_remove_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['admin_ranks']), __('Rank removed', 'admin_ranks'));
}


// Load the cached ranks
if (file_exists(FORUM_CACHE_DIR.'cache_ranks.php'))
	include FORUM_CACHE_DIR.'cache_ranks.php';

if (!defined('FORUM_RANKS_LOADED')) {
	require FORUM_ROOT . 'include/cache.php';
	generate_ranks_cache();
	require FORUM_CACHE_DIR . 'cache_ranks.php';
}


// Setup the form
$forum_page['fld_count'] = $forum_page['item_count'] = $forum_page['group_count'] = 0;

// Setup breadcrumbs
$forum_page['crumbs'] = array(
	array(config()->o_board_title, forum_link($forum_url['index'])),
	array(__('Forum administration', 'admin_common'), forum_link($forum_url['admin_index'])),
	array(__('Users', 'admin_common'), forum_link($forum_url['admin_users'])),
	array(__('Ranks', 'admin_common'), forum_link($forum_url['admin_ranks']))
);

($hook = get_hook('ark_pre_header_load')) ? eval($hook) : null;

define('FORUM_PAGE_SECTION', 'users');
define('FORUM_PAGE', 'admin-ranks');

$forum_main_view = 'admin/ranks/main';
template()->render();
