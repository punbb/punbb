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


if (!defined('FORUM_ROOT'))
	define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';
require FORUM_ROOT.'include/common_admin.php';

($hook = get_hook('acs_start')) ? eval($hook) : null;

if (!$forum_user['is_admmod'])
	message($lang_common['No permission']);

// Load the admin.php language file
require FORUM_ROOT.'lang/'.$forum_user['language'].'/admin_common.php';
require FORUM_ROOT.'lang/'.$forum_user['language'].'/admin_censoring.php';


// Add a censor word
if (isset($_POST['add_word']))
{
	$search_for = forum_trim($_POST['new_search_for']);
	$replace_with = forum_trim($_POST['new_replace_with']);

	if ($search_for == '' || $replace_with == '')
		message($lang_admin_censoring['Must enter text message']);

	($hook = get_hook('acs_add_word_form_submitted')) ? eval($hook) : null;

	$query = array(
		'INSERT'	=> 'search_for, replace_with',
		'INTO'		=> 'censoring',
		'VALUES'	=> '\''.$forum_db->escape($search_for).'\', \''.$forum_db->escape($replace_with).'\''
	);

	($hook = get_hook('acs_add_word_qr_add_censor')) ? eval($hook) : null;
	$forum_db->query_build($query) or error(__FILE__, __LINE__);

	// Regenerate the censor cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_censors_cache();

	// Add flash message
	$forum_flash->add_info($lang_admin_censoring['Censor word added']);

	($hook = get_hook('acs_add_word_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['admin_censoring']), $lang_admin_censoring['Censor word added']);
}


// Update a censor word
else if (isset($_POST['update']))
{
	$id = intval(key($_POST['update']));

	$search_for = forum_trim($_POST['search_for'][$id]);
	$replace_with = forum_trim($_POST['replace_with'][$id]);

	if ($search_for == '' || $replace_with == '')
		message($lang_admin_censoring['Must enter text message']);

	($hook = get_hook('acs_update_form_submitted')) ? eval($hook) : null;

	$query = array(
		'UPDATE'	=> 'censoring',
		'SET'		=> 'search_for=\''.$forum_db->escape($search_for).'\', replace_with=\''.$forum_db->escape($replace_with).'\'',
		'WHERE'		=> 'id='.$id
	);

	($hook = get_hook('acs_update_qr_update_censor')) ? eval($hook) : null;
	$forum_db->query_build($query) or error(__FILE__, __LINE__);

	// Regenerate the censor cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_censors_cache();

	// Add flash message
	$forum_flash->add_info($lang_admin_censoring['Censor word updated']);

	($hook = get_hook('acs_update_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['admin_censoring']), $lang_admin_censoring['Censor word updated']);
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
	$forum_db->query_build($query) or error(__FILE__, __LINE__);

	// Regenerate the censor cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_censors_cache();

	// Add flash message
	$forum_flash->add_info($lang_admin_censoring['Censor word removed']);

	($hook = get_hook('acs_remove_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['admin_censoring']), $lang_admin_censoring['Censor word removed']);
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
	array($lang_admin_common['Forum administration'], forum_link($forum_url['admin_index']))
);
if ($forum_user['g_id'] == FORUM_ADMIN)
	$forum_page['crumbs'][] = array($lang_admin_common['Settings'], forum_link($forum_url['admin_settings_setup']));
$forum_page['crumbs'][] = array($lang_admin_common['Censoring'], forum_link($forum_url['admin_censoring']));


($hook = get_hook('acs_pre_header_load')) ? eval($hook) : null;

define('FORUM_PAGE_SECTION', 'settings');
define('FORUM_PAGE', 'admin-censoring');
require FORUM_ROOT.'header.php';

// START SUBST - <!-- forum_main -->
ob_start();
include FORUM_ROOT . 'include/view/admin/censoring/main.php';
$view_forum_main = forum_trim(ob_get_contents());
$tpl_main = str_replace('<!-- forum_main -->', $view_forum_main, $tpl_main);
ob_end_clean();
// END SUBST - <!-- forum_main -->

require FORUM_ROOT.'footer.php';
