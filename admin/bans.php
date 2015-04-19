<?php
/**
 * Ban management page.
 *
 * Allows administrators and moderators to create, modify, and delete bans.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */
namespace punbb;

require __DIR__ . '/../vendor/pautoload.php';

($hook = get_hook('aba_start')) ? eval($hook) : null;

if (user()->g_id != FORUM_ADMIN &&
		(user()->g_moderator != '1' || user()->g_mod_ban_users == '0')) {
	message(__('No permission'));
}

// Add/edit a ban (stage 1)
if (isset($_REQUEST['add_ban']) || isset($_GET['edit_ban']))
{
	if (isset($_GET['add_ban']) || isset($_POST['add_ban']))
	{
		// If the id of the user to ban was provided through GET (a link from profile.php)
		if (isset($_GET['add_ban']))
		{
			$add_ban = intval($_GET['add_ban']);
			if ($add_ban < 2)
				message(__('Bad request'));

			$user_id = $add_ban;

			($hook = get_hook('aba_add_ban_selected')) ? eval($hook) : null;

			$query = array(
				'SELECT'	=> 'u.group_id, u.username, u.email, u.registration_ip',
				'FROM'		=> 'users AS u',
				'WHERE'		=> 'u.id='.$user_id
			);

			($hook = get_hook('aba_add_ban_qr_get_user_by_id')) ? eval($hook) : null;
			$result = db()->query_build($query) or error(__FILE__, __LINE__);
			$banned_user_info = db()->fetch_row($result);
			if (!$banned_user_info)
			{
				message(__('No user id message', 'admin_bans'));
			}

			list($group_id, $ban_user, $ban_email, $ban_ip) = $banned_user_info;
		}
		else	// Otherwise the username is in POST
		{
			$ban_user = forum_trim($_POST['new_ban_user']);

			($hook = get_hook('aba_add_ban_form_submitted')) ? eval($hook) : null;

			if ($ban_user != '')
			{
				$query = array(
					'SELECT'	=> 'u.id, u.group_id, u.username, u.email, u.registration_ip',
					'FROM'		=> 'users AS u',
					'WHERE'		=> 'u.username=\''.db()->escape($ban_user).'\' AND u.id>1'
				);

				($hook = get_hook('aba_add_ban_qr_get_user_by_username')) ? eval($hook) : null;
				$result = db()->query_build($query) or error(__FILE__, __LINE__);
				$banned_user_info = db()->fetch_row($result);
				if (!$banned_user_info)
				{
					message(__('No user username message', 'admin_bans'));
				}

				list($user_id, $group_id, $ban_user, $ban_email, $ban_ip) = $banned_user_info;
			}
		}

		// Make sure we're not banning an admin
		if (isset($group_id) && $group_id == FORUM_ADMIN)
			message(__('User is admin message', 'admin_bans'));

		// If we have a $user_id, we can try to find the last known IP of that user
		if (isset($user_id))
		{
			$query = array(
				'SELECT'	=> 'p.poster_ip',
				'FROM'		=> 'posts AS p',
				'WHERE'		=> 'p.poster_id='.$user_id,
				'ORDER BY'	=> 'p.posted DESC',
				'LIMIT'		=> '1'
			);

			($hook = get_hook('aba_add_ban_qr_get_last_known_ip')) ? eval($hook) : null;
			$result = db()->query_build($query) or error(__FILE__, __LINE__);
			$ban_ip_from_db = db()->result($result);

			if ($ban_ip_from_db)
			{
				$ban_ip = $ban_ip_from_db;
			}
		}

		$mode = 'add';
	}
	else	// We are editing a ban
	{
		$ban_id = intval($_GET['edit_ban']);
		if ($ban_id < 1)
			message(__('Bad request'));

		($hook = get_hook('aba_edit_ban_selected')) ? eval($hook) : null;

		$query = array(
			'SELECT'	=> 'b.username, b.ip, b.email, b.message, b.expire',
			'FROM'		=> 'bans AS b',
			'WHERE'		=> 'b.id='.$ban_id
		);

		($hook = get_hook('aba_edit_ban_qr_get_ban_data')) ? eval($hook) : null;
		$result = db()->query_build($query) or error(__FILE__, __LINE__);
		$banned_user_info = db()->fetch_row($result);

		if (!$banned_user_info)
		{
			message(__('Bad request'));
		}

		list($ban_user, $ban_ip, $ban_email, $ban_message, $ban_expire) = $banned_user_info;

		// We just use GMT for expire dates, as its a date rather than a day I don't think its worth worrying about
		$ban_expire = ($ban_expire != '') ? gmdate('Y-m-d', $ban_expire) : '';

		$mode = 'edit';
	}


	// Setup the form
	$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array(config()->o_board_title, forum_link($forum_url['index'])),
		array(__('Forum administration', 'admin_common'), forum_link($forum_url['admin_index']))
	);
	if (user()->g_id == FORUM_ADMIN) {
		$forum_page['crumbs'][] = array(__('Users', 'admin_common'), forum_link($forum_url['admin_users']));
	}
	$forum_page['crumbs'][] = array(__('Bans', 'admin_common'), forum_link($forum_url['admin_bans']));
	$forum_page['crumbs'][] = __('Ban advanced', 'admin_bans');

	($hook = get_hook('aba_add_edit_ban_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE_SECTION', 'users');
	define('FORUM_PAGE', 'admin-bans');

	$forum_main_view = 'admin/bans/edit';
	template()->render($forum_layout);
}


// Add/edit a ban (stage 2)
else if (isset($_POST['add_edit_ban']))
{
	$ban_user = forum_trim($_POST['ban_user']);
	$ban_ip = forum_trim($_POST['ban_ip']);
	$ban_email = strtolower(forum_trim($_POST['ban_email']));
	$ban_message = forum_trim($_POST['ban_message']);
	$ban_expire = forum_trim($_POST['ban_expire']);

	if ($ban_user == '' && $ban_ip == '' && $ban_email == '')
		message(__('Must enter message', 'admin_bans'));
	else if (strtolower($ban_user) == 'guest')
		message(__('Can\'t ban guest user', 'admin_bans'));

	($hook = get_hook('aba_add_edit_ban_form_submitted')) ? eval($hook) : null;

	// Validate IP/IP range (it's overkill, I know)
	if ($ban_ip != '')
	{
		$ban_ip = preg_replace('/[\s]{2,}/', ' ', $ban_ip);
		$addresses = explode(' ', $ban_ip);
		$addresses = array_map('trim', $addresses);

		for ($i = 0; $i < count($addresses); ++$i)
		{
			if (strpos($addresses[$i], ':') !== false)
			{
				$octets = explode(':', $addresses[$i]);


				for ($c = 0; $c < count($octets); ++$c)
				{

					$octets[$c] = ltrim($octets[$c], "0");

					if ($c > 7 || (!empty($octets[$c]) && !ctype_xdigit($octets[$c])) || intval($octets[$c], 16) > 65535)
						message(__('Invalid IP message', 'admin_bans'));
				}

				$cur_address = implode(':', $octets);
				$addresses[$i] = $cur_address;
			}
			else
			{
				$octets = explode('.', $addresses[$i]);

				for ($c = 0; $c < count($octets); ++$c)
				{

					$octets[$c] = (strlen($octets[$c]) > 1) ? ltrim($octets[$c], "0") : $octets[$c];

					if ($c > 3 || !ctype_digit($octets[$c]) || intval($octets[$c]) > 255)
						message(__('Invalid IP message', 'admin_bans'));
				}

				$cur_address = implode('.', $octets);
				$addresses[$i] = $cur_address;
			}
		}

		$ban_ip = implode(' ', $addresses);
	}

	if (!defined('FORUM_EMAIL_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/email.php';

	if ($ban_email != '' && !is_valid_email($ban_email))
	{
		if (!preg_match('/^[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/', $ban_email))
			message(__('Invalid e-mail message', 'admin_bans'));
	}

	if ($ban_expire != '' && $ban_expire != 'Never')
	{
		$ban_expire = strtotime($ban_expire);

		if ($ban_expire == -1 || $ban_expire <= time())
			message(__('Invalid expire message', 'admin_bans'));
	}
	else
		$ban_expire = 'NULL';

	$ban_user = ($ban_user != '') ? '\''.db()->escape($ban_user).'\'' : 'NULL';
	$ban_ip = ($ban_ip != '') ? '\''.db()->escape($ban_ip).'\'' : 'NULL';
	$ban_email = ($ban_email != '') ? '\''.db()->escape($ban_email).'\'' : 'NULL';
	$ban_message = ($ban_message != '') ? '\''.db()->escape($ban_message).'\'' : 'NULL';

	if ($_POST['mode'] == 'add')
	{
		$query = array(
			'INSERT'	=> 'username, ip, email, message, expire, ban_creator',
			'INTO'		=> 'bans',
			'VALUES'	=> $ban_user.', '.$ban_ip.', '.$ban_email.', '.$ban_message.', '.$ban_expire.', '.user()->id
		);

		($hook = get_hook('aba_add_edit_ban_qr_add_ban')) ? eval($hook) : null;
		db()->query_build($query) or error(__FILE__, __LINE__);
	}
	else
	{
		$query = array(
			'UPDATE'	=> 'bans',
			'SET'		=> 'username='.$ban_user.', ip='.$ban_ip.', email='.$ban_email.', message='.$ban_message.', expire='.$ban_expire,
			'WHERE'		=> 'id='.intval($_POST['ban_id'])
		);

		($hook = get_hook('aba_qr_update_ban')) ? eval($hook) : null;
		db()->query_build($query) or error(__FILE__, __LINE__);
	}

	// Regenerate the bans cache
	require FORUM_ROOT . 'include/cache.php';
	generate_bans_cache();

	flash()->add_info((($_POST['mode'] == 'edit') ?
		__('Ban edited', 'admin_bans') : __('Ban added', 'admin_bans')));

	($hook = get_hook('aba_add_edit_ban_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['admin_bans']), (($_POST['mode'] == 'edit') ?
		__('Ban edited', 'admin_bans') : __('Ban added', 'admin_bans')));
}


// Remove a ban
else if (isset($_GET['del_ban']))
{
	$ban_id = intval($_GET['del_ban']);
	if ($ban_id < 1)
		message(__('Bad request'));

	// Validate the CSRF token
	if (!isset($_POST['csrf_token']) && (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== generate_form_token('del_ban'.$ban_id)))
		csrf_confirm_form();

	($hook = get_hook('aba_del_ban_form_submitted')) ? eval($hook) : null;

	$query = array(
		'DELETE'	=> 'bans',
		'WHERE'		=> 'id='.$ban_id
	);

	($hook = get_hook('aba_del_ban_qr_delete_ban')) ? eval($hook) : null;
	db()->query_build($query) or error(__FILE__, __LINE__);

	// Regenerate the bans cache
	require FORUM_ROOT . 'include/cache.php';
	generate_bans_cache();

	flash()->add_info(__('Ban removed', 'admin_bans'));

	($hook = get_hook('aba_del_ban_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['admin_bans']), __('Ban removed', 'admin_bans'));
}


// Setup the form
$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;
$forum_page['form_action'] = forum_link($forum_url['admin_bans']).'&amp;action=more';

$forum_page['hidden_fields'] = array(
	'csrf_token'	=> '<input type="hidden" name="csrf_token" value="'.generate_form_token($forum_page['form_action']).'" />'
);

// Setup breadcrumbs
$forum_page['crumbs'] = array(
	array(config()->o_board_title, forum_link($forum_url['index'])),
	array(__('Forum administration', 'admin_common'), forum_link($forum_url['admin_index']))
);
if (user()->g_id == FORUM_ADMIN) {
	$forum_page['crumbs'][] = array(__('Users', 'admin_common'), forum_link($forum_url['admin_users']));
}
$forum_page['crumbs'][] = array(__('Bans', 'admin_common'), forum_link($forum_url['admin_bans']));


// Fetch user count
$query = array(
	'SELECT'	=>	'COUNT(id)',
	'FROM'		=>	'bans'
);

$result = db()->query_build($query) or error(__FILE__, __LINE__);
$forum_page['num_bans'] = db()->result($result);
$forum_page['num_pages'] = ceil($forum_page['num_bans'] / user()->disp_topics);
$forum_page['page'] = (!isset($_GET['p']) || !is_numeric($_GET['p']) || $_GET['p'] <= 1 || $_GET['p'] > $forum_page['num_pages']) ? 1 : intval($_GET['p']);
$forum_page['start_from'] = user()->disp_topics * ($forum_page['page'] - 1);
$forum_page['finish_at'] = min(($forum_page['start_from'] + user()->disp_topics), ($forum_page['num_bans']));

// Generate paging
$forum_page['page_post']['paging'] =
	'<p class="paging"><span class="pages">' . __('Pages') .
	'</span> '.paginate($forum_page['num_pages'], $forum_page['page'], $forum_url['admin_bans'],
		__('Paging separator'), null, true).'</p>';

// Navigation links for header and page numbering for title/meta description
if ($forum_page['page'] < $forum_page['num_pages'])
{
	$forum_page['nav']['last'] =
		'<link rel="last" href="'.forum_sublink($forum_url['admin_bans'], $forum_url['page'], $forum_page['num_pages']).'" title="'.
		__('Page') . ' '.$forum_page['num_pages'].'" />';
	$forum_page['nav']['next'] =
		'<link rel="next" href="'.forum_sublink($forum_url['admin_bans'], $forum_url['page'], ($forum_page['page'] + 1)).'" title="'.
		__('Page').' '.($forum_page['page'] + 1).'" />';
}
if ($forum_page['page'] > 1)
{
	$forum_page['nav']['prev'] = '<link rel="prev" href="'.forum_sublink($forum_url['admin_bans'], $forum_url['page'], ($forum_page['page'] - 1)).'" title="'.
	__('Page') . ' '.($forum_page['page'] - 1).'" />';
	$forum_page['nav']['first'] = '<link rel="first" href="'.forum_link($forum_url['admin_bans']).'" title="'.
	__('Page').' 1" />';
}

($hook = get_hook('aba_pre_header_load')) ? eval($hook) : null;

define('FORUM_PAGE_SECTION', 'users');
define('FORUM_PAGE', 'admin-bans');

if ($forum_page['num_bans'] > 0) {
	// Grab the bans
	$query = array(
		'SELECT'	=> 'b.*, u.username AS ban_creator_username',
		'FROM'		=> 'bans AS b',
		'JOINS'		=> array(
			array(
				'LEFT JOIN'		=> 'users AS u',
				'ON'			=> 'u.id=b.ban_creator'
			)
		),
		'ORDER BY'	=> 'b.id',
		'LIMIT'		=> $forum_page['start_from'].', '.$forum_page['finish_at']
	);

	$result = db()->query_build($query) or error(__FILE__, __LINE__);
}

$forum_main_view = 'admin/bans/main';
template()->render($forum_layout);
