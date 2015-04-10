<?php
/**
 * Handles logins, logouts, and password reset requests.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */
namespace punbb;

if (isset($_GET['action']))
	define('FORUM_QUIET_VISIT', 1);

require __DIR__ . '/vendor/pautoload.php';

($hook = get_hook('li_start')) ? eval($hook) : null;

$action = isset($_GET['action']) ? $_GET['action'] : null;
$errors = array();

// Login
if (isset($_POST['form_sent']) && empty($action))
{
	$form_username = forum_trim($_POST['req_username']);
	$form_password = forum_trim($_POST['req_password']);
	$save_pass = isset($_POST['save_pass']);

	($hook = get_hook('li_login_form_submitted')) ? eval($hook) : null;

	// Get user info matching login attempt
	$query = array(
		'SELECT'	=> 'u.id, u.group_id, u.password, u.salt',
		'FROM'		=> 'users AS u'
	);

	if (in_array($db_type, array('mysql', 'mysqli', 'mysql_innodb', 'mysqli_innodb')))
		$query['WHERE'] = 'username=\''.db()->escape($form_username).'\'';
	else
		$query['WHERE'] = 'LOWER(username)=LOWER(\''.db()->escape($form_username).'\')';

	($hook = get_hook('li_login_qr_get_login_data')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);
	list($user_id, $group_id, $db_password_hash, $salt) = db()->fetch_row($result);

	$authorized = false;
	if (!empty($db_password_hash))
	{
		$sha1_in_db = (strlen($db_password_hash) == 40) ? true : false;
		$form_password_hash = forum_hash($form_password, $salt);

		if ($sha1_in_db && $db_password_hash == $form_password_hash)
			$authorized = true;
		else if ((!$sha1_in_db && $db_password_hash == md5($form_password)) || ($sha1_in_db && $db_password_hash == sha1($form_password)))
		{
			$authorized = true;

			$salt = random_key(12);
			$form_password_hash = forum_hash($form_password, $salt);

			// There's an old MD5 hash or an unsalted SHA1 hash in the database, so we replace it
			// with a randomly generated salt and a new, salted SHA1 hash
			$query = array(
				'UPDATE'	=> 'users',
				'SET'		=> 'password=\''.$form_password_hash.'\', salt=\''.db()->escape($salt).'\'',
				'WHERE'		=> 'id='.$user_id
			);

			($hook = get_hook('li_login_qr_update_user_hash')) ? eval($hook) : null;
			db()->query_build($query) or error(__FILE__, __LINE__);
		}
	}

	($hook = get_hook('li_login_pre_auth_message')) ? eval($hook) : null;

	if (!$authorized)
		$errors[] = sprintf(__('Wrong user/pass', 'login'));

	// Did everything go according to plan?
	if (empty($errors))
	{
		// Update the status if this is the first time the user logged in
		if ($group_id == FORUM_UNVERIFIED)
		{
			$query = array(
				'UPDATE'	=> 'users',
				'SET'		=> 'group_id='.config()['o_default_user_group'],
				'WHERE'		=> 'id='.$user_id
			);

			($hook = get_hook('li_login_qr_update_user_group')) ? eval($hook) : null;
			db()->query_build($query) or error(__FILE__, __LINE__);

			// Remove cache file with forum stats
			if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
			{
				require FORUM_ROOT.'include/cache.php';
			}

			clean_stats_cache();
		}

		// Remove this user's guest entry from the online list
		$query = array(
			'DELETE'	=> 'online',
			'WHERE'		=> 'ident=\''.db()->escape(get_remote_address()).'\''
		);

		($hook = get_hook('li_login_qr_delete_online_user')) ? eval($hook) : null;
		db()->query_build($query) or error(__FILE__, __LINE__);

		$expire = ($save_pass) ? time() + 1209600 : time() + config()['o_timeout_visit'];
		forum_setcookie($cookie_name, base64_encode($user_id.'|'.$form_password_hash.'|'.$expire.'|'.sha1($salt.$form_password_hash.forum_hash($expire, $salt))), $expire);

		($hook = get_hook('li_login_pre_redirect')) ? eval($hook) : null;

		redirect(forum_htmlencode($_POST['redirect_url']).((substr_count($_POST['redirect_url'], '?') == 1) ? '&amp;' : '?').'login=1',
			__('Login redirect', 'login'));
	}
}


// Logout
else if ($action == 'out')
{
	if (user()['is_guest'] || !isset($_GET['id']) || $_GET['id'] != user()['id'])
	{
		header('Location: '.forum_link($forum_url['index']));
		exit;
	}

	// We validate the CSRF token. If it's set in POST and we're at this point, the token is valid.
	// If it's in GET, we need to make sure it's valid.
	if (!isset($_POST['csrf_token']) && (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== generate_form_token('logout'.user()['id'])))
		csrf_confirm_form();

	($hook = get_hook('li_logout_selected')) ? eval($hook) : null;

	// Remove user from "users online" list.
	$query = array(
		'DELETE'	=> 'online',
		'WHERE'		=> 'user_id='.user()['id']
	);

	($hook = get_hook('li_logout_qr_delete_online_user')) ? eval($hook) : null;
	db()->query_build($query) or error(__FILE__, __LINE__);

	// Update last_visit (make sure there's something to update it with)
	if (isset(user()['logged']))
	{
		$query = array(
			'UPDATE'	=> 'users',
			'SET'		=> 'last_visit='.user()['logged'],
			'WHERE'		=> 'id='.user()['id']
		);

		($hook = get_hook('li_logout_qr_update_last_visit')) ? eval($hook) : null;
		db()->query_build($query) or error(__FILE__, __LINE__);
	}

	$expire = time() + 1209600;
	forum_setcookie($cookie_name, base64_encode('1|'.random_key(8, false, true).'|'.$expire.'|'.random_key(8, false, true)), $expire);

	// Reset tracked topics
	set_tracked_topics(null);

	($hook = get_hook('li_logout_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['index']), __('Logout redirect', 'login'));
}


// New password
else if ($action == 'forget' || $action == 'forget_2')
{
	if (!user()['is_guest'])
		header('Location: '.forum_link($forum_url['index']));

	($hook = get_hook('li_forgot_pass_selected')) ? eval($hook) : null;

	if (isset($_POST['form_sent']))
	{
		// User pressed the cancel button
		if (isset($_POST['cancel']))
			redirect(forum_link($forum_url['index']), __('New password cancel redirect', 'login'));

		if (!defined('FORUM_EMAIL_FUNCTIONS_LOADED'))
			require FORUM_ROOT.'include/email.php';

		// Validate the email-address
		$email = strtolower(forum_trim($_POST['req_email']));
		if (!is_valid_email($email))
			$errors[] = __('Invalid e-mail', 'login');

		($hook = get_hook('li_forgot_pass_end_validation')) ? eval($hook) : null;

		// Did everything go according to plan?
		if (empty($errors))
		{
			$users_with_email = array();

			// Fetch user matching $email
			$query = array(
				'SELECT'	=> 'u.id, u.group_id, u.username, u.salt, u.last_email_sent',
				'FROM'		=> 'users AS u',
				'WHERE'		=> 'u.email=\''.db()->escape($email).'\''
			);

			($hook = get_hook('li_forgot_pass_qr_get_user_data')) ? eval($hook) : null;
			$result = db()->query_build($query) or error(__FILE__, __LINE__);

			while ($cur_user = db()->fetch_assoc($result))
			{
				$users_with_email[] = $cur_user;
			}

			if (!empty($users_with_email))
			{
				($hook = get_hook('li_forgot_pass_pre_email')) ? eval($hook) : null;

				// Load the "activate password" template
				$mail_tpl = forum_trim(file_get_contents(FORUM_ROOT.'lang/'.user()['language'].'/mail_templates/activate_password.tpl'));

				// The first row contains the subject
				$first_crlf = strpos($mail_tpl, "\n");
				$mail_subject = forum_trim(substr($mail_tpl, 8, $first_crlf-8));
				$mail_message = forum_trim(substr($mail_tpl, $first_crlf));

				// Do the generic replacements first (they apply to all e-mails sent out here)
				$mail_message = str_replace('<base_url>', $base_url.'/', $mail_message);
				$mail_message = str_replace('<board_mailer>', sprintf(__('Forum mailer'), config()['o_board_title']), $mail_message);

				($hook = get_hook('li_forgot_pass_new_general_replace_data')) ? eval($hook) : null;

				// Loop through users we found
				foreach ($users_with_email as $cur_hit)
				{
					$forgot_pass_timeout = 3600;

					($hook = get_hook('li_forgot_pass_pre_flood_check')) ? eval($hook) : null;

					if ($cur_hit['group_id'] == FORUM_ADMIN)
						message(sprintf(__('Email important', 'login'), '<a href="mailto:'.forum_htmlencode(config()['o_admin_email']).'">'.forum_htmlencode(config()['o_admin_email']).'</a>'));

					if ($cur_hit['last_email_sent'] != '' && (time() - $cur_hit['last_email_sent']) < $forgot_pass_timeout && (time() - $cur_hit['last_email_sent']) >= 0)
						message(sprintf(__('Email flood', 'login'), $forgot_pass_timeout));

					// Generate a new password activation key
					$new_password_key = random_key(8, true);

					$query = array(
						'UPDATE'	=> 'users',
						'SET'		=> 'activate_key=\''.$new_password_key.'\', last_email_sent = '.time(),
						'WHERE'		=> 'id='.$cur_hit['id']
					);

					($hook = get_hook('li_forgot_pass_qr_set_activate_key')) ? eval($hook) : null;
					db()->query_build($query) or error(__FILE__, __LINE__);

					// Do the user specific replacements to the template
					$cur_mail_message = str_replace('<username>', $cur_hit['username'], $mail_message);
					$cur_mail_message = str_replace('<activation_url>', str_replace('&amp;', '&', forum_link($forum_url['change_password_key'], array($cur_hit['id'], $new_password_key))), $cur_mail_message);

					($hook = get_hook('li_forgot_pass_new_user_replace_data')) ? eval($hook) : null;

					forum_mail($email, $mail_subject, $cur_mail_message);
				}

				message(sprintf(__('Forget mail', 'login'), '<a href="mailto:'.forum_htmlencode(config()['o_admin_email']).'">'.forum_htmlencode(config()['o_admin_email']).'</a>'));
			}
			else
				$errors[] = sprintf(__('No e-mail match', 'login'), forum_htmlencode($email));
		}
	}

	// Setup form
	$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;
	$forum_page['form_action'] = forum_link($forum_url['request_password']);

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array(config()['o_board_title'], forum_link($forum_url['index'])),
		__('New password request', 'login')
	);

	($hook = get_hook('li_forgot_pass_pre_header_load')) ? eval($hook) : null;

	define ('FORUM_PAGE', 'reqpass');

	$forum_main_view = 'login/reqpass';
	include FORUM_ROOT . 'include/render.php';
}

if (!user()['is_guest'])
	header('Location: '.forum_link($forum_url['index']));

// Setup form
$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;
$forum_page['form_action'] = forum_link($forum_url['login']);

$forum_page['hidden_fields'] = array(
	'form_sent'		=> '<input type="hidden" name="form_sent" value="1" />',
	'redirect_url'	=> '<input type="hidden" name="redirect_url" value="'.forum_htmlencode(user()['prev_url']).'" />',
	'csrf_token'	=> '<input type="hidden" name="csrf_token" value="'.generate_form_token($forum_page['form_action']).'" />'
);

// Setup breadcrumbs
$forum_page['crumbs'] = array(
	array(config()['o_board_title'], forum_link($forum_url['index'])),
	sprintf(__('Login info', 'login'), config()['o_board_title'])
);

($hook = get_hook('li_login_pre_header_load')) ? eval($hook) : null;

define('FORUM_PAGE', 'login');

$forum_main_view = 'login/main';
include FORUM_ROOT . 'include/render.php';
