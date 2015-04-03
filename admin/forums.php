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


if (!defined('FORUM_ROOT'))
	define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';
require FORUM_ROOT.'include/common_admin.php';

($hook = get_hook('afo_start')) ? eval($hook) : null;

if ($forum_user['g_id'] != FORUM_ADMIN)
	message($lang_common['No permission']);

// Load the admin.php language file
require FORUM_ROOT.'lang/'.$forum_user['language'].'/admin_common.php';
require FORUM_ROOT.'lang/'.$forum_user['language'].'/admin_forums.php';


// Add a "default" forum
if (isset($_POST['add_forum']))
{
	$add_to_cat = isset($_POST['add_to_cat']) ? intval($_POST['add_to_cat']) : 0;
	if ($add_to_cat < 1)
		message($lang_common['Bad request']);

	$forum_name = forum_trim($_POST['forum_name']);
	$position = intval($_POST['position']);

	($hook = get_hook('afo_add_forum_form_submitted')) ? eval($hook) : null;

	if ($forum_name == '')
		message($lang_admin_forums['Must enter forum message']);

	// Make sure the category we're adding to exists
	$query = array(
		'SELECT'	=> 'COUNT(c.id)',
		'FROM'		=> 'categories AS c',
		'WHERE'		=> 'c.id='.$add_to_cat
	);

	($hook = get_hook('afo_add_forum_qr_validate_category_id')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

	if ($forum_db->result($result) != 1)
		message($lang_common['Bad request']);


	$query = array(
		'INSERT'	=> 'forum_name, disp_position, cat_id',
		'INTO'		=> 'forums',
		'VALUES'	=> '\''.$forum_db->escape($forum_name).'\', '.$position.', '.$add_to_cat
	);

	($hook = get_hook('afo_add_forum_qr_add_forum')) ? eval($hook) : null;
	$forum_db->query_build($query) or error(__FILE__, __LINE__);

	// Regenerate the quickjump cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_quickjump_cache();

	// Add flash message
	$forum_flash->add_info($lang_admin_forums['Forum added']);

	($hook = get_hook('afo_add_forum_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['admin_forums']), $lang_admin_forums['Forum added']);
}


// Delete a forum
else if (isset($_GET['del_forum']))
{
	$forum_to_delete = intval($_GET['del_forum']);
	if ($forum_to_delete < 1)
		message($lang_common['Bad request']);

	// User pressed the cancel button
	if (isset($_POST['del_forum_cancel']))
		redirect(forum_link($forum_url['admin_forums']), $lang_admin_common['Cancel redirect']);

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
		$forum_db->query_build($query) or error(__FILE__, __LINE__);

		$query = array(
			'DELETE'	=> 'forum_perms',
			'WHERE'		=> 'forum_id='.$forum_to_delete
		);

		($hook = get_hook('afo_del_forum_qr_delete_forum_perms')) ? eval($hook) : null;
		$forum_db->query_build($query) or error(__FILE__, __LINE__);

		// Delete forum subscriptions
		$query = array(
			'DELETE'	=> 'forum_subscriptions',
			'WHERE'		=> 'forum_id='.$forum_to_delete
		);

		($hook = get_hook('afo_del_forum_qr_delete_forum_subscriptions')) ? eval($hook) : null;
		$forum_db->query_build($query) or error(__FILE__, __LINE__);

		// Regenerate the quickjump cache
		if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
			require FORUM_ROOT.'include/cache.php';

		generate_quickjump_cache();

		// Add flash message
		$forum_flash->add_info($lang_admin_forums['Forum deleted']);

		($hook = get_hook('afo_del_forum_pre_redirect')) ? eval($hook) : null;

		redirect(forum_link($forum_url['admin_forums']), $lang_admin_forums['Forum deleted']);
	}
	else	// If the user hasn't confirmed the delete
	{
		$query = array(
			'SELECT'	=> 'f.forum_name',
			'FROM'		=> 'forums AS f',
			'WHERE'		=> 'f.id='.$forum_to_delete
		);

		($hook = get_hook('afo_del_forum_qr_get_forum_name')) ? eval($hook) : null;
		$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
		$forum_name = $forum_db->result($result);

		if (is_null($forum_name) || $forum_name === false)
			message($lang_common['Bad request']);


		// Setup breadcrumbs
		$forum_page['crumbs'] = array(
			array($forum_config['o_board_title'], forum_link($forum_url['index'])),
			array($lang_admin_common['Forum administration'], forum_link($forum_url['admin_index'])),
			array($lang_admin_common['Start'], forum_link($forum_url['admin_index'])),
			array($lang_admin_common['Forums'], forum_link($forum_url['admin_forums'])),
			$lang_admin_forums['Delete forum']
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
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	while ($cur_forum = $forum_db->fetch_assoc($result))
	{
		// If these aren't set, we're looking at a forum that was added after
		// the admin started editing: we don't want to mess with it
		if (isset($positions[$cur_forum['id']]))
		{
			$new_disp_position = $positions[$cur_forum['id']];

			if ($new_disp_position < 0)
				message($lang_admin_forums['Must be integer']);

			// We only want to update if we changed the position
			if ($cur_forum['disp_position'] != $new_disp_position)
			{
				$query = array(
					'UPDATE'	=> 'forums',
					'SET'		=> 'disp_position='.$new_disp_position,
					'WHERE'		=> 'id='.$cur_forum['id']
				);

				($hook = get_hook('afo_update_positions_qr_update_forum_position')) ? eval($hook) : null;
				$forum_db->query_build($query) or error(__FILE__, __LINE__);
			}
		}
	}

	// Regenerate the quickjump cache
	require_once FORUM_ROOT.'include/cache.php';
	generate_quickjump_cache();

	// Add flash message
	$forum_flash->add_info($lang_admin_forums['Forums updated']);

	($hook = get_hook('afo_update_positions_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['admin_forums']), $lang_admin_forums['Forums updated']);
}


else if (isset($_GET['edit_forum']))
{
	$forum_id = intval($_GET['edit_forum']);
	if ($forum_id < 1)
		message($lang_common['Bad request']);

	($hook = get_hook('afo_edit_forum_selected')) ? eval($hook) : null;

	// Fetch forum info
	$query = array(
		'SELECT'	=> 'f.id, f.forum_name, f.forum_desc, f.redirect_url, f.num_topics, f.sort_by, f.cat_id',
		'FROM'		=> 'forums AS f',
		'WHERE'		=> 'f.id='.$forum_id
	);

	($hook = get_hook('afo_edit_forum_qr_get_forum_details')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	$cur_forum = $forum_db->fetch_assoc($result);

	if (is_null($cur_forum) || $cur_forum === false)
		message($lang_common['Bad request']);


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
			message($lang_admin_forums['Must enter forum message']);

		if ($cat_id < 1)
			message($lang_common['Bad request']);

		$forum_desc = ($forum_desc != '') ? '\''.$forum_db->escape($forum_desc).'\'' : 'NULL';
		$redirect_url = ($redirect_url != '') ? '\''.$forum_db->escape($redirect_url).'\'' : 'NULL';

		$query = array(
			'UPDATE'	=> 'forums',
			'SET'		=> 'forum_name=\''.$forum_db->escape($forum_name).'\', forum_desc='.$forum_desc.', redirect_url='.$redirect_url.', sort_by='.$sort_by.', cat_id='.$cat_id,
			'WHERE'		=> 'id='.$forum_id
		);

		($hook = get_hook('afo_save_forum_qr_update_forum')) ? eval($hook) : null;
		$forum_db->query_build($query) or error(__FILE__, __LINE__);

		// Now let's deal with the permissions
		if (isset($_POST['read_forum_old']))
		{
			$query = array(
				'SELECT'	=> 'g.g_id, g.g_read_board, g.g_post_replies, g.g_post_topics',
				'FROM'		=> 'groups AS g',
				'WHERE'		=> 'g_id!='.FORUM_ADMIN
			);

			($hook = get_hook('afo_save_forum_qr_get_groups')) ? eval($hook) : null;
			$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
			while ($cur_group = $forum_db->fetch_assoc($result))
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
						$forum_db->query_build($query) or error(__FILE__, __LINE__);
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
						$forum_db->query_build($query) or error(__FILE__, __LINE__);
						if (!$forum_db->affected_rows())
						{
							$query = array(
								'INSERT'	=> 'group_id, forum_id',
								'INTO'		=> 'forum_perms',
								'VALUES'	=> $cur_group['g_id'].', '.$forum_id
							);

							$query['INSERT'] .= ', '.implode(', ', array_keys($perms_new));
							$query['VALUES'] .= ', '.implode(', ', $perms_new);

							($hook = get_hook('afo_save_forum_qr_add_forum_perms')) ? eval($hook) : null;
							$forum_db->query_build($query) or error(__FILE__, __LINE__);
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
		$forum_flash->add_info($lang_admin_forums['Forum updated']);

		($hook = get_hook('afo_save_forum_pre_redirect')) ? eval($hook) : null;

		redirect(forum_link($forum_url['admin_forums_forum'], $forum_id), $lang_admin_forums['Forum updated']);
	}
	else if (isset($_POST['revert_perms']))
	{
		($hook = get_hook('afo_revert_perms_form_submitted')) ? eval($hook) : null;

		$query = array(
			'DELETE'	=> 'forum_perms',
			'WHERE'		=> 'forum_id='.$forum_id
		);

		($hook = get_hook('afo_revert_perms_qr_revert_forum_perms')) ? eval($hook) : null;
		$forum_db->query_build($query) or error(__FILE__, __LINE__);

		// Regenerate the quickjump cache
		if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
			require FORUM_ROOT.'include/cache.php';

		generate_quickjump_cache();

		$forum_flash->add_info($lang_admin_forums['Permissions reverted']);

		($hook = get_hook('afo_revert_perms_pre_redirect')) ? eval($hook) : null;

		redirect(forum_link($forum_url['admin_forums']).'?edit_forum='.$forum_id, $lang_admin_forums['Permissions reverted']);
	}

	$forum_page['form_info'] = array();
	if ($cur_forum['redirect_url'])
		$forum_page['form_info'][] = '<li><span>'.$lang_admin_forums['Forum perms redirect info'].'</span></li>';

	$forum_page['form_info']['read'] = '<li><span>'.$lang_admin_forums['Forum perms read info'].'</span></li>';
	$forum_page['form_info']['restore'] = '<li><span>'.$lang_admin_forums['Forum perms restore info'].'</span></li>';
	$forum_page['form_info']['groups'] = '<li><span>'. sprintf($lang_admin_forums['Forum perms groups info'], '<a href="'.forum_link($forum_url['admin_groups']).'">'.$lang_admin_forums['User groups'].'</a>').'</span></li>';
	$forum_page['form_info']['admins'] = '<li><span>'.$lang_admin_forums['Forum perms admins info'].'</span></li>';

	// Setup the form
	$forum_page['item_count'] = $forum_page['group_count'] = $forum_page['fld_count'] = 0;

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array($forum_config['o_board_title'], forum_link($forum_url['index'])),
		array($lang_admin_common['Forum administration'], forum_link($forum_url['admin_index'])),
		array($lang_admin_common['Start'], forum_link($forum_url['admin_index'])),
		array($lang_admin_common['Forums'], forum_link($forum_url['admin_forums'])),
		$lang_admin_forums['Edit forum']
	);

	($hook = get_hook('afo_edit_forum_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE_SECTION', 'start');
	define('FORUM_PAGE', 'admin-forums');

	$forum_main_view = 'admin/forums/edit';
	include FORUM_ROOT . 'include/render.php';
}

// Setup the form
$forum_page['fld_count'] = $forum_page['group_count'] = $forum_page['item_count'] = 0;

// Setup breadcrumbs
$forum_page['crumbs'] = array(
	array($forum_config['o_board_title'], forum_link($forum_url['index'])),
	array($lang_admin_common['Forum administration'], forum_link($forum_url['admin_index'])),
	array($lang_admin_common['Start'], forum_link($forum_url['admin_index'])),
	array($lang_admin_common['Forums'], forum_link($forum_url['admin_forums']))
);

($hook = get_hook('afo_pre_header_load')) ? eval($hook) : null;

define('FORUM_PAGE_SECTION', 'start');
define('FORUM_PAGE', 'admin-forums');

$forum_main_view = 'admin/forums/main';
include FORUM_ROOT . 'include/render.php';
