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


if (!defined('FORUM_ROOT'))
	define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';
require FORUM_ROOT.'include/common_admin.php';

($hook = get_hook('ark_start')) ? eval($hook) : null;

if ($forum_user['g_id'] != FORUM_ADMIN)
	message($lang_common['No permission']);

// Load the admin.php language file
require FORUM_ROOT.'lang/'.$forum_user['language'].'/admin_common.php';
require FORUM_ROOT.'lang/'.$forum_user['language'].'/admin_ranks.php';


// Add a rank
if (isset($_POST['add_rank']))
{
	$rank = forum_trim($_POST['new_rank']);
	$min_posts = intval($_POST['new_min_posts']);

	if ($rank == '')
		message($lang_admin_ranks['Title message']);

	if ($min_posts < 0)
		message($lang_admin_ranks['Min posts message']);

	($hook = get_hook('ark_add_rank_form_submitted')) ? eval($hook) : null;

	// Make sure there isn't already a rank with the same min_posts value
	$query = array(
		'SELECT'	=> 'COUNT(r.id)',
		'FROM'		=> 'ranks AS r',
		'WHERE'		=> 'min_posts='.$min_posts
	);

	($hook = get_hook('ark_add_rank_qr_check_rank_collision')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

	if ($forum_db->result($result) > 0)
		message(sprintf($lang_admin_ranks['Min posts occupied message'], $min_posts));

	$query = array(
		'INSERT'	=> 'rank, min_posts',
		'INTO'		=> 'ranks',
		'VALUES'	=> '\''.$forum_db->escape($rank).'\', '.$min_posts
	);

	($hook = get_hook('ark_add_rank_qr_add_rank')) ? eval($hook) : null;
	$forum_db->query_build($query) or error(__FILE__, __LINE__);

	// Regenerate the ranks cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_ranks_cache();

	// Add flash message
	$forum_flash->add_info($lang_admin_ranks['Rank added']);

	($hook = get_hook('ark_add_rank_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['admin_ranks']), $lang_admin_ranks['Rank added']);
}


// Update a rank
else if (isset($_POST['update']))
{
	$id = intval(key($_POST['update']));

	$rank = forum_trim($_POST['rank'][$id]);
	$min_posts = intval($_POST['min_posts'][$id]);

	if ($rank == '')
		message($lang_admin_ranks['Title message']);

	if ($min_posts < 0)
		message($lang_admin_ranks['Min posts message']);

	($hook = get_hook('ark_update_form_submitted')) ? eval($hook) : null;

	// Make sure there isn't already a rank with the same min_posts value
	$query = array(
		'SELECT'	=> 'COUNT(r.id)',
		'FROM'		=> 'ranks AS r',
		'WHERE'		=> 'id!='.$id.' AND min_posts='.$min_posts
	);

	($hook = get_hook('ark_update_qr_check_rank_collision')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

	if ($forum_db->result($result) > 0)
		message(sprintf($lang_admin_ranks['Min posts occupied message'], $min_posts));

	$query = array(
		'UPDATE'	=> 'ranks',
		'SET'		=> 'rank=\''.$forum_db->escape($rank).'\', min_posts='.$min_posts,
		'WHERE'		=> 'id='.$id
	);

	($hook = get_hook('ark_update_qr_update_rank')) ? eval($hook) : null;
	$forum_db->query_build($query) or error(__FILE__, __LINE__);

	// Regenerate the ranks cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_ranks_cache();

	// Add flash message
	$forum_flash->add_info($lang_admin_ranks['Rank updated']);

	($hook = get_hook('ark_update_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['admin_ranks']), $lang_admin_ranks['Rank updated']);
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
	$forum_db->query_build($query) or error(__FILE__, __LINE__);

	// Regenerate the ranks cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_ranks_cache();

	// Add flash message
	$forum_flash->add_info($lang_admin_ranks['Rank removed']);

	($hook = get_hook('ark_remove_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['admin_ranks']), $lang_admin_ranks['Rank removed']);
}


// Load the cached ranks
if (file_exists(FORUM_CACHE_DIR.'cache_ranks.php'))
	include FORUM_CACHE_DIR.'cache_ranks.php';

if (!defined('FORUM_RANKS_LOADED'))
{
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_ranks_cache();
	require FORUM_CACHE_DIR.'cache_ranks.php';
}


// Setup the form
$forum_page['fld_count'] = $forum_page['item_count'] = $forum_page['group_count'] = 0;

// Setup breadcrumbs
$forum_page['crumbs'] = array(
	array($forum_config['o_board_title'], forum_link($forum_url['index'])),
	array($lang_admin_common['Forum administration'], forum_link($forum_url['admin_index'])),
	array($lang_admin_common['Users'], forum_link($forum_url['admin_users'])),
	array($lang_admin_common['Ranks'], forum_link($forum_url['admin_ranks']))
);

($hook = get_hook('ark_pre_header_load')) ? eval($hook) : null;

define('FORUM_PAGE_SECTION', 'users');
define('FORUM_PAGE', 'admin-ranks');

$forum_main_view = 'admin/ranks/main';
include FORUM_ROOT . 'include/render.php';
