<?php
/**
 * Forum management page.
 *
 * Allows administrators to add, modify, and remove forums.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */
namespace punbb;

require __DIR__ . '/../vendor/pautoload.php';

($hook = get_hook('afo_start')) ? eval($hook) : null;

if (user()['g_id'] != FORUM_ADMIN)
	message(__('No permission'));

// Add a "default" forum
if (isset($_POST['add_forum']))
{
	$add_to_cat = isset($_POST['add_to_cat']) ? intval($_POST['add_to_cat']) : 0;
	if ($add_to_cat < 1)
		message(__('Bad request'));

	$forum_name = forum_trim($_POST['forum_name']);
	$position = intval($_POST['position']);

	($hook = get_hook('afo_add_forum_form_submitted')) ? eval($hook) : null;

	if ($forum_name == '')
		message(__('Must enter forum message', 'admin_forums'));

	// Make sure the category we're adding to exists
	$query = array(
		'SELECT'	=> 'COUNT(c.id)',
		'FROM'		=> 'categories AS c',
		'WHERE'		=> 'c.id='.$add_to_cat
	);

	($hook = get_hook('afo_add_forum_qr_validate_category_id')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);

	if (db()->result($result) != 1)
		message(__('Bad request'));


	$query = array(
		'INSERT'	=> 'forum_name, disp_position, cat_id',
		'INTO'		=> 'forums',
		'VALUES'	=> '\''.db()->escape($forum_name).'\', '.$position.', '.$add_to_cat
	);

	($hook = get_hook('afo_add_forum_qr_add_forum')) ? eval($hook) : null;
	db()->query_build($query) or error(__FILE__, __LINE__);

	// Regenerate the quickjump cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_quickjump_cache();

	// Add flash message
	flash()->add_info(__('Forum added', 'admin_forums'));

	($hook = get_hook('afo_add_forum_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['admin_forums']), __('Forum added', 'admin_forums'));
}


// Delete a forum
else if (isset($_GET['del_forum']))
{
	$forum_to_delete = intval($_GET['del_forum']);
	if ($forum_to_delete < 1)
		message(__('Bad request'));

	// User pressed the cancel button
	if (isset($_POST['del_forum_cancel']))
		redirect(forum_link($forum_url['admin_forums']), __('Cancel redirect', 'admin_common'));

	($hook = get_hook('afo_del_forum_form_submitted')) ? eval($hook) : null;

	if (isset($_POST['del_forum_comply']))	// Delete a forum with all posts
	{
		@set_time_limit(0);

		// Prune all posts and topics
		prune($forum_to_delete, 1, -1);

		delete_orphans();

		// Delete the forum and any forum specific group permissions
		$query = array(
			'DELETE'	=> 'forums',
			'WHERE'		=> 'id='.$forum_to_delete
		);

		($hook = get_hook('afo_del_forum_qr_delete_forum')) ? eval($hook) : null;
		db()->query_build($query) or error(__FILE__, __LINE__);

		$query = array(
			'DELETE'	=> 'forum_perms',
			'WHERE'		=> 'forum_id='.$forum_to_delete
		);

		($hook = get_hook('afo_del_forum_qr_delete_forum_perms')) ? eval($hook) : null;
		db()->query_build($query) or error(__FILE__, __LINE__);

		// Delete forum subscriptions
		$query = array(
			'DELETE'	=> 'forum_subscriptions',
			'WHERE'		=> 'forum_id='.$forum_to_delete
		);

		($hook = get_hook('afo_del_forum_qr_delete_forum_subscriptions')) ? eval($hook) : null;
		db()->query_build($query) or error(__FILE__, __LINE__);

		// Regenerate the quickjump cache
		if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
			require FORUM_ROOT.'include/cache.php';

		generate_quickjump_cache();

		// Add flash message
		flash()->add_info(__('Forum deleted', 'admin_forums'));

		($hook = get_hook('afo_del_forum_pre_redirect')) ? eval($hook) : null;

		redirect(forum_link($forum_url['admin_forums']), __('Forum deleted', 'admin_forums'));
	}
	else	// If the user hasn't confirmed the delete
	{
		$query = array(
			'SELECT'	=> 'f.forum_name',
			'FROM'		=> 'forums AS f',
			'WHERE'		=> 'f.id='.$forum_to_delete
		);

		($hook = get_hook('afo_del_forum_qr_get_forum_name')) ? eval($hook) : null;
		$result = db()->query_build($query) or error(__FILE__, __LINE__);
		$forum_name = db()->result($result);

		if (is_null($forum_name) || $forum_name === false)
			message(__('Bad request'));


		// Setup breadcrumbs
		$forum_page['crumbs'] = array(
			array(config()['o_board_title'], forum_link($forum_url['index'])),
			array(__('Forum administration', 'admin_common'), forum_link($forum_url['admin_index'])),
			array(__('Start', 'admin_common'), forum_link($forum_url['admin_index'])),
			array(__('Forums', 'admin_common'), forum_link($forum_url['admin_forums'])),
			__('Delete forum', 'admin_forums')
		);

		($hook = get_hook('afo_del_forum_pre_header_load')) ? eval($hook) : null;

		define('FORUM_PAGE_SECTION', 'start');
		define('FORUM_PAGE', 'admin-forums');

		$forum_main_view = 'admin/forums/delete';
		include FORUM_ROOT . 'include/render.php';
	}
}


// Update forum positions
else if (isset($_POST['update_positions']))
{
	$positions = array_map('intval', $_POST['position']);

	($hook = get_hook('afo_update_positions_form_submitted')) ? eval($hook) : null;

	$query = array(
		'SELECT'	=> 'f.id, f.disp_position',
		'FROM'		=> 'categories AS c',
		'JOINS'		=> array(
			array(
				'INNER JOIN'	=> 'forums AS f',
				'ON'			=> 'c.id=f.cat_id'
			)
		),
		'ORDER BY'	=> 'c.disp_position, c.id, f.disp_position'
	);

	($hook = get_hook('afo_update_positions_qr_get_forums')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);
	while ($cur_forum = db()->fetch_assoc($result))
	{
		// If these aren't set, we're looking at a forum that was added after
		// the admin started editing: we don't want to mess with it
		if (isset($positions[$cur_forum['id']]))
		{
			$new_disp_position = $positions[$cur_forum['id']];

			if ($new_disp_position < 0)
				message(__('Must be integer', 'admin_forums'));

			// We only want to update if we changed the position
			if ($cur_forum['disp_position'] != $new_disp_position)
			{
				$query = array(
					'UPDATE'	=> 'forums',
					'SET'		=> 'disp_position='.$new_disp_position,
					'WHERE'		=> 'id='.$cur_forum['id']
				);

				($hook = get_hook('afo_update_positions_qr_update_forum_position')) ? eval($hook) : null;
				db()->query_build($query) or error(__FILE__, __LINE__);
			}
		}
	}

	// Regenerate the quickjump cache
	require_once FORUM_ROOT.'include/cache.php';
	generate_quickjump_cache();

	// Add flash message
	flash()->add_info(__('Forums updated', 'admin_forums'));

	($hook = get_hook('afo_update_positions_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['admin_forums']), __('Forums updated', 'admin_forums'));
}


else if (isset($_GET['edit_forum']))
{
	$forum_id = intval($_GET['edit_forum']);
	if ($forum_id < 1)
		message(__('Bad request'));

	($hook = get_hook('afo_edit_forum_selected')) ? eval($hook) : null;

	// Fetch forum info
	$query = array(
		'SELECT'	=> 'f.id, f.forum_name, f.forum_desc, f.redirect_url, f.num_topics, f.sort_by, f.cat_id',
		'FROM'		=> 'forums AS f',
		'WHERE'		=> 'f.id='.$forum_id
	);

	($hook = get_hook('afo_edit_forum_qr_get_forum_details')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);
	$cur_forum = db()->fetch_assoc($result);

	if (is_null($cur_forum) || $cur_forum === false)
		message(__('Bad request'));


	// Update group permissions for $forum_id
	if (isset($_POST['save']))
	{
		// Start with the forum details
		$forum_name = forum_trim($_POST['forum_name']);
		$forum_desc = forum_linebreaks(forum_trim($_POST['forum_desc']));
		$cat_id = intval($_POST['cat_id']);
		$sort_by = intval($_POST['sort_by']);
		$redirect_url = isset($_POST['redirect_url']) && $cur_forum['num_topics'] == 0 ? forum_trim($_POST['redirect_url']) : null;

		($hook = get_hook('afo_save_forum_form_submitted')) ? eval($hook) : null;

		if ($forum_name == '')
			message(__('Must enter forum message', 'admin_forums'));

		if ($cat_id < 1)
			message(__('Bad request'));

		$forum_desc = ($forum_desc != '') ? '\''.db()->escape($forum_desc).'\'' : 'NULL';
		$redirect_url = ($redirect_url != '') ? '\''.db()->escape($redirect_url).'\'' : 'NULL';

		$query = array(
			'UPDATE'	=> 'forums',
			'SET'		=> 'forum_name=\''.db()->escape($forum_name).'\', forum_desc='.$forum_desc.', redirect_url='.$redirect_url.', sort_by='.$sort_by.', cat_id='.$cat_id,
			'WHERE'		=> 'id='.$forum_id
		);

		($hook = get_hook('afo_save_forum_qr_update_forum')) ? eval($hook) : null;
		db()->query_build($query) or error(__FILE__, __LINE__);

		// Now let's deal with the permissions
		if (isset($_POST['read_forum_old']))
		{
			$query = array(
				'SELECT'	=> 'g.g_id, g.g_read_board, g.g_post_replies, g.g_post_topics',
				'FROM'		=> 'groups AS g',
				'WHERE'		=> 'g_id!='.FORUM_ADMIN
			);

			($hook = get_hook('afo_save_forum_qr_get_groups')) ? eval($hook) : null;
			$result = db()->query_build($query) or error(__FILE__, __LINE__);
			while ($cur_group = db()->fetch_assoc($result))
			{
				// The default permissions for this group
				$perms_default = array(
					'read_forum'	=>	$cur_group['g_read_board'],
					'post_replies'	=>	$cur_group['g_post_replies'],
					'post_topics'	=>	$cur_group['g_post_topics']
				);

				// The old permissions for this group
				$perms_old = array(
					'read_forum'	=>	$_POST['read_forum_old'][$cur_group['g_id']],
					'post_replies'	=>	$_POST['post_replies_old'][$cur_group['g_id']],
					'post_topics'	=>	$_POST['post_topics_old'][$cur_group['g_id']]
				);

				// The new permissions for this group
				$perms_new = array(
					'read_forum'	=>	($cur_group['g_read_board'] == '1') ? isset($_POST['read_forum_new'][$cur_group['g_id']]) ? '1' : '0' : intval($_POST['read_forum_old'][$cur_group['g_id']]),
					'post_replies'	=>	isset($_POST['post_replies_new'][$cur_group['g_id']]) ? '1' : '0',
					'post_topics'	=>	isset($_POST['post_topics_new'][$cur_group['g_id']]) ? '1' : '0'
				);

				($hook = get_hook('afo_save_forum_pre_perms_compare')) ? eval($hook) : null;

				// Force all permissions values to integers
				$perms_default = array_map('intval', $perms_default);
				$perms_old = array_map('intval', $perms_old);
				$perms_new = array_map('intval', $perms_new);

				// Check if the new permissions differ from the old
				if ($perms_new !== $perms_old)
				{
					// If the new permissions are identical to the default permissions for this group, delete its row in forum_perms
					if ($perms_new === $perms_default)
					{
						$query = array(
							'DELETE'	=> 'forum_perms',
							'WHERE'		=> 'group_id='.$cur_group['g_id'].' AND forum_id='.$forum_id
						);

						($hook = get_hook('afo_save_forum_qr_delete_group_forum_perms')) ? eval($hook) : null;
						db()->query_build($query) or error(__FILE__, __LINE__);
					}
					else
					{
						// Run an UPDATE and see if it affected a row, if not, INSERT
						$query = array(
							'UPDATE'	=> 'forum_perms',
							'WHERE'		=> 'group_id='.$cur_group['g_id'].' AND forum_id='.$forum_id
						);

						$perms_new_values = array();
						foreach ($perms_new as $key => $value)
							$perms_new_values[] = $key.'='.$value;

						$query['SET'] = implode(', ', $perms_new_values);

						($hook = get_hook('afo_save_forum_qr_update_forum_perms')) ? eval($hook) : null;
						db()->query_build($query) or error(__FILE__, __LINE__);
						if (!db()->affected_rows())
						{
							$query = array(
								'INSERT'	=> 'group_id, forum_id',
								'INTO'		=> 'forum_perms',
								'VALUES'	=> $cur_group['g_id'].', '.$forum_id
							);

							$query['INSERT'] .= ', '.implode(', ', array_keys($perms_new));
							$query['VALUES'] .= ', '.implode(', ', $perms_new);

							($hook = get_hook('afo_save_forum_qr_add_forum_perms')) ? eval($hook) : null;
							db()->query_build($query) or error(__FILE__, __LINE__);
						}
					}
				}
			}
		}

		// Regenerate the quickjump cache
		if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
			require FORUM_ROOT.'include/cache.php';

		generate_quickjump_cache();

		// Add flash message
		flash()->add_info(__('Forum updated', 'admin_forums'));

		($hook = get_hook('afo_save_forum_pre_redirect')) ? eval($hook) : null;

		redirect(forum_link($forum_url['admin_forums_forum'], $forum_id), __('Forum updated', 'admin_forums'));
	}
	else if (isset($_POST['revert_perms']))
	{
		($hook = get_hook('afo_revert_perms_form_submitted')) ? eval($hook) : null;

		$query = array(
			'DELETE'	=> 'forum_perms',
			'WHERE'		=> 'forum_id='.$forum_id
		);

		($hook = get_hook('afo_revert_perms_qr_revert_forum_perms')) ? eval($hook) : null;
		db()->query_build($query) or error(__FILE__, __LINE__);

		// Regenerate the quickjump cache
		if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
			require FORUM_ROOT.'include/cache.php';

		generate_quickjump_cache();

		flash()->add_info(__('Permissions reverted', 'admin_forums'));

		($hook = get_hook('afo_revert_perms_pre_redirect')) ? eval($hook) : null;

		redirect(forum_link($forum_url['admin_forums']).'?edit_forum='.$forum_id, __('Permissions reverted', 'admin_forums'));
	}

	$forum_page['form_info'] = array();
	if ($cur_forum['redirect_url'])
		$forum_page['form_info'][] = '<li><span>'.__('Forum perms redirect info', 'admin_forums').'</span></li>';

	$forum_page['form_info']['read'] = '<li><span>'.__('Forum perms read info', 'admin_forums').'</span></li>';
	$forum_page['form_info']['restore'] = '<li><span>'.__('Forum perms restore info', 'admin_forums').'</span></li>';
	$forum_page['form_info']['groups'] = '<li><span>'. sprintf(__('Forum perms groups info', 'admin_forums'), '<a href="'.forum_link($forum_url['admin_groups']).'">'.
		__('User groups', 'admin_forums').'</a>').'</span></li>';
	$forum_page['form_info']['admins'] = '<li><span>'.
		__('Forum perms admins info', 'admin_forums').'</span></li>';

	// Setup the form
	$forum_page['item_count'] = $forum_page['group_count'] = $forum_page['fld_count'] = 0;

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array(config()['o_board_title'], forum_link($forum_url['index'])),
		array(__('Forum administration', 'admin_common'), forum_link($forum_url['admin_index'])),
		array(__('Start', 'admin_common'), forum_link($forum_url['admin_index'])),
		array(__('Forums', 'admin_common'), forum_link($forum_url['admin_forums'])),
		__('Edit forum', 'admin_forums')
	);

	($hook = get_hook('afo_edit_forum_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE_SECTION', 'start');
	define('FORUM_PAGE', 'admin-forums');

	// categories
	$query = array(
		'SELECT'	=> 'c.id, c.cat_name',
		'FROM'		=> 'categories AS c',
		'ORDER BY'	=> 'c.disp_position'
	);
	($hook = get_hook('afo_edit_forum_qr_get_categories')) ? eval($hook) : null;
	$result_categories = db()->query_build($query) or error(__FILE__, __LINE__);

	// groups
	$query = array(
		'SELECT'	=> 'g.g_id, g.g_title, g.g_read_board, g.g_post_replies, g.g_post_topics, fp.read_forum, fp.post_replies, fp.post_topics',
		'FROM'		=> 'groups AS g',
		'JOINS'		=> array(
			array(
				'LEFT JOIN'		=> 'forum_perms AS fp',
				'ON'			=> 'g.g_id=fp.group_id AND fp.forum_id='.$forum_id
			)
		),
		'WHERE'		=> 'g.g_id!='.FORUM_ADMIN,
		'ORDER BY'	=> 'g.g_id'
	);

	($hook = get_hook('afo_qr_get_forum_perms')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);

	$forum_main_view = 'admin/forums/edit';
	include FORUM_ROOT . 'include/render.php';
}

// Setup the form
$forum_page['fld_count'] = $forum_page['group_count'] = $forum_page['item_count'] = 0;

// Setup breadcrumbs
$forum_page['crumbs'] = array(
	array(config()['o_board_title'], forum_link($forum_url['index'])),
	array(__('Forum administration', 'admin_common'), forum_link($forum_url['admin_index'])),
	array(__('Start', 'admin_common'), forum_link($forum_url['admin_index'])),
	array(__('Forums', 'admin_common'), forum_link($forum_url['admin_forums']))
);

($hook = get_hook('afo_pre_header_load')) ? eval($hook) : null;

define('FORUM_PAGE_SECTION', 'start');
define('FORUM_PAGE', 'admin-forums');

// categories
$query = array(
	'SELECT'	=> 'c.id, c.cat_name',
	'FROM'		=> 'categories AS c',
	'ORDER BY'	=> 'c.disp_position'
);
($hook = get_hook('afo_qr_get_categories')) ? eval($hook) : null;
$result_categories = db()->query_build($query) or error(__FILE__, __LINE__);

// Display all the categories and forums
$query = array(
	'SELECT'	=> 'c.id AS cid, c.cat_name, f.id AS fid, f.forum_name, f.disp_position',
	'FROM'		=> 'categories AS c',
	'JOINS'		=> array(
		array(
			'INNER JOIN'	=> 'forums AS f',
			'ON'			=> 'c.id=f.cat_id'
		)
	),
	'ORDER BY'	=> 'c.disp_position, c.id, f.disp_position'
);
($hook = get_hook('afo_qr_get_cats_and_forums')) ? eval($hook) : null;
$result = db()->query_build($query) or error(__FILE__, __LINE__);

$forums = array();
while ($cur_forum = db()->fetch_assoc($result)) {
	$forums[] = $cur_forum;
}

$forum_main_view = 'admin/forums/main';
include FORUM_ROOT . 'include/render.php';
