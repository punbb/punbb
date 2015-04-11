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
namespace punbb;

require __DIR__ . '/../vendor/pautoload.php';

($hook = get_hook('acg_start')) ? eval($hook) : null;

if (user()['g_id'] != FORUM_ADMIN)
	message(__('No permission'));

// Add a new category
if (isset($_POST['add_cat']))
{
	$new_cat_name = forum_trim($_POST['new_cat_name']);
	if ($new_cat_name == '')
		message(__('Must name category', 'admin_categories'));

	$new_cat_pos = intval($_POST['position']);

	($hook = get_hook('acg_add_cat_form_submitted')) ? eval($hook) : null;

	$query = array(
		'INSERT'	=> 'cat_name, disp_position',
		'INTO'		=> 'categories',
		'VALUES'	=> '\''.db()->escape($new_cat_name).'\', '.$new_cat_pos
	);

	($hook = get_hook('acg_add_cat_qr_add_category')) ? eval($hook) : null;
	db()->query_build($query) or error(__FILE__, __LINE__);

	// Add flash message
	flash()->add_info(__('Category added', 'admin_categories'));

	($hook = get_hook('acg_add_cat_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['admin_categories']), __('Category added', 'admin_categories'));
}


// Delete a category
else if (isset($_POST['del_cat']) || isset($_POST['del_cat_comply']))
{
	$cat_to_delete = intval($_POST['cat_to_delete']);
	if ($cat_to_delete < 1)
		message(__('Bad request'));

	// User pressed the cancel button
	if (isset($_POST['del_cat_cancel']))
		redirect(forum_link($forum_url['admin_categories']), __('Cancel redirect', 'admin_common'));

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
		$result = db()->query_build($query) or error(__FILE__, __LINE__);

		$forum_ids = array();
		while ($cur_forum_id = db()->fetch_assoc($result)) {
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
				db()->query_build($query) or error(__FILE__, __LINE__);

				// Delete any forum subscriptions
				$query = array(
					'DELETE'	=> 'forum_subscriptions',
					'WHERE'		=> 'forum_id='.$cur_forum
				);

				($hook = get_hook('acg_del_cat_qr_delete_forum_subscriptions')) ? eval($hook) : null;
				db()->query_build($query) or error(__FILE__, __LINE__);
			}
		}

		delete_orphans();

		// Delete the category
		$query = array(
			'DELETE'	=> 'categories',
			'WHERE'		=> 'id='.$cat_to_delete
		);

		($hook = get_hook('acg_del_cat_qr_delete_category')) ? eval($hook) : null;
		db()->query_build($query) or error(__FILE__, __LINE__);

		// Regenerate the quickjump cache
		require FORUM_ROOT . 'include/cache.php';
		generate_quickjump_cache();

		// Add flash message
		flash()->add_info(__('Category deleted', 'admin_categories'));

		($hook = get_hook('acg_del_cat_pre_redirect')) ? eval($hook) : null;

		redirect(forum_link($forum_url['admin_categories']), __('Category deleted', 'admin_categories'));
	}
	else	// If the user hasn't comfirmed the delete
	{
		$query = array(
			'SELECT'	=> 'c.cat_name',
			'FROM'		=> 'categories AS c',
			'WHERE'		=> 'c.id='.$cat_to_delete
		);

		($hook = get_hook('acg_del_cat_qr_get_category_name')) ? eval($hook) : null;
		$result = db()->query_build($query) or error(__FILE__, __LINE__);
		$cat_name = db()->result($result);

		if (is_null($cat_name) || $cat_name === false)
			message(__('Bad request'));


		// Setup the form
		$forum_page['form_action'] = forum_link($forum_url['admin_categories']);

		$forum_page['hidden_fields'] = array(
			'csrf_token'	=> '<input type="hidden" name="csrf_token" value="'.generate_form_token($forum_page['form_action']).'" />',
			'cat_to_delete'	=> '<input type="hidden" name="cat_to_delete" value="'.$cat_to_delete.'" />'
		);

		// Setup breadcrumbs
		$forum_page['crumbs'] = array(
			array(config()['o_board_title'], forum_link($forum_url['index'])),
			array(__('Forum administration', 'admin_common'), forum_link($forum_url['admin_index'])),
			array(__('Start', 'admin_common'), forum_link($forum_url['admin_index'])),
			array(__('Categories', 'admin_common'), forum_link($forum_url['admin_categories'])),
			__('Delete category', 'admin_categories')
		);

		($hook = get_hook('acg_del_cat_pre_header_load')) ? eval($hook) : null;

		define('FORUM_PAGE_SECTION', 'start');
		define('FORUM_PAGE', 'admin-categories');

		$forum_main_view = 'admin/categories/edit';
		include FORUM_ROOT . 'include/render.php';
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
	$result = db()->query_build($query) or error(__FILE__, __LINE__);
	while ($cur_cat = db()->fetch_assoc($result))
	{
		// If these aren't set, we're looking at a category that was added after
		// the admin started editing: we don't want to mess with it
		if (isset($cat_name[$cur_cat['id']]) && isset($cat_order[$cur_cat['id']]))
		{
			if ($cat_name[$cur_cat['id']] == '')
				message(__('Must name category', 'admin_categories'));

			if ($cat_order[$cur_cat['id']] < 0)
				message(__('Must be integer', 'admin_categories'));

			// We only want to update if we changed anything
			if ($cur_cat['cat_name'] != $cat_name[$cur_cat['id']] || $cur_cat['disp_position'] != $cat_order[$cur_cat['id']])
			{
				$query = array(
					'UPDATE'	=> 'categories',
					'SET'		=> 'cat_name=\''.db()->escape($cat_name[$cur_cat['id']]).'\', disp_position='.$cat_order[$cur_cat['id']],
					'WHERE'		=> 'id='.$cur_cat['id']
				);

				($hook = get_hook('acg_update_cats_qr_update_category')) ? eval($hook) : null;
				db()->query_build($query) or error(__FILE__, __LINE__);
			}
		}
	}

	// Regenerate the quickjump cache
	require FORUM_ROOT.'include/cache.php';
	generate_quickjump_cache();

	// Add flash message
	flash()->add_info(__('Categories updated', 'admin_categories'));

	($hook = get_hook('acg_update_cats_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['admin_categories']), __('Categories updated', 'admin_categories'));
}


// Generate an array with all categories
$query = array(
	'SELECT'	=> 'c.id, c.cat_name, c.disp_position',
	'FROM'		=> 'categories AS c',
	'ORDER BY'	=> 'c.disp_position'
);

($hook = get_hook('acg_qr_get_categories')) ? eval($hook) : null;
$result = db()->query_build($query) or error(__FILE__, __LINE__);

$cat_list = array();
while ($cur_cat = db()->fetch_assoc($result))
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
	array(config()['o_board_title'], forum_link($forum_url['index'])),
	array(__('Forum administration', 'admin_common'), forum_link($forum_url['admin_index'])),
	array(__('Start', 'admin_common'), forum_link($forum_url['admin_index'])),
	array(__('Categories', 'admin_common'), forum_link($forum_url['admin_categories']))
);

($hook = get_hook('acg_pre_header_load')) ? eval($hook) : null;

define('FORUM_PAGE_SECTION', 'start');
define('FORUM_PAGE', 'admin-categories');

$forum_main_view = 'admin/categories/main';
include FORUM_ROOT . 'include/render.php';
