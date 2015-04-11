<?php
/**
 * Allows the creation of new user accounts.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */
namespace punbb;

require __DIR__ . '/vendor/pautoload.php';

($hook = get_hook('rg_start')) ? eval($hook) : null;

// If we are logged in, we shouldn't be here
if (!user()['is_guest'])
{
	header('Location: '.forum_link($forum_url['index']));
	exit;
}

if (config()->o_regs_allow == '0')
	message(__('No new regs', 'profile'));

$errors = array();


// User pressed the cancel button
if (isset($_GET['cancel']))
	redirect(forum_link($forum_url['index']), __('Reg cancel redirect', 'profile'));

// User pressed agree but failed to tick checkbox
else if (isset($_GET['agree']) && !isset($_GET['req_agreement']))
	redirect(forum_link($forum_url['index']), __('Reg cancel redirect', 'profile'));

// Show the rules
else if (config()->o_rules == '1' && !isset($_GET['agree']) && !isset($_POST['form_sent']))
{
	// Setup form
	$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array(config()->o_board_title, forum_link($forum_url['index'])),
		array(__('Register'), forum_link($forum_url['register'])),
		__('Rules')
	);

	($hook = get_hook('rg_rules_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE', 'rules-register');

	$forum_main_view = 'register/rules';
	include FORUM_ROOT . 'include/render.php';
}

else if (isset($_POST['form_sent']))
{
	($hook = get_hook('rg_register_form_submitted')) ? eval($hook) : null;

	// Check that someone from this IP didn't register a user within the last hour (DoS prevention)
	$query = array(
		'SELECT'	=> 'COUNT(u.id)',
		'FROM'		=> 'users AS u',
		'WHERE'		=> 'u.registration_ip=\''.db()->escape(get_remote_address()).'\' AND u.registered>'.(time() - 3600)
	);

	($hook = get_hook('rg_register_qr_check_register_flood')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);
	if (db()->result($result) > 0)
	{
		$errors[] = __('Registration flood', 'profile');
	}

	// Did everything go according to plan so far?
	if (empty($errors))
	{
		$username = forum_trim($_POST['req_username']);
		$email1 = strtolower(forum_trim($_POST['req_email1']));

		if (config()->o_regs_verify == '1')
		{
			$password1 = random_key(8, true);
			$password2 = $password1;
		}
		else
		{
			$password1 = forum_trim($_POST['req_password1']);
			$password2 = (config()->o_mask_passwords == '1') ? forum_trim($_POST['req_password2']) : $password1;
		}

		// Validate the username
		$errors = array_merge($errors, validate_username($username));

		// ... and the password
		if (utf8_strlen($password1) < 4)
			$errors[] = __('Pass too short', 'profile');
		else if ($password1 != $password2)
			$errors[] = __('Pass not match', 'profile');

		// ... and the e-mail address
		if (!defined('FORUM_EMAIL_FUNCTIONS_LOADED'))
			require FORUM_ROOT.'include/email.php';

		if (!is_valid_email($email1))
			$errors[] = __('Invalid e-mail', 'profile');

		// Check if it's a banned e-mail address
		$banned_email = is_banned_email($email1);
		if ($banned_email && config()->p_allow_banned_email == '0')
			$errors[] = __('Banned e-mail', 'profile');

		// Clean old unverified registrators - delete older than 72 hours
		$query = array(
			'DELETE'	=> 'users',
			'WHERE'		=> 'group_id='.FORUM_UNVERIFIED.' AND activate_key IS NOT NULL AND registered < '.(time() - 259200)
		);
		($hook = get_hook('rg_register_qr_delete_unverified')) ? eval($hook) : null;
		db()->query_build($query) or error(__FILE__, __LINE__);

		// Check if someone else already has registered with that e-mail address
		$dupe_list = array();

		$query = array(
			'SELECT'	=> 'u.username',
			'FROM'		=> 'users AS u',
			'WHERE'		=> 'u.email=\''.db()->escape($email1).'\''
		);

		($hook = get_hook('rg_register_qr_check_email_dupe')) ? eval($hook) : null;
		$result = db()->query_build($query) or error(__FILE__, __LINE__);

		while ($cur_dupe = db()->fetch_assoc($result))
		{
			$dupe_list[] = $cur_dupe['username'];
		}

		if (!empty($dupe_list) && empty($errors))
		{
			if (config()->p_allow_dupe_email == '0')
				$errors[] = __('Dupe e-mail', 'profile');
		}

		($hook = get_hook('rg_register_end_validation')) ? eval($hook) : null;

		// Did everything go according to plan so far?
		if (empty($errors))
		{
			// Make sure we got a valid language string
			if (isset($_POST['language']))
			{
				$language = preg_replace('#[\.\\\/]#', '', $_POST['language']);
				if (!file_exists(FORUM_ROOT.'lang/'.$language.'/common.php'))
					message(__('Bad request'));
			}
			else
				$language = config()->o_default_lang;

			$initial_group_id = (config()->o_regs_verify == '0') ?
				config()->o_default_user_group : FORUM_UNVERIFIED;
			$salt = random_key(12);
			$password_hash = forum_hash($password1, $salt);

			// Validate timezone and DST
			$timezone = (isset($_POST['timezone'])) ? floatval($_POST['timezone']) : config()->o_default_timezone;

			// Validate timezone — on error use default value
			if (($timezone > 14.0) || ($timezone < -12.0)) {
				$timezone = config()->o_default_timezone;
			}

			// DST
			$dst = (isset($_POST['dst']) && intval($_POST['dst']) === 1) ?
				1 : config()->o_default_dst;


			// Insert the new user into the database. We do this now to get the last inserted id for later use.
			$user_info = array(
				'username'				=>	$username,
				'group_id'				=>	$initial_group_id,
				'salt'					=>	$salt,
				'password'				=>	$password1,
				'password_hash'			=>	$password_hash,
				'email'					=>	$email1,
				'email_setting'			=>	config()->o_default_email_setting,
				'timezone'				=>	$timezone,
				'dst'					=>	$dst,
				'language'				=>	$language,
				'style'					=>	config()->o_default_style,
				'registered'			=>	time(),
				'registration_ip'		=>	get_remote_address(),
				'activate_key'			=>	(config()->o_regs_verify == '1') ? '\''.random_key(8, true).'\'' : 'NULL',
				'require_verification'	=>	(config()->o_regs_verify == '1'),
				'notify_admins'			=>	(config()->o_regs_report == '1')
			);

			($hook = get_hook('rg_register_pre_add_user')) ? eval($hook) : null;
			add_user($user_info, $new_uid);

			// If we previously found out that the e-mail was banned
			if ($banned_email && config()->o_mailing_list != '')
			{
				$mail_subject = 'Alert - Banned e-mail detected';
				$mail_message = 'User \''.$username.'\' registered with banned e-mail address: '.$email1."\n\n".'User profile: '.forum_link($forum_url['user'], $new_uid)."\n\n".'-- '."\n".'Forum Mailer'."\n".'(Do not reply to this message)';

				($hook = get_hook('rg_register_banned_email')) ? eval($hook) : null;

				forum_mail(config()->o_mailing_list, $mail_subject, $mail_message);
			}

			// If we previously found out that the e-mail was a dupe
			if (!empty($dupe_list) && config()->o_mailing_list != '')
			{
				$mail_subject = 'Alert - Duplicate e-mail detected';
				$mail_message = 'User \''.$username.'\' registered with an e-mail address that also belongs to: '.implode(', ', $dupe_list)."\n\n".'User profile: '.forum_link($forum_url['user'], $new_uid)."\n\n".'-- '."\n".'Forum Mailer'."\n".'(Do not reply to this message)';

				($hook = get_hook('rg_register_dupe_email')) ? eval($hook) : null;

				forum_mail(config()->o_mailing_list, $mail_subject, $mail_message);
			}

			($hook = get_hook('rg_register_pre_login_redirect')) ? eval($hook) : null;

			// Must the user verify the registration or do we log him/her in right now?
			if (config()->o_regs_verify == '1')
			{
				message(sprintf(__('Reg e-mail', 'profile'), '<a href="mailto:'.forum_htmlencode(config()->o_admin_email).'">'.forum_htmlencode(config()->o_admin_email).'</a>'));
			}
			else
			{
				// Remove cache file with forum stats
				require FORUM_ROOT . 'include/cache.php';
				clean_stats_cache();
			}

			$expire = time() + config()->o_timeout_visit;

			forum_setcookie($cookie_name, base64_encode($new_uid.'|'.$password_hash.'|'.$expire.'|'.sha1($salt.$password_hash.forum_hash($expire, $salt))), $expire);

			redirect(forum_link($forum_url['index']), __('Reg complete', 'profile'));
		}
	}
}

// Setup form
$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;
$forum_page['form_action'] = forum_link($forum_url['register']).'?action=register';

// Setup form information
$forum_page['frm_info'] = array();
if (config()->o_regs_verify != '0')
	$forum_page['frm_info']['email'] = '<p class="warn">'.
		__('Reg e-mail info', 'profile') . '</p>';

// Setup breadcrumbs
$forum_page['crumbs'] = array(
	array(config()->o_board_title, forum_link($forum_url['index'])),
	sprintf(__('Register at', 'profile'), config()->o_board_title)
);

// Load JS for timezone detection
assets()->add_js($base_url.'/include/js/min/punbb.timezone.min.js');
assets()->add_js('PUNBB.timezone.detect_on_register_form();', array('type' => 'inline'));


($hook = get_hook('rg_register_pre_header_load')) ? eval($hook) : null;

define('FORUM_PAGE', 'register');

$forum_main_view = 'register/main';
include FORUM_ROOT . 'include/render.php';
