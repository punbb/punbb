<?php
/**
 * Word censor management page.
 *
 * Allows administrators and moderators to add, modify, and delete the word censors used by the software when censoring is enabled.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */
namespace punbb;

require __DIR__ . '/../vendor/pautoload.php';

($hook = get_hook('acs_start')) ? eval($hook) : null;

if (!$forum_user['is_admmod'])
	message(__('No permission'));

// Add a censor word
if (isset($_POST['add_word']))
{
	$search_for = forum_trim($_POST['new_search_for']);
	$replace_with = forum_trim($_POST['new_replace_with']);

	if ($search_for == '' || $replace_with == '')
		message(__('Must enter text message', 'admin_censoring'));

	($hook = get_hook('acs_add_word_form_submitted')) ? eval($hook) : null;

	$query = array(
		'INSERT'	=> 'search_for, replace_with',
		'INTO'		=> 'censoring',
		'VALUES'	=> '\''.db()->escape($search_for).'\', \''.db()->escape($replace_with).'\''
	);

	($hook = get_hook('acs_add_word_qr_add_censor')) ? eval($hook) : null;
	db()->query_build($query) or error(__FILE__, __LINE__);

	// Regenerate the censor cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_censors_cache();

	// Add flash message
	flash()->add_info(__('Censor word added', 'admin_censoring'));

	($hook = get_hook('acs_add_word_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['admin_censoring']), __('Censor word added', 'admin_censoring'));
}


// Update a censor word
else if (isset($_POST['update']))
{
	$id = intval(key($_POST['update']));

	$search_for = forum_trim($_POST['search_for'][$id]);
	$replace_with = forum_trim($_POST['replace_with'][$id]);

	if ($search_for == '' || $replace_with == '')
		message(__('Must enter text message', 'admin_censoring'));

	($hook = get_hook('acs_update_form_submitted')) ? eval($hook) : null;

	$query = array(
		'UPDATE'	=> 'censoring',
		'SET'		=> 'search_for=\''.db()->escape($search_for).'\', replace_with=\''.db()->escape($replace_with).'\'',
		'WHERE'		=> 'id='.$id
	);

	($hook = get_hook('acs_update_qr_update_censor')) ? eval($hook) : null;
	db()->query_build($query) or error(__FILE__, __LINE__);

	// Regenerate the censor cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_censors_cache();

	// Add flash message
	flash()->add_info(__('Censor word updated', 'admin_censoring'));

	($hook = get_hook('acs_update_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['admin_censoring']), __('Censor word updated', 'admin_censoring'));
}


// Remove a censor word
else if (isset($_POST['remove']))
{
	$id = intval(key($_POST['remove']));

	($hook = get_hook('acs_remove_form_submitted')) ? eval($hook) : null;

	$query = array(
		'DELETE'	=> 'censoring',
		'WHERE'		=> 'id='.$id
	);

	($hook = get_hook('acs_remove_qr_delete_censor')) ? eval($hook) : null;
	db()->query_build($query) or error(__FILE__, __LINE__);

	// Regenerate the censor cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_censors_cache();

	// Add flash message
	flash()->add_info(__('Censor word removed', 'admin_censoring'));

	($hook = get_hook('acs_remove_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['admin_censoring']), __('Censor word removed', 'admin_censoring'));
}


// Load the cached censors
if (file_exists(FORUM_CACHE_DIR.'cache_censors.php'))
	include FORUM_CACHE_DIR.'cache_censors.php';

if (!defined('FORUM_CENSORS_LOADED'))
{
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_censors_cache();
	require FORUM_CACHE_DIR.'cache_censors.php';
}


// Setup the form
$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;

// Setup breadcrumbs
$forum_page['crumbs'] = array(
	array($forum_config['o_board_title'], forum_link($forum_url['index'])),
	array(__('Forum administration', 'admin_common'), forum_link($forum_url['admin_index']))
);
if ($forum_user['g_id'] == FORUM_ADMIN)
	$forum_page['crumbs'][] = array(__('Settings', 'admin_common'), forum_link($forum_url['admin_settings_setup']));
$forum_page['crumbs'][] = array(__('Censoring', 'admin_common'), forum_link($forum_url['admin_censoring']));


($hook = get_hook('acs_pre_header_load')) ? eval($hook) : null;

define('FORUM_PAGE_SECTION', 'settings');
define('FORUM_PAGE', 'admin-censoring');

$forum_main_view = 'admin/censoring/main';
include FORUM_ROOT . 'include/render.php';
