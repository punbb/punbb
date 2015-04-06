<?php
/**
 * Group management page.
 *
 * Allows administrators to control group permissions.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */

require __DIR__ . '/../vendor/pautoload.php';

($hook = get_hook('agr_start')) ? eval($hook) : null;

if ($forum_user['g_id'] != FORUM_ADMIN)
	message(__('No permission'));

// Load the admin.php language file
require FORUM_ROOT.'lang/'.$forum_user['language'].'/admin_common.php';
require FORUM_ROOT.'lang/'.$forum_user['language'].'/admin_groups.php';


// Add/edit a group (stage 1)
if (isset($_POST['add_group']) || isset($_GET['edit_group']))
{
	if (isset($_POST['add_group']))
	{
		($hook = get_hook('agr_add_group_form_submitted')) ? eval($hook) : null;

		$base_group = intval($_POST['base_group']);

		$query = array(
			'SELECT'	=> 'g.*',
			'FROM'		=> 'groups AS g',
			'WHERE'		=> 'g.g_id='.$base_group
		);

		($hook = get_hook('agr_add_group_qr_get_base_group')) ? eval($hook) : null;
		$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
		$group = $forum_db->fetch_assoc($result);

		$mode = 'add';
	}
	else	// We are editing a group
	{
		($hook = get_hook('agr_edit_group_form_submitted')) ? eval($hook) : null;

		$group_id = intval($_GET['edit_group']);
		if ($group_id < 1)
			message(__('Bad request'));

		$query = array(
			'SELECT'	=> 'g.*',
			'FROM'		=> 'groups AS g',
			'WHERE'		=> 'g.g_id='.$group_id
		);

		($hook = get_hook('agr_edit_group_qr_get_group')) ? eval($hook) : null;
		$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
		$group = $forum_db->fetch_assoc($result);

		if (!$group)
			message(__('Bad request'));

		$mode = 'edit';
	}

	// Setup the form
	$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array($forum_config['o_board_title'], forum_link($forum_url['index'])),
		array($lang_admin_common['Forum administration'], forum_link($forum_url['admin_index'])),
		array($lang_admin_common['Users'], forum_link($forum_url['admin_users'])),
		array($lang_admin_common['Groups'], forum_link($forum_url['admin_groups'])),
		$mode == 'edit' ? $lang_admin_groups['Edit group heading'] : $lang_admin_groups['Add group heading']
	);

	($hook = get_hook('agr_add_edit_group_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE_SECTION', 'users');
	define('FORUM_PAGE', 'admin-groups');

	$forum_main_view = 'admin/groups/edit';
	include FORUM_ROOT . 'include/render.php';
}


// Add/edit a group (stage 2)
else if (isset($_POST['add_edit_group']))
{
	// Is this the admin group? (special rules apply)
	$is_admin_group = (isset($_POST['group_id']) && $_POST['group_id'] == FORUM_ADMIN) ? true : false;

	$title = forum_trim($_POST['req_title']);
	$user_title = forum_trim($_POST['user_title']);
	$moderator = isset($_POST['moderator']) && $_POST['moderator'] == '1' ? '1' : '0';
	$mod_edit_users = $moderator == '1' && isset($_POST['mod_edit_users']) && $_POST['mod_edit_users'] == '1' ? '1' : '0';
	$mod_rename_users = $moderator == '1' && isset($_POST['mod_rename_users']) && $_POST['mod_rename_users'] == '1' ? '1' : '0';
	$mod_change_passwords = $moderator == '1' && isset($_POST['mod_change_passwords']) && $_POST['mod_change_passwords'] == '1' ? '1' : '0';
	$mod_ban_users = $moderator == '1' && isset($_POST['mod_ban_users']) && $_POST['mod_ban_users'] == '1' ? '1' : '0';
	$read_board = (isset($_POST['read_board']) && $_POST['read_board'] == '1') || $is_admin_group ? '1' : '0';
	$view_users = (isset($_POST['view_users']) && $_POST['view_users'] == '1') || $is_admin_group ? '1' : '0';
	$post_replies = (isset($_POST['post_replies']) && $_POST['post_replies'] == '1') || $is_admin_group ? '1' : '0';
	$post_topics = (isset($_POST['post_topics']) && $_POST['post_topics'] == '1') || $is_admin_group ? '1' : '0';
	$edit_posts = (isset($_POST['edit_posts']) && $_POST['edit_posts'] == '1') || $is_admin_group ? '1' : '0';
	$delete_posts = (isset($_POST['delete_posts']) && $_POST['delete_posts'] == '1') || $is_admin_group ? '1' : '0';
	$delete_topics = (isset($_POST['delete_topics']) && $_POST['delete_topics'] == '1') || $is_admin_group ? '1' : '0';
	$set_title = (isset($_POST['set_title']) && $_POST['set_title'] == '1') || $is_admin_group ? '1' : '0';
	$search = (isset($_POST['search']) && $_POST['search'] == '1') || $is_admin_group ? '1' : '0';
	$search_users = (isset($_POST['search_users']) && $_POST['search_users'] == '1') || $is_admin_group ? '1' : '0';
	$send_email = (isset($_POST['send_email']) && $_POST['send_email'] == '1') || $is_admin_group ? '1' : '0';
	$post_flood = isset($_POST['post_flood']) ? intval($_POST['post_flood']) : '0';
	$search_flood = isset($_POST['search_flood']) ? intval($_POST['search_flood']) : '0';
	$email_flood = isset($_POST['email_flood']) ? intval($_POST['email_flood']) : '0';

	if ($title == '')
		message($lang_admin_groups['Must enter group message']);

	$user_title = ($user_title != '') ? '\''.$forum_db->escape($user_title).'\'' : 'NULL';

	($hook = get_hook('agr_add_edit_end_validation')) ? eval($hook) : null;


	if ($_POST['mode'] == 'add')
	{
		($hook = get_hook('agr_add_add_group')) ? eval($hook) : null;

		$query = array(
			'SELECT'	=> 'COUNT(g.g_id)',
			'FROM'		=> 'groups AS g',
			'WHERE'		=> 'g_title=\''.$forum_db->escape($title).'\''
		);

		($hook = get_hook('agr_add_end_qr_check_add_group_title_collision')) ? eval($hook) : null;
		$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

		if ($forum_db->result($result) != 0)
			message(sprintf($lang_admin_groups['Already a group message'], forum_htmlencode($title)));

		// Insert the new group
		$query = array(
			'INSERT'	=> 'g_title, g_user_title, g_moderator, g_mod_edit_users, g_mod_rename_users, g_mod_change_passwords, g_mod_ban_users, g_read_board, g_view_users, g_post_replies, g_post_topics, g_edit_posts, g_delete_posts, g_delete_topics, g_set_title, g_search, g_search_users, g_send_email, g_post_flood, g_search_flood, g_email_flood',
			'INTO'		=> 'groups',
			'VALUES'	=> '\''.$forum_db->escape($title).'\', '.$user_title.', '.$moderator.', '.$mod_edit_users.', '.$mod_rename_users.', '.$mod_change_passwords.', '.$mod_ban_users.', '.$read_board.', '.$view_users.', '.$post_replies.', '.$post_topics.', '.$edit_posts.', '.$delete_posts.', '.$delete_topics.', '.$set_title.', '.$search.', '.$search_users.', '.$send_email.', '.$post_flood.', '.$search_flood.', '.$email_flood
		);

		($hook = get_hook('agr_add_end_qr_add_group')) ? eval($hook) : null;
		$forum_db->query_build($query) or error(__FILE__, __LINE__);
		$new_group_id = $forum_db->insert_id();

		// Now lets copy the forum specific permissions from the group which this group is based on
		$query = array(
			'SELECT'	=> 'fp.forum_id, fp.read_forum, fp.post_replies, fp.post_topics',
			'FROM'		=> 'forum_perms AS fp',
			'WHERE'		=> 'group_id='.intval($_POST['base_group'])
		);

		($hook = get_hook('agr_add_end_qr_get_group_forum_perms')) ? eval($hook) : null;
		$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
		while ($cur_forum_perm = $forum_db->fetch_assoc($result))
		{
			$query = array(
				'INSERT'	=> 'group_id, forum_id, read_forum, post_replies, post_topics',
				'INTO'		=> 'forum_perms',
				'VALUES'	=> $new_group_id.', '.$cur_forum_perm['forum_id'].', '.$cur_forum_perm['read_forum'].', '.$cur_forum_perm['post_replies'].', '.$cur_forum_perm['post_topics']
			);

			($hook = get_hook('agr_add_end_qr_add_group_forum_perms')) ? eval($hook) : null;
			$forum_db->query_build($query) or error(__FILE__, __LINE__);
		}
	}
	else
	{
		$group_id = intval($_POST['group_id']);

		($hook = get_hook('agr_edit_end_edit_group')) ? eval($hook) : null;

		// Make sure admins and guests don't get moderator privileges
		if ($group_id == FORUM_ADMIN || $group_id == FORUM_GUEST)
			$moderator = '0';

		// Make sure the default group isn't assigned moderator privileges
		if ($moderator == '1' && $forum_config['o_default_user_group'] == $group_id)
			message($lang_admin_groups['Moderator default group']);

		$query = array(
			'SELECT'	=> 'COUNT(g.g_id)',
			'FROM'		=> 'groups AS g',
			'WHERE'		=> 'g_title=\''.$forum_db->escape($title).'\' AND g_id!='.$group_id
		);

		($hook = get_hook('agr_edit_end_qr_check_edit_group_title_collision')) ? eval($hook) : null;
		$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

		if ($forum_db->result($result) != 0)
			message(sprintf($lang_admin_groups['Already a group message'], forum_htmlencode($title)));

		// Save changes
		$query = array(
			'UPDATE'	=> 'groups',
			'SET'		=> 'g_title=\''.$forum_db->escape($title).'\', g_user_title='.$user_title.', g_moderator='.$moderator.', g_mod_edit_users='.$mod_edit_users.', g_mod_rename_users='.$mod_rename_users.', g_mod_change_passwords='.$mod_change_passwords.', g_mod_ban_users='.$mod_ban_users.', g_read_board='.$read_board.', g_view_users='.$view_users.', g_post_replies='.$post_replies.', g_post_topics='.$post_topics.', g_edit_posts='.$edit_posts.', g_delete_posts='.$delete_posts.', g_delete_topics='.$delete_topics.', g_set_title='.$set_title.', g_search='.$search.', g_search_users='.$search_users.', g_send_email='.$send_email.', g_post_flood='.$post_flood.', g_search_flood='.$search_flood.', g_email_flood='.$email_flood,
			'WHERE'		=> 'g_id='.$group_id
		);

		($hook = get_hook('agr_edit_end_qr_update_group')) ? eval($hook) : null;
		$forum_db->query_build($query) or error(__FILE__, __LINE__);

		// If the group doesn't have moderator privileges (it might have had before), remove its users from the moderator list in all forums
		if (!$moderator)
			clean_forum_moderators();
	}

	// Regenerate the quickjump cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_quickjump_cache();

	// Add flash message
	$forum_flash->add_info((($_POST['mode'] == 'edit') ? $lang_admin_groups['Group edited'] : $lang_admin_groups['Group added']));

	($hook = get_hook('agr_add_edit_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['admin_groups']), (($_POST['mode'] == 'edit') ? $lang_admin_groups['Group edited'] : $lang_admin_groups['Group added']));
}


// Set default group
else if (isset($_POST['set_default_group']))
{
	$group_id = intval($_POST['default_group']);

	($hook = get_hook('agr_set_default_group_form_submitted')) ? eval($hook) : null;

	// Make sure it's not the admin or guest groups
	if ($group_id == FORUM_ADMIN || $group_id == FORUM_GUEST)
		message(__('Bad request'));

	// Make sure it's not a moderator group
	$query = array(
		'SELECT'	=> 'COUNT(g.g_id)',
		'FROM'		=> 'groups AS g',
		'WHERE'		=> 'g.g_id='.$group_id.' AND g.g_moderator=0',
		'LIMIT'		=> '1'
	);

	($hook = get_hook('agr_set_default_group_qr_get_group_moderation_status')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

	if ($forum_db->result($result) != 1)
		message(__('Bad request'));

	$query = array(
		'UPDATE'	=> 'config',
		'SET'		=> 'conf_value='.$group_id,
		'WHERE'		=> 'conf_name=\'o_default_user_group\''
	);

	($hook = get_hook('agr_set_default_group_qr_set_default_group')) ? eval($hook) : null;
	$forum_db->query_build($query) or error(__FILE__, __LINE__);

	// Regenerate the config cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_config_cache();

	// Add flash message
	$forum_flash->add_info($lang_admin_groups['Default group set']);

	($hook = get_hook('agr_set_default_group_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['admin_groups']), $lang_admin_groups['Default group set']);
}


// Remove a group
else if (isset($_GET['del_group']))
{
	$group_id = intval($_GET['del_group']);
	if ($group_id <= FORUM_GUEST)
		message(__('Bad request'));

	// User pressed the cancel button
	if (isset($_POST['del_group_cancel']))
		redirect(forum_link($forum_url['admin_groups']), $lang_admin_common['Cancel redirect']);

	// Make sure we don't remove the default group
	if ($group_id == $forum_config['o_default_user_group'])
		message($lang_admin_groups['Cannot remove default group']);

	($hook = get_hook('agr_del_group_selected')) ? eval($hook) : null;


	// Check if this group has any members
	$query = array(
		'SELECT'	=> 'g.g_title AS title, COUNT(u.id) AS num_members',
		'FROM'		=> 'groups AS g',
		'JOINS'		=> array(
			array(
				'INNER JOIN'	=> 'users AS u',
				'ON'			=> 'g.g_id=u.group_id'
			)
		),
		'WHERE'		=> 'g.g_id='.$group_id,
		'GROUP BY'	=> 'g.g_id, g.g_title'
	);

	($hook = get_hook('agr_del_group_qr_get_group_member_count')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	$group_info = $forum_db->fetch_row($result);

	// If the group doesn't have any members or if we've already selected a group to move the members to
	if (!$group_info || isset($_POST['del_group']))
	{
		($hook = get_hook('agr_del_group_form_submitted')) ? eval($hook) : null;

		if (isset($_POST['del_group']))	// Move users
		{
			$query = array(
				'UPDATE'	=> 'users',
				'SET'		=> 'group_id='.intval($_POST['move_to_group']),
				'WHERE'		=> 'group_id='.$group_id
			);

			($hook = get_hook('agr_del_group_qr_move_users')) ? eval($hook) : null;
			$forum_db->query_build($query) or error(__FILE__, __LINE__);
		}

		// Delete the group and any forum specific permissions
		$query = array(
			'DELETE'	=> 'groups',
			'WHERE'		=> 'g_id='.$group_id
		);

		($hook = get_hook('agr_del_group_qr_delete_group')) ? eval($hook) : null;
		$forum_db->query_build($query) or error(__FILE__, __LINE__);

		$query = array(
			'DELETE'	=> 'forum_perms',
			'WHERE'		=> 'group_id='.$group_id
		);

		($hook = get_hook('agr_del_group_qr_delete_group_forum_perms')) ? eval($hook) : null;
		$forum_db->query_build($query) or error(__FILE__, __LINE__);

		clean_forum_moderators();

		// Regenerate the quickjump cache
		if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
			require FORUM_ROOT.'include/cache.php';

		generate_quickjump_cache();

		// Add flash message
		$forum_flash->add_info($lang_admin_groups['Group removed']);

		($hook = get_hook('agr_del_group_pre_redirect')) ? eval($hook) : null;

		redirect(forum_link($forum_url['admin_groups']), $lang_admin_groups['Group removed']);
	}

	// Setup the form
	$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array($forum_config['o_board_title'], forum_link($forum_url['index'])),
		array($lang_admin_common['Forum administration'], forum_link($forum_url['admin_index'])),
		array($lang_admin_common['Users'], forum_link($forum_url['admin_users'])),
		array($lang_admin_common['Groups'], forum_link($forum_url['admin_groups'])),
		$lang_admin_groups['Remove group']
	);

	($hook = get_hook('agr_del_group_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE_SECTION', 'users');
	define('FORUM_PAGE', 'admin-groups');

	$query = array(
		'SELECT'	=> 'g.g_id, g.g_title',
		'FROM'		=> 'groups AS g',
		'WHERE'		=> 'g.g_id!='.FORUM_GUEST.' AND g.g_id!='.$group_id,
		'ORDER BY'	=> 'g.g_title'
	);

	($hook = get_hook('agr_del_group_qr_get_groups')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

	$forum_main_view = 'admin/groups/delete';
	include FORUM_ROOT . 'include/render.php';
}


// Setup the form
$forum_page['item_count'] = $forum_page['fld_count'] = $forum_page['group_count'] = 0;

// Setup breadcrumbs
$forum_page['crumbs'] = array(
	array($forum_config['o_board_title'], forum_link($forum_url['index'])),
	array($lang_admin_common['Forum administration'], forum_link($forum_url['admin_index'])),
	array($lang_admin_common['Users'], forum_link($forum_url['admin_users'])),
	array($lang_admin_common['Groups'], forum_link($forum_url['admin_groups']))
);

($hook = get_hook('agr_pre_header_load')) ? eval($hook) : null;

define('FORUM_PAGE_SECTION', 'users');
define('FORUM_PAGE', 'admin-groups');

$query = array(
	'SELECT'	=> 'g.g_id, g.g_title',
	'FROM'		=> 'groups AS g',
	'WHERE'		=> 'g_id>'.FORUM_GUEST,
	'ORDER BY'	=> 'g.g_title'
);
($hook = get_hook('agr_qr_get_allowed_base_groups')) ? eval($hook) : null;
$result_groups = $forum_db->query_build($query) or error(__FILE__, __LINE__);

$query = array(
	'SELECT'	=> 'g.g_id, g.g_title',
	'FROM'		=> 'groups AS g',
	'WHERE'		=> 'g_id>'.FORUM_GUEST.' AND g_moderator=0',
	'ORDER BY'	=> 'g.g_title'
);
($hook = get_hook('agr_qr_get_groups')) ? eval($hook) : null;
$result_groups_default = $forum_db->query_build($query) or error(__FILE__, __LINE__);

$query = array(
	'SELECT'	=> 'g.g_id, g.g_title',
	'FROM'		=> 'groups AS g',
	'ORDER BY'	=> 'g.g_title'
);
($hook = get_hook('agr_qr_get_group_list')) ? eval($hook) : null;
$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

$forum_main_view = 'admin/groups/main';
include FORUM_ROOT . 'include/render.php';
