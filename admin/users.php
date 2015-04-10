<?php
/**
 * User search page.
 *
 * Allows administrators or moderators to search the existing users based on various criteria.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */
namespace punbb;

require __DIR__ . '/../vendor/pautoload.php';

($hook = get_hook('aus_start')) ? eval($hook) : null;

if (!$forum_user['is_admmod'])
	message(__('No permission'));

// Show IP statistics for a certain user ID
if (isset($_GET['ip_stats']))
{
	$ip_stats = intval($_GET['ip_stats']);
	if ($ip_stats < 1)
		message(__('Bad request'));

	($hook = get_hook('aus_ip_stats_selected')) ? eval($hook) : null;

	$query = array(
		'SELECT'	=> 'p.poster_ip, MAX(p.posted) AS last_used, COUNT(p.id) AS used_times',
		'FROM'		=> 'posts AS p',
		'WHERE'		=> 'p.poster_id='.$ip_stats,
		'GROUP BY'	=> 'p.poster_ip',
		'ORDER BY'	=> 'last_used DESC'
	);

	($hook = get_hook('aus_ip_stats_qr_get_user_ips')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);

	$founded_ips = array();
	while ($cur_ip = db()->fetch_assoc($result))
	{
		$founded_ips[] = $cur_ip;
	}

	$forum_page['num_users'] = count($founded_ips);

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array(config()['o_board_title'], forum_link($forum_url['index'])),
		array(__('Forum administration', 'admin_common'), forum_link($forum_url['admin_index']))
	);
	if ($forum_user['g_id'] == FORUM_ADMIN)
		$forum_page['crumbs'][] = array(__('Users', 'admin_common'), forum_link($forum_url['admin_users']));
	$forum_page['crumbs'][] = array(__('Searches', 'admin_common'), forum_link($forum_url['admin_users']));
	$forum_page['crumbs'][] = __('User search results', 'admin_users');

	($hook = get_hook('aus_ip_stats_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE_SECTION', 'users');
	define('FORUM_PAGE', 'admin-iresults');

	$forum_main_view = 'admin/users/stats';
	include FORUM_ROOT . 'include/render.php';
}


// Show users that have at one time posted with the specified IP address
else if (isset($_GET['show_users']))
{
	$ip = $_GET['show_users'];

	if (empty($ip) || (!preg_match('/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/', $ip) && !preg_match('/^((([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}:[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){0,5}:((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(::([0-9A-Fa-f]{1,4}:){0,5}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|([0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}[0-9A-Fa-f]{1,4})|(::([0-9A-Fa-f]{1,4}:){0,6}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){1,7}:))$/', $ip)))
		message(__('Invalid IP address', 'admin_users'));

	($hook = get_hook('aus_show_users_selected')) ? eval($hook) : null;

	$query = array(
		'SELECT'	=> 'DISTINCT p.poster_id, p.poster',
		'FROM'		=> 'posts AS p',
		'WHERE'		=> 'p.poster_ip=\''.db()->escape($ip).'\'',
		'ORDER BY'	=> 'p.poster DESC'
	);

	($hook = get_hook('aus_show_users_qr_get_users_matching_ip')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);

	$users = array();
	while ($cur_user = db()->fetch_assoc($result))
	{
		$users[] = $cur_user;
	}

	$forum_page['num_users'] = count($users);

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array(config()['o_board_title'], forum_link($forum_url['index'])),
		array(__('Forum administration', 'admin_common'), forum_link($forum_url['admin_index']))
	);
	if ($forum_user['g_id'] == FORUM_ADMIN)
		$forum_page['crumbs'][] = array(__('Users', 'admin_common'), forum_link($forum_url['admin_users']));
	$forum_page['crumbs'][] = array(__('Searches', 'admin_common'), forum_link($forum_url['admin_users']));
	$forum_page['crumbs'][] = __('User search results', 'admin_users');

	($hook = get_hook('aus_show_users_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE_SECTION', 'users');
	define('FORUM_PAGE', 'admin-uresults');

	if ($forum_page['num_users'] > 0) {
		$users_data_list = array();
		// Loop through users and print out some info
		foreach ($users as $user) {
			$query = array(
				'SELECT'	=> 'u.id, u.username, u.email, u.title, u.num_posts, u.admin_note, g.g_id, g.g_user_title',
				'FROM'		=> 'users AS u',
				'JOINS'		=> array(
					array(
						'INNER JOIN'	=> 'groups AS g',
						'ON'			=> 'g.g_id=u.group_id'
					)
				),
				'WHERE'		=> 'u.id>1 AND u.id='.$user['poster_id']
			);
			($hook = get_hook('aus_show_users_qr_get_user_details')) ? eval($hook) : null;
			$result2 = db()->query_build($query) or error(__FILE__, __LINE__);
			$users_data_list[] = db()->fetch_assoc($result2);
		}
	}

	$forum_main_view = 'admin/users/show';
	include FORUM_ROOT . 'include/render.php';
}


else if (isset($_POST['delete_users']) || isset($_POST['delete_users_comply']) || isset($_POST['delete_users_cancel']))
{
	// User pressed the cancel button
	if (isset($_POST['delete_users_cancel']))
		redirect(forum_link($forum_url['admin_users']), __('Cancel redirect', 'admin_common'));

	if ($forum_user['g_id'] != FORUM_ADMIN)
		message(__('No permission'));

	if (empty($_POST['users']))
		message(__('No users selected', 'admin_users'));

	($hook = get_hook('aus_delete_users_selected')) ? eval($hook) : null;

	if (!is_array($_POST['users']))
		$users = explode(',', $_POST['users']);
	else
		$users = array_keys($_POST['users']);

	$users = array_map('intval', $users);

	// We check to make sure there are no administrators in this list
	$query = array(
		'SELECT'	=> 'COUNT(u.id)',
		'FROM'		=> 'users AS u',
		'WHERE'		=> 'u.id IN ('.implode(',', $users).') AND u.group_id='.FORUM_ADMIN
	);

	($hook = get_hook('aus_delete_users_qr_check_for_admins')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);

	if (db()->result($result) > 0)
		message(__('Delete admin message', 'admin_users'));

	if (isset($_POST['delete_users_comply']))
	{
		($hook = get_hook('aus_delete_users_form_submitted')) ? eval($hook) : null;

		foreach ($users as $id)
		{
			// We don't want to delete the Guest user
			if ($id > 1)
				delete_user($id, isset($_POST['delete_posts']));
		}

		// Remove cache file with forum stats
		if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		{
			require FORUM_ROOT.'include/cache.php';
		}

		clean_stats_cache();

		($hook = get_hook('aus_delete_users_pre_redirect')) ? eval($hook) : null;

		redirect(forum_link($forum_url['admin_users']), __('Users deleted', 'admin_users'));
	}

	// Setup form
	$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array(config()['o_board_title'], forum_link($forum_url['index'])),
		array(__('Forum administration', 'admin_common'), forum_link($forum_url['admin_index'])),
		array(__('Users', 'admin_common'), forum_link($forum_url['admin_users'])),
		array(__('Searches', 'admin_common'), forum_link($forum_url['admin_users'])),
		__('Delete users', 'admin_users')
	);

	($hook = get_hook('aus_delete_users_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE_SECTION', 'users');
	define('FORUM_PAGE', 'admin-users');

	$forum_main_view = 'admin/users/delete';
	include FORUM_ROOT . 'include/render.php';
}


else if (isset($_POST['ban_users']) || isset($_POST['ban_users_comply']))
{
	if ($forum_user['g_id'] != FORUM_ADMIN && ($forum_user['g_moderator'] != '1' || $forum_user['g_mod_ban_users'] == '0'))
		message(__('No permission'));

	if (empty($_POST['users']))
		message(__('No users selected', 'admin_users'));

	($hook = get_hook('aus_ban_users_selected')) ? eval($hook) : null;

	if (!is_array($_POST['users']))
		$users = explode(',', $_POST['users']);
	else
		$users = array_keys($_POST['users']);

	$users = array_map('intval', $users);

	// We check to make sure there are no administrators in this list
	$query = array(
		'SELECT'	=> 'COUNT(u.id)',
		'FROM'		=> 'users AS u',
		'WHERE'		=> 'u.id IN ('.implode(',', $users).') AND u.group_id='.FORUM_ADMIN
	);

	($hook = get_hook('aus_ban_users_qr_check_for_admins')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);

	if (db()->result($result) > 0)
		message(__('Ban admin message', 'admin_users'));

	if (isset($_POST['ban_users_comply']))
	{
		$ban_message = forum_trim($_POST['ban_message']);
		$ban_expire = forum_trim($_POST['ban_expire']);

		($hook = get_hook('aus_ban_users_form_submitted')) ? eval($hook) : null;

		if ($ban_expire != '' && $ban_expire != 'Never')
		{
			$ban_expire = strtotime($ban_expire);

			if ($ban_expire == -1 || $ban_expire <= time())
				message(__('Invalid expire message', 'admin_bans'));
		}
		else
			$ban_expire = 'NULL';

		$ban_message = ($ban_message != '') ? '\''.db()->escape($ban_message).'\'' : 'NULL';

		// Get the latest IPs for the posters and store them for a little later
		$query = array(
			'SELECT'	=> 'p.poster_id, p.poster_ip',
			'FROM'		=> 'posts AS p',
			'WHERE'		=> 'p.poster_id IN ('.implode(',', $users).') AND p.poster_id>1',
			'ORDER BY'	=> 'p.posted ASC'
		);

		($hook = get_hook('aus_ban_users_qr_get_latest_user_ips')) ? eval($hook) : null;
		$result = db()->query_build($query) or error(__FILE__, __LINE__);

		$ips = array();
		while ($cur_post = db()->fetch_assoc($result))
			$ips[$cur_post['poster_id']] = $cur_post['poster_ip'];

		// Get the rest of the data for the posters, merge in the IP information, create a ban
		$query = array(
			'SELECT'	=> 'u.id, u.username, u.email, u.registration_ip',
			'FROM'		=> 'users AS u',
			'WHERE'		=> 'id IN ('.implode(',', $users).') AND id>1'
		);

		($hook = get_hook('aus_ban_users_qr_get_users')) ? eval($hook) : null;
		$result = db()->query_build($query) or error(__FILE__, __LINE__);
		while ($cur_user = db()->fetch_assoc($result))
		{
			$ban_ip = isset($ips[$cur_user['id']]) ? $ips[$cur_user['id']] : $cur_user['registration_ip'];

			$query = array(
				'INSERT'	=> 'username, ip, email, message, expire, ban_creator',
				'INTO'		=> 'bans',
				'VALUES'	=> '\''.db()->escape($cur_user['username']).'\', \''.$ban_ip.'\', \''.db()->escape($cur_user['email']).'\', '.$ban_message.', '.$ban_expire.', '.$forum_user['id']
			);

			($hook = get_hook('aus_ban_users_qr_add_ban')) ? eval($hook) : null;
			db()->query_build($query) or error(__FILE__, __LINE__);
		}

		// Regenerate the bans cache
		if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
			require FORUM_ROOT.'include/cache.php';

		generate_bans_cache();

		// Add flash message
		flash()->add_info(__('Users banned', 'admin_users'));

		($hook = get_hook('aus_ban_users_pre_redirect')) ? eval($hook) : null;

		redirect(forum_link($forum_url['admin_users']), __('Users banned', 'admin_users'));
	}

	// Setup form
	$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array(config()['o_board_title'], forum_link($forum_url['index'])),
		array(__('Forum administration', 'admin_common'), forum_link($forum_url['admin_index']))
	);
	if ($forum_user['g_id'] == FORUM_ADMIN)
		$forum_page['crumbs'][] = array(__('Users', 'admin_common'), forum_link($forum_url['admin_users']));
	$forum_page['crumbs'][] = array(__('Searches', 'admin_common'), forum_link($forum_url['admin_users']));
	$forum_page['crumbs'][] = __('Ban users', 'admin_users');

	($hook = get_hook('aus_ban_users_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE_SECTION', 'users');
	define('FORUM_PAGE', 'admin-users');

	$forum_main_view = 'admin/users/ban_users';
	include FORUM_ROOT . 'include/render.php';
}


else if (isset($_POST['change_group']) || isset($_POST['change_group_comply']) || isset($_POST['change_group_cancel']))
{
	if ($forum_user['g_id'] != FORUM_ADMIN)
		message(__('No permission'));

	// User pressed the cancel button
	if (isset($_POST['change_group_cancel']))
		redirect(forum_link($forum_url['admin_users']), __('Cancel redirect', 'admin_common'));

	if (empty($_POST['users']))
		message(__('No users selected', 'admin_users'));

	($hook = get_hook('aus_change_group_selected')) ? eval($hook) : null;

	if (!is_array($_POST['users']))
		$users = explode(',', $_POST['users']);
	else
		$users = array_keys($_POST['users']);

	$users = array_map('intval', $users);

	if (isset($_POST['change_group_comply']))
	{
		$move_to_group = intval($_POST['move_to_group']);

		($hook = get_hook('aus_change_group_form_submitted')) ? eval($hook) : null;

		// We need some information on the group
		$query = array(
			'SELECT'	=> 'g.g_moderator',
			'FROM'		=> 'groups AS g',
			'WHERE'		=> 'g.g_id='.$move_to_group
		);

		($hook = get_hook('aus_change_group_qr_get_group_moderator_status')) ? eval($hook) : null;
		$result = db()->query_build($query) or error(__FILE__, __LINE__);
		$group_is_mod = db()->result($result);

		if ($move_to_group == FORUM_GUEST || (is_null($group_is_mod) || $group_is_mod === false))
			message(__('Bad request'));

		// Move users
		$query = array(
			'UPDATE'	=> 'users',
			'SET'		=> 'group_id='.$move_to_group,
			'WHERE'		=> 'id IN ('.implode(',', $users).') AND id>1'
		);

		($hook = get_hook('aus_change_group_qr_change_user_group')) ? eval($hook) : null;
		$result = db()->query_build($query) or error(__FILE__, __LINE__);

		if ($move_to_group != FORUM_ADMIN && ($group_is_mod !== false && $group_is_mod == '0'))
			clean_forum_moderators();

		($hook = get_hook('aus_change_group_pre_redirect')) ? eval($hook) : null;

		redirect(forum_link($forum_url['admin_users']), __('User groups updated', 'admin_users'));
	}

	// Setup form
	$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array(config()['o_board_title'], forum_link($forum_url['index'])),
		array(__('Forum administration', 'admin_common'), forum_link($forum_url['admin_index'])),
		array(__('Users', 'admin_common'), forum_link($forum_url['admin_users'])),
		array(__('Searches', 'admin_common'), forum_link($forum_url['admin_users'])),
		__('Change group', 'admin_users')
	);

	($hook = get_hook('aus_change_group_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE_SECTION', 'users');
	define('FORUM_PAGE', 'admin-users');

	$query = array(
		'SELECT'	=> 'g.g_id, g.g_title',
		'FROM'		=> 'groups AS g',
		'WHERE'		=> 'g.g_id!='.FORUM_GUEST,
		'ORDER BY'	=> 'g.g_title'
	);
	($hook = get_hook('aus_change_group_qr_get_groups')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);

	$forum_main_view = 'admin/users/change_group';
	include FORUM_ROOT . 'include/render.php';
}


else if (isset($_GET['find_user']))
{
	$form = isset($_GET['form']) ? $_GET['form'] : array();

	// trim() all elements in $form
	$form = array_map('forum_trim', $form);
	$conditions = $query_str = array();

	//Check up for order_by and direction values
	$order_by = isset($_GET['order_by']) ? forum_trim($_GET['order_by']) : null;
	$direction = isset($_GET['direction']) ? forum_trim($_GET['direction']) : null;
	if ($order_by == null || $direction == null)
		message(__('Bad request'));

	if (!in_array($order_by, array('username', 'email', 'num_posts', 'num_posts', 'registered')) || !in_array($direction, array('ASC', 'DESC')))
		message(__('Bad request'));

	($hook = get_hook('aus_find_user_selected')) ? eval($hook) : null;

	$query_str[] = 'order_by='.$order_by;
	$query_str[] = 'direction='.$direction;

	$posts_greater = isset($_GET['posts_greater']) ? forum_trim($_GET['posts_greater']) : '';
	$posts_less = isset($_GET['posts_less']) ? forum_trim($_GET['posts_less']) : '';
	$last_post_after = isset($_GET['last_post_after']) ? forum_trim($_GET['last_post_after']) : '';
	$last_post_before = isset($_GET['last_post_before']) ? forum_trim($_GET['last_post_before']) : '';
	$registered_after = isset($_GET['registered_after']) ? forum_trim($_GET['registered_after']) : '';
	$registered_before = isset($_GET['registered_before']) ? forum_trim($_GET['registered_before']) : '';
	$user_group = isset($_GET['user_group']) ? intval($_GET['user_group']) : -1;

	$query_str[] = 'user_group='.$user_group;

	if ((!empty($posts_greater) || !empty($posts_less)) && !ctype_digit($posts_greater.$posts_less))
		message(__('Non numeric value message', 'admin_users'));

	// Try to convert date/time to timestamps
	if ($last_post_after != '')
	{
		$query_str[] = 'last_post_after='.$last_post_after;

		$last_post_after = strtotime($last_post_after);
		if ($last_post_after === false || $last_post_after == -1)
			message(__('Invalid date/time message', 'admin_users'));

		$conditions[] = 'u.last_post>'.$last_post_after;
	}
	if ($last_post_before != '')
	{
		$query_str[] = 'last_post_before='.$last_post_before;

		$last_post_before = strtotime($last_post_before);
		if ($last_post_before === false || $last_post_before == -1)
			message(__('Invalid date/time message', 'admin_users'));

		$conditions[] = 'u.last_post<'.$last_post_before;
	}
	if ($registered_after != '')
	{
		$query_str[] = 'registered_after='.$registered_after;

		$registered_after = strtotime($registered_after);
		if ($registered_after === false || $registered_after == -1)
			message(__('Invalid date/time message', 'admin_users'));

		$conditions[] = 'u.registered>'.$registered_after;
	}
	if ($registered_before != '')
	{
		$query_str[] = 'registered_before='.$registered_before;

		$registered_before = strtotime($registered_before);
		if ($registered_before === false || $registered_before == -1)
			message(__('Invalid date/time message', 'admin_users'));

		$conditions[] = 'u.registered<'.$registered_before;
	}

	$like_command = ($db_type == 'pgsql') ? 'ILIKE' : 'LIKE';
	foreach ($form as $key => $input)
	{
		if ($input != '' && in_array($key, array('username', 'email', 'title', 'realname', 'url', 'jabber', 'icq', 'msn', 'aim', 'yahoo', 'location', 'signature', 'admin_note')))
		{
			$conditions[] = 'u.'.db()->escape($key).' '.$like_command.' \''.db()->escape(str_replace('*', '%', $input)).'\'';
			$query_str[] = 'form%5B'.$key.'%5D='.urlencode($input);
		}
	}

	if ($posts_greater != '')
	{
		$query_str[] = 'posts_greater='.$posts_greater;
		$conditions[] = 'u.num_posts>'.$posts_greater;
	}
	if ($posts_less != '')
	{
		$query_str[] = 'posts_less='.$posts_less;
		$conditions[] = 'u.num_posts<'.$posts_less;
	}

	if ($user_group > -1)
		$conditions[] = 'u.group_id='.intval($user_group);

	if (empty($conditions))
		message(__('No search terms message', 'admin_users'));

	// Fetch user count
	$query = array(
		'SELECT'	=> 'COUNT(id)',
		'FROM'		=> 'users AS u',
		'JOINS'		=> array(
			array(
				'LEFT JOIN'		=> 'groups AS g',
				'ON'			=> 'g.g_id=u.group_id'
			)
		),
		'WHERE'		=> 'u.id>1 AND '.implode(' AND ', $conditions)
	);

	($hook = get_hook('aus_find_user_qr_count_find_users')) ? eval($hook) : null;

	$result = db()->query_build($query) or error(__FILE__, __LINE__);
	$forum_page['num_users'] = db()->result($result);
	$forum_page['num_pages'] = ceil($forum_page['num_users'] / $forum_user['disp_topics']);
	$forum_page['page'] = (!isset($_GET['p']) || !is_numeric($_GET['p']) || $_GET['p'] <= 1 || $_GET['p'] > $forum_page['num_pages']) ? 1 : $_GET['p'];
	$forum_page['start_from'] = $forum_user['disp_topics'] * ($forum_page['page'] - 1);
	$forum_page['finish_at'] = min(($forum_page['start_from'] + $forum_user['disp_topics']), ($forum_page['num_users']));

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array(config()['o_board_title'], forum_link($forum_url['index'])),
		array(__('Forum administration', 'admin_common'), forum_link($forum_url['admin_index']))
	);
	if ($forum_user['g_id'] == FORUM_ADMIN)
		$forum_page['crumbs'][] = array(__('Users', 'admin_common'), forum_link($forum_url['admin_users']));
	$forum_page['crumbs'][] = array(__('Searches', 'admin_common'), forum_link($forum_url['admin_users']));
	$forum_page['crumbs'][] = __('User search results', 'admin_users');

	// Generate paging
	$forum_page['page_post']['paging'] =
		'<p class="paging"><span class="pages">' .
		__('Pages') . '</span> '.paginate($forum_page['num_pages'], $forum_page['page'], $forum_url['admin_users'].'?find_user=&amp;'.implode('&amp;', $query_str),
			__('Paging separator'), null, true).'</p>';

	($hook = get_hook('aus_find_user_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE_SECTION', 'users');
	define('FORUM_PAGE', 'admin-uresults');

	// Find any users matching the conditions
	$query = array(
		'SELECT'	=> 'u.id, u.username, u.email, u.title, u.num_posts, u.admin_note, g.g_id, g.g_user_title',
		'FROM'		=> 'users AS u',
		'JOINS'		=> array(
			array(
				'LEFT JOIN'		=> 'groups AS g',
				'ON'			=> 'g.g_id=u.group_id'
			)
		),
		'WHERE'		=> 'u.id>1 AND '.implode(' AND ', $conditions),
		'ORDER BY'	=> $order_by.' '.$direction,
		'LIMIT'		=> $forum_page['start_from'].', '.$forum_page['finish_at']
	);
	($hook = get_hook('aus_find_user_qr_find_users')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);

	$forum_main_view = 'admin/users/find';
	include FORUM_ROOT . 'include/render.php';
}


($hook = get_hook('aus_new_action')) ? eval($hook) : null;


// Setup form
$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;

// Setup breadcrumbs
$forum_page['crumbs'] = array(
	array(config()['o_board_title'], forum_link($forum_url['index'])),
	array(__('Forum administration', 'admin_common'), forum_link($forum_url['admin_index']))
);
if ($forum_user['g_id'] == FORUM_ADMIN)
	$forum_page['crumbs'][] = array(__('Users', 'admin_common'), forum_link($forum_url['admin_users']));
$forum_page['crumbs'][] = array(__('Searches', 'admin_common'), forum_link($forum_url['admin_users']));

($hook = get_hook('aus_search_form_pre_header_load')) ? eval($hook) : null;

define('FORUM_PAGE_SECTION', 'users');
define('FORUM_PAGE', 'admin-users');

$query = array(
	'SELECT'	=> 'g.g_id, g.g_title',
	'FROM'		=> 'groups AS g',
	'WHERE'		=> 'g.g_id!='.FORUM_GUEST,
	'ORDER BY'	=> 'g.g_title'
);
($hook = get_hook('aus_search_form_qr_get_groups')) ? eval($hook) : null;
$result = db()->query_build($query) or error(__FILE__, __LINE__);

$forum_main_view = 'admin/users/search';
include FORUM_ROOT . 'include/render.php';
