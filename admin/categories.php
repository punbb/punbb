<?php
/**
 * Category management page.
 *
 * Allows administrators to create, reposition, and remove categories.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */


if (!defined('FORUM_ROOT'))
	define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';
require FORUM_ROOT.'include/common_admin.php';

($hook = get_hook('acg_start')) ? eval($hook) : null;

if ($forum_user['g_id'] != FORUM_ADMIN)
	message($lang_common['No permission']);

// Load the admin.php language file
require FORUM_ROOT.'lang/'.$forum_user['language'].'/admin_common.php';
require FORUM_ROOT.'lang/'.$forum_user['language'].'/admin_categories.php';


// Add a new category
if (isset($_POST['add_cat']))
{
	$new_cat_name = forum_trim($_POST['new_cat_name']);
	if ($new_cat_name == '')
		message($lang_admin_categories['Must name category']);

	$new_cat_pos = intval($_POST['position']);

	($hook = get_hook('acg_add_cat_form_submitted')) ? eval($hook) : null;

	$query = array(
		'INSERT'	=> 'cat_name, disp_position',
		'INTO'		=> 'categories',
		'VALUES'	=> '\''.$forum_db->escape($new_cat_name).'\', '.$new_cat_pos
	);

	($hook = get_hook('acg_add_cat_qr_add_category')) ? eval($hook) : null;
	$forum_db->query_build($query) or error(__FILE__, __LINE__);

	// Add flash message
	$forum_flash->add_info($lang_admin_categories['Category added']);

	($hook = get_hook('acg_add_cat_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['admin_categories']), $lang_admin_categories['Category added']);
}


// Delete a category
else if (isset($_POST['del_cat']) || isset($_POST['del_cat_comply']))
{
	$cat_to_delete = intval($_POST['cat_to_delete']);
	if ($cat_to_delete < 1)
		message($lang_common['Bad request']);

	// User pressed the cancel button
	if (isset($_POST['del_cat_cancel']))
		redirect(forum_link($forum_url['admin_categories']), $lang_admin_common['Cancel redirect']);

	($hook = get_hook('acg_del_cat_form_submitted')) ? eval($hook) : null;

	if (isset($_POST['del_cat_comply']))	// Delete a category with all forums and posts
	{
		@set_time_limit(0);

		$query = array(
			'SELECT'	=> 'f.id',
			'FROM'		=> 'forums AS f',
			'WHERE'		=> 'cat_id='.$cat_to_delete
		);

		($hook = get_hook('acg_del_cat_qr_get_forums_to_delete')) ? eval($hook) : null;
		$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

		$forum_ids = array();
		while ($cur_forum_id = $forum_db->fetch_assoc($result)) {
			$forum_ids[] = $cur_forum_id['id'];
		}

		if (!empty($forum_ids))
		{
			foreach ($forum_ids as $cur_forum)
			{
				// Prune all posts and topics
				prune($cur_forum, 1, -1);

				// Delete the forum
				$query = array(
					'DELETE'	=> 'forums',
					'WHERE'		=> 'id='.$cur_forum
				);

				($hook = get_hook('acg_del_cat_qr_delete_forum')) ? eval($hook) : null;
				$forum_db->query_build($query) or error(__FILE__, __LINE__);

				// Delete any forum subscriptions
				$query = array(
					'DELETE'	=> 'forum_subscriptions',
					'WHERE'		=> 'forum_id='.$cur_forum
				);

				($hook = get_hook('acg_del_cat_qr_delete_forum_subscriptions')) ? eval($hook) : null;
				$forum_db->query_build($query) or error(__FILE__, __LINE__);
			}
		}

		delete_orphans();

		// Delete the category
		$query = array(
			'DELETE'	=> 'categories',
			'WHERE'		=> 'id='.$cat_to_delete
		);

		($hook = get_hook('acg_del_cat_qr_delete_category')) ? eval($hook) : null;
		$forum_db->query_build($query) or error(__FILE__, __LINE__);

		// Regenerate the quickjump cache
		if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
			require FORUM_ROOT.'include/cache.php';

		generate_quickjump_cache();

		// Add flash message
		$forum_flash->add_info($lang_admin_categories['Category deleted']);

		($hook = get_hook('acg_del_cat_pre_redirect')) ? eval($hook) : null;

		redirect(forum_link($forum_url['admin_categories']), $lang_admin_categories['Category deleted']);
	}
	else	// If the user hasn't comfirmed the delete
	{
		$query = array(
			'SELECT'	=> 'c.cat_name',
			'FROM'		=> 'categories AS c',
			'WHERE'		=> 'c.id='.$cat_to_delete
		);

		($hook = get_hook('acg_del_cat_qr_get_category_name')) ? eval($hook) : null;
		$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
		$cat_name = $forum_db->result($result);

		if (is_null($cat_name) || $cat_name === false)
			message($lang_common['Bad request']);


		// Setup the form
		$forum_page['form_action'] = forum_link($forum_url['admin_categories']);

		$forum_page['hidden_fields'] = array(
			'csrf_token'	=> '<input type="hidden" name="csrf_token" value="'.generate_form_token($forum_page['form_action']).'" />',
			'cat_to_delete'	=> '<input type="hidden" name="cat_to_delete" value="'.$cat_to_delete.'" />'
		);

		// Setup breadcrumbs
		$forum_page['crumbs'] = array(
			array($forum_config['o_board_title'], forum_link($forum_url['index'])),
			array($lang_admin_common['Forum administration'], forum_link($forum_url['admin_index'])),
			array($lang_admin_common['Start'], forum_link($forum_url['admin_index'])),
			array($lang_admin_common['Categories'], forum_link($forum_url['admin_categories'])),
			$lang_admin_categories['Delete category']
		);

		($hook = get_hook('acg_del_cat_pre_header_load')) ? eval($hook) : null;

		define('FORUM_PAGE_SECTION', 'start');
		define('FORUM_PAGE', 'admin-categories');
		require FORUM_ROOT.'header.php';

		// START SUBST - <!-- forum_main -->
		ob_start();
		include FORUM_ROOT . 'include/view/admin/categories/edit.php';
		$view_forum_main = forum_trim(ob_get_contents());
		$tpl_main = str_replace('<!-- forum_main -->', $view_forum_main, $tpl_main);
		ob_end_clean();
		// END SUBST - <!-- forum_main -->

		require FORUM_ROOT.'footer.php';
	}
}


else if (isset($_POST['update']))	// Change position and name of the categories
{
	$cat_order = array_map('intval', $_POST['cat_order']);
	$cat_name = array_map('trim', $_POST['cat_name']);

	($hook = get_hook('acg_update_cats_form_submitted')) ? eval($hook) : null;

	$query = array(
		'SELECT'	=> 'c.id, c.cat_name, c.disp_position',
		'FROM'		=> 'categories AS c',
		'ORDER BY'	=> 'c.id'
	);

	($hook = get_hook('acg_update_cats_qr_get_categories')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	while ($cur_cat = $forum_db->fetch_assoc($result))
	{
		// If these aren't set, we're looking at a category that was added after
		// the admin started editing: we don't want to mess with it
		if (isset($cat_name[$cur_cat['id']]) && isset($cat_order[$cur_cat['id']]))
		{
			if ($cat_name[$cur_cat['id']] == '')
				message($lang_admin_categories['Must name category']);

			if ($cat_order[$cur_cat['id']] < 0)
				message($lang_admin_categories['Must be integer']);

			// We only want to update if we changed anything
			if ($cur_cat['cat_name'] != $cat_name[$cur_cat['id']] || $cur_cat['disp_position'] != $cat_order[$cur_cat['id']])
			{
				$query = array(
					'UPDATE'	=> 'categories',
					'SET'		=> 'cat_name=\''.$forum_db->escape($cat_name[$cur_cat['id']]).'\', disp_position='.$cat_order[$cur_cat['id']],
					'WHERE'		=> 'id='.$cur_cat['id']
				);

				($hook = get_hook('acg_update_cats_qr_update_category')) ? eval($hook) : null;
				$forum_db->query_build($query) or error(__FILE__, __LINE__);
			}
		}
	}

	// Regenerate the quickjump cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_quickjump_cache();

	// Add flash message
	$forum_flash->add_info($lang_admin_categories['Categories updated']);

	($hook = get_hook('acg_update_cats_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['admin_categories']), $lang_admin_categories['Categories updated']);
}


// Generate an array with all categories
$query = array(
	'SELECT'	=> 'c.id, c.cat_name, c.disp_position',
	'FROM'		=> 'categories AS c',
	'ORDER BY'	=> 'c.disp_position'
);

($hook = get_hook('acg_qr_get_categories')) ? eval($hook) : null;
$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

$cat_list = array();
while ($cur_cat = $forum_db->fetch_assoc($result))
{
	$cat_list[] = $cur_cat;
}

// Setup the form
$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;
$forum_page['form_action'] = forum_link($forum_url['admin_categories']).'?action=foo';

$forum_page['hidden_fields'] = array(
	'csrf_token'	=> '<input type="hidden" name="csrf_token" value="'.generate_form_token($forum_page['form_action']).'" />'
);

// Setup breadcrumbs
$forum_page['crumbs'] = array(
	array($forum_config['o_board_title'], forum_link($forum_url['index'])),
	array($lang_admin_common['Forum administration'], forum_link($forum_url['admin_index'])),
	array($lang_admin_common['Start'], forum_link($forum_url['admin_index'])),
	array($lang_admin_common['Categories'], forum_link($forum_url['admin_categories']))
);

($hook = get_hook('acg_pre_header_load')) ? eval($hook) : null;

define('FORUM_PAGE_SECTION', 'start');
define('FORUM_PAGE', 'admin-categories');
require FORUM_ROOT.'header.php';

// START SUBST - <!-- forum_main -->
ob_start();
include FORUM_ROOT . 'include/view/admin/categories/main.php';
$view_forum_main = forum_trim(ob_get_contents());
$tpl_main = str_replace('<!-- forum_main -->', $view_forum_main, $tpl_main);
ob_end_clean();
// END SUBST - <!-- forum_main -->

require FORUM_ROOT.'footer.php';
