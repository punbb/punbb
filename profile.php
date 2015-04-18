<?php
/**
 * Allows users to view and edit their details.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */
namespace punbb;

require __DIR__ . '/vendor/pautoload.php';

($hook = get_hook('pf_start')) ? eval($hook) : null;

$action = isset($_GET['action']) ? $_GET['action'] : null;
$section = isset($_GET['section']) ? $_GET['section'] : 'about';	// Default to section "about"
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id < 2)
	message(__('Bad request'));

$errors = array();

if ($action != 'change_pass' || !isset($_GET['key']))
{
	if (user()->g_read_board == '0') {
		message(__('No view'));
	}
	else if (user()->g_view_users == '0' && (user()->is_guest || user()->id != $id)) {
		message(__('No permission'));
	}
}

// Fetch info about the user whose profile we're viewing
$query = array(
	'SELECT'	=> 'u.*, g.g_id, g.g_user_title, g.g_moderator',
	'FROM'		=> 'users AS u',
	'JOINS'		=> array(
		array(
			'LEFT JOIN'	=> 'groups AS g',
			'ON'		=> 'g.g_id=u.group_id'
		)
	),
	'WHERE'		=> 'u.id='.$id
);

($hook = get_hook('pf_qr_get_user_info')) ? eval($hook) : null;
$result = db()->query_build($query) or error(__FILE__, __LINE__);
$user = db()->fetch_assoc($result);

if (!$user)
	message(__('Bad request'));


if ($action == 'change_pass')
{
	($hook = get_hook('pf_change_pass_selected')) ? eval($hook) : null;

	// User pressed the cancel button
	if (isset($_POST['cancel']))
		redirect(forum_link($forum_url['profile_about'], $id), __('Cancel redirect'));

	if (isset($_GET['key']))
	{
		$key = $_GET['key'];

		// If the user is already logged in we shouldn't be here :)
		if (!user()->is_guest) {
			message(__('Pass logout', 'profile'));
		}

		($hook = get_hook('pf_change_pass_key_supplied')) ? eval($hook) : null;

		if ($key == '' || $key != $user['activate_key'])
			message(sprintf(__('Pass key bad', 'profile'), '<a href="mailto:'.
				forum_htmlencode(config()->o_admin_email).'">'.forum_htmlencode(config()->o_admin_email).'</a>'));
		else
		{
			if (isset($_POST['form_sent']))
			{
				($hook = get_hook('pf_change_pass_key_form_submitted')) ? eval($hook) : null;

				$new_password1 = forum_trim($_POST['req_new_password1']);
				$new_password2 = (config()->o_mask_passwords == '1') ? forum_trim($_POST['req_new_password2']) : $new_password1;

				if (utf8_strlen($new_password1) < 4)
					$errors[] = __('Pass too short', 'profile');
				else if ($new_password1 != $new_password2)
					$errors[] = __('Pass not match', 'profile');

				// Did everything go according to plan?
				if (empty($errors))
				{
					$new_password_hash = forum_hash($new_password1, $user['salt']);

					$query = array(
						'UPDATE'	=> 'users',
						'SET'		=> 'password=\''.$new_password_hash.'\', activate_key=NULL',
						'WHERE'		=> 'id='.$id
					);

					($hook = get_hook('pf_change_pass_key_qr_update_password')) ? eval($hook) : null;
					db()->query_build($query) or error(__FILE__, __LINE__);

					// Add flash message
					flash()->add_info(__('Pass updated', 'profile'));

					($hook = get_hook('pf_change_pass_key_pre_redirect')) ? eval($hook) : null;

					redirect(forum_link($forum_url['index']), __('Pass updated', 'profile'));
				}
			}

			// Is this users own profile
			$forum_page['own_profile'] = (user()->id == $id) ? true : false;

			// Setup form
			$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;
			$forum_page['form_action'] = forum_link($forum_url['change_password_key'], array($id, $key));

			// Setup breadcrumbs
			$forum_page['crumbs'] = array(
				array(config()->o_board_title, forum_link($forum_url['index'])),
				array(sprintf(__('Users profile', 'profile'), $user['username'], __('Section about', 'profile')), forum_link($forum_url['profile_about'], $id)),
				($forum_page['own_profile']) ? __('Change your password', 'profile') :
				sprintf(__('Change user password', 'profile'), forum_htmlencode($user['username']))
			);

			($hook = get_hook('pf_change_pass_key_pre_header_load')) ? eval($hook) : null;

			define('FORUM_PAGE', 'profile-changepass');

			$forum_main_view = 'profile/profile_changepass';
			include FORUM_ROOT . 'include/render.php';
		}
	}

	// Make sure we are allowed to change this user's password
	if (user()->id != $id &&
		user()->g_id != FORUM_ADMIN &&
		(user()->g_moderator != '1' || user()->g_mod_edit_users == '0' || user()->g_mod_change_passwords == '0' || $user['g_id'] == FORUM_ADMIN || $user['g_moderator'] == '1'))
		message(__('No permission'));

	if (isset($_POST['form_sent']))
	{
		($hook = get_hook('pf_change_pass_normal_form_submitted')) ? eval($hook) : null;

		$old_password = isset($_POST['req_old_password']) ? forum_trim($_POST['req_old_password']) : '';
		$new_password1 = forum_trim($_POST['req_new_password1']);
		$new_password2 = (config()->o_mask_passwords == '1') ? forum_trim($_POST['req_new_password2']) : $new_password1;

		if (utf8_strlen($new_password1) < 4)
			$errors[] = __('Pass too short', 'profile');
		else if ($new_password1 != $new_password2)
			$errors[] = __('Pass not match', 'profile');

		$authorized = false;
		if (!empty($user['password']))
		{
			$old_password_hash = forum_hash($old_password, $user['salt']);

			if (($user['password'] == $old_password_hash) || user()->is_admmod)
				$authorized = true;
		}

		if (!$authorized)
			$errors[] = __('Wrong old password', 'profile');

		// Did everything go according to plan?
		if (empty($errors))
		{
			$new_password_hash = forum_hash($new_password1, $user['salt']);

			$query = array(
				'UPDATE'	=> 'users',
				'SET'		=> 'password=\''.$new_password_hash.'\'',
				'WHERE'		=> 'id='.$id
			);

			($hook = get_hook('pf_change_pass_normal_qr_update_password')) ? eval($hook) : null;
			db()->query_build($query) or error(__FILE__, __LINE__);

			if (user()->id == $id) {
				$cookie_data = @explode('|', base64_decode($_COOKIE[$cookie_name]));

				$expire = ($cookie_data[2] > time() + config()->o_timeout_visit) ?
					time() + 1209600 : time() + config()->o_timeout_visit;
				forum_setcookie($cookie_name, base64_encode(user()->id.'|'.$new_password_hash.'|'.$expire.'|'.sha1($user['salt'].$new_password_hash.forum_hash($expire, $user['salt']))), $expire);
			}

			// Add flash message
			flash()->add_info(__('Pass updated redirect', 'profile'));

			($hook = get_hook('pf_change_pass_normal_pre_redirect')) ? eval($hook) : null;

			redirect(forum_link($forum_url['profile_about'], $id), __('Pass updated redirect', 'profile'));
		}
	}

	// Is this users own profile
	$forum_page['own_profile'] = (user()->id == $id) ? true : false;

	// Setup form
	$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;
	$forum_page['form_action'] = forum_link($forum_url['change_password'], $id);

	$forum_page['hidden_fields'] = array(
		'form_sent'		=> '<input type="hidden" name="form_sent" value="1" />',
		'csrf_token'	=> '<input type="hidden" name="csrf_token" value="'.generate_form_token($forum_page['form_action']).'" />'
	);

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array(config()->o_board_title, forum_link($forum_url['index'])),
		array(sprintf(__('Users profile', 'profile'), $user['username']), forum_link($forum_url['profile_about'], $id)),
		($forum_page['own_profile']) ? __('Change your password', 'profile') :
		sprintf(__('Change user password', 'profile'), forum_htmlencode($user['username']))
	);

	($hook = get_hook('pf_change_pass_normal_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE', 'profile-changepass');

	$forum_main_view = 'profile/profile_changepass2';
	include FORUM_ROOT . 'include/render.php';
}


else if ($action == 'change_email')
{
	// Make sure we are allowed to change this user's e-mail
	if (user()->id != $id &&
		user()->g_id != FORUM_ADMIN &&
		(user()->g_moderator != '1' || user()->g_mod_edit_users == '0' || $user['g_id'] == FORUM_ADMIN || $user['g_moderator'] == '1'))
		message(__('No permission'));

	($hook = get_hook('pf_change_email_selected')) ? eval($hook) : null;

	// User pressed the cancel button
	if (isset($_POST['cancel']))
		redirect(forum_link($forum_url['profile_about'], $id), __('Cancel redirect'));

	if (isset($_GET['key']))
	{
		$key = $_GET['key'];

		($hook = get_hook('pf_change_email_key_supplied')) ? eval($hook) : null;

		if ($key == '' || $key != $user['activate_key'])
			message(sprintf(__('E-mail key bad', 'profile'), '<a href="mailto:'.
				forum_htmlencode(config()->o_admin_email).'">'.forum_htmlencode(config()->o_admin_email).'</a>'));
		else
		{
			$query = array(
				'UPDATE'	=> 'users',
				'SET'		=> 'email=activate_string, activate_string=NULL, activate_key=NULL',
				'WHERE'		=> 'id='.$id
			);

			($hook = get_hook('pf_change_email_key_qr_update_email')) ? eval($hook) : null;
			db()->query_build($query) or error(__FILE__, __LINE__);

			message(__('E-mail updated', 'profile'));
		}
	}
	else if (isset($_POST['form_sent']))
	{
		($hook = get_hook('pf_change_email_normal_form_submitted')) ? eval($hook) : null;

		if (forum_hash($_POST['req_password'], user()->salt) !== user()->password)
			$errors[] = __('Wrong password', 'profile');

		if (!defined('FORUM_EMAIL_FUNCTIONS_LOADED'))
			require FORUM_ROOT.'include/email.php';

		// Validate the email-address
		$new_email = strtolower(forum_trim($_POST['req_new_email']));
		if (!is_valid_email($new_email))
			$errors[] = __('Invalid e-mail');

		// Check if it's a banned e-mail address
		if (is_banned_email($new_email))
		{
			($hook = get_hook('pf_change_email_normal_banned_email')) ? eval($hook) : null;

			if (config()->p_allow_banned_email == '0')
				$errors[] = __('Banned e-mail', 'profile');
			else if (config()->o_mailing_list != '')
			{
				$mail_subject = 'Alert - Banned e-mail detected';
				$mail_message = 'User \''.user()->username.'\' changed to banned e-mail address: '.$new_email."\n\n".'User profile: '.forum_link($forum_url['user'], $id)."\n\n".'-- '."\n".'Forum Mailer'."\n".'(Do not reply to this message)';

				forum_mail(config()->o_mailing_list, $mail_subject, $mail_message);
			}
		}

		// Check if someone else already has registered with that e-mail address
		$query = array(
			'SELECT'	=> 'u.id, u.username',
			'FROM'		=> 'users AS u',
			'WHERE'		=> 'u.email=\''.db()->escape($new_email).'\''
		);

		($hook = get_hook('pf_change_email_normal_qr_check_email_dupe')) ? eval($hook) : null;
		$result = db()->query_build($query) or error(__FILE__, __LINE__);

		$dupe_list = array();
		while ($cur_dupe = db()->fetch_assoc($result))
		{
			$dupe_list[] = $cur_dupe['username'];
		}

		if (!empty($dupe_list))
		{
			($hook = get_hook('pf_change_email_normal_dupe_email')) ? eval($hook) : null;

			if (config()->p_allow_dupe_email == '0')
				$errors[] = __('Dupe e-mail', 'profile');
			else if ((config()->o_mailing_list != '') && empty($errors))
			{
				$mail_subject = 'Alert - Duplicate e-mail detected';
				$mail_message = 'User \''.user()->username.'\' changed to an e-mail address that also belongs to: '.implode(', ', $dupe_list)."\n\n".'User profile: '.forum_link($forum_url['user'], $id)."\n\n".'-- '."\n".'Forum Mailer'."\n".'(Do not reply to this message)';

				forum_mail(config()->o_mailing_list, $mail_subject, $mail_message);
			}
		}

		// Did everything go according to plan?
		if (empty($errors))
		{
			if (config()->o_regs_verify != '1')
			{
				// We have no confirmed e-mail so we change e-mail right now
				$query = array(
					'UPDATE'	=> 'users',
					'SET'		=> 'email=\''.db()->escape($new_email).'\'',
					'WHERE'		=> 'id='.$id
				);

				($hook = get_hook('pf_change_email_key_qr_update_email')) ? eval($hook) : null;
				db()->query_build($query) or error(__FILE__, __LINE__);

				redirect(forum_link($forum_url['profile_about'], $id), __('E-mail updated redirect', 'profile'));
			}

			// We have a confirmed e-mail so we going to send an activation link

			$new_email_key = random_key(8, true);

			// Save new e-mail and activation key
			$query = array(
				'UPDATE'	=> 'users',
				'SET'		=> 'activate_string=\''.db()->escape($new_email).'\', activate_key=\''.$new_email_key.'\'',
				'WHERE'		=> 'id='.$id
			);

			($hook = get_hook('pf_change_email_normal_qr_update_email_activation')) ? eval($hook) : null;
			db()->query_build($query) or error(__FILE__, __LINE__);

			// Load the "activate e-mail" template
			$mail_tpl = forum_trim(file_get_contents(
				PUNBB::get('language')->path[user()->language] . '/mail_templates/activate_email.tpl'));

			// The first row contains the subject
			$first_crlf = strpos($mail_tpl, "\n");
			$mail_subject = forum_trim(substr($mail_tpl, 8, $first_crlf-8));
			$mail_message = forum_trim(substr($mail_tpl, $first_crlf));

			$mail_message = str_replace('<username>', user()->username, $mail_message);
			$mail_message = str_replace('<base_url>', $base_url.'/', $mail_message);
			$mail_message = str_replace('<activation_url>', str_replace('&amp;', '&', forum_link($forum_url['change_email_key'], array($id, $new_email_key))), $mail_message);
			$mail_message = str_replace('<board_mailer>', sprintf(__('Forum mailer'), config()->o_board_title), $mail_message);

			($hook = get_hook('pf_change_email_normal_pre_activation_email_sent')) ? eval($hook) : null;

			forum_mail($new_email, $mail_subject, $mail_message);

			message(sprintf(__('Activate e-mail sent', 'profile'), '<a href="mailto:'.
				forum_htmlencode(config()->o_admin_email).'">'.
				forum_htmlencode(config()->o_admin_email).'</a>'));
		}
	}

	// Is this users own profile
	$forum_page['own_profile'] = (user()->id == $id) ? true : false;

	// Setup form
	$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;
	$forum_page['form_action'] = forum_link($forum_url['change_email'], $id);

	$forum_page['hidden_fields'] = array(
		'form_sent'		=> '<input type="hidden" name="form_sent" value="1" />',
		'csrf_token'	=> '<input type="hidden" name="csrf_token" value="'.generate_form_token($forum_page['form_action']).'" />'
	);

	// Setup form information
	$forum_page['frm_info'] = '<p class="important"><span>' . __('E-mail info', 'profile') . '</span></p>';

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array(config()->o_board_title, forum_link($forum_url['index'])),
		array(sprintf(__('Users profile', 'profile'), $user['username'], __('Section about', 'profile')), forum_link($forum_url['profile_about'], $id)),
		($forum_page['own_profile']) ? __('Change your e-mail', 'profile') : sprintf(__('Change user e-mail', 'profile'), forum_htmlencode($user['username']))
	);

	($hook = get_hook('pf_change_email_normal_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE', 'profile-changemail');

	$forum_main_view = 'profile/profile_changemail';
	include FORUM_ROOT . 'include/render.php';
}

else if ($action == 'delete_user' || isset($_POST['delete_user_comply']) || isset($_POST['cancel']))
{
	// User pressed the cancel button
	if (isset($_POST['cancel']))
		redirect(forum_link($forum_url['profile_admin'], $id), __('Cancel redirect'));

	($hook = get_hook('pf_delete_user_selected')) ? eval($hook) : null;

	if (user()->g_id != FORUM_ADMIN) {
		message(__('No permission'));
	}

	if ($user['g_id'] == FORUM_ADMIN)
		message(__('Cannot delete admin', 'profile'));

	if (isset($_POST['delete_user_comply']))
	{
		($hook = get_hook('pf_delete_user_form_submitted')) ? eval($hook) : null;

		delete_user($id, isset($_POST['delete_posts']));

		// Remove cache file with forum stats
		require FORUM_ROOT . 'include/cache.php';
		clean_stats_cache();

		// Add flash message
		flash()->add_info(__('User delete redirect', 'profile'));

		($hook = get_hook('pf_delete_user_pre_redirect')) ? eval($hook) : null;

		redirect(forum_link($forum_url['index']), __('User delete redirect', 'profile'));
	}

	// Setup form
	$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;
	$forum_page['form_action'] = forum_link($forum_url['delete_user'], $id);

	// Setup form information
	$forum_page['frm_info'] = array(
		'<li class="warn"><span>' . __('Delete warning', 'profile') . '</span></li>',
		'<li class="warn"><span>' . __('Delete posts info', 'profile') . '</span></li>'
	);

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array(config()->o_board_title, forum_link($forum_url['index'])),
		array(sprintf(__('Users profile', 'profile'), $user['username'], __('Section admin', 'profile')), forum_link($forum_url['profile_admin'], $id)),
		__('Delete user', 'profile')
	);

	($hook = get_hook('pf_delete_user_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE', 'dialogue');

	$forum_main_view = 'profile/dialogue';
	include FORUM_ROOT . 'include/render.php';
}


else if ($action == 'delete_avatar')
{
	// Make sure we are allowed to delete this user's avatar
	if (user()->id != $id &&
		user()->g_id != FORUM_ADMIN &&
		(user()->g_moderator != '1' || user()->g_mod_edit_users == '0' || $user['g_id'] == FORUM_ADMIN || $user['g_moderator'] == '1'))
		message(__('No permission'));

	// We validate the CSRF token. If it's set in POST and we're at this point, the token is valid.
	// If it's in GET, we need to make sure it's valid.
	if (!isset($_POST['csrf_token']) && (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== generate_form_token('delete_avatar'.$id.user()->id)))
		csrf_confirm_form();

	($hook = get_hook('pf_delete_avatar_selected')) ? eval($hook) : null;

	delete_avatar($id);

	// Add flash message
	flash()->add_info(__('Avatar deleted redirect', 'profile'));

	($hook = get_hook('pf_delete_avatar_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['profile_avatar'], $id), __('Avatar deleted redirect', 'profile'));
}


else if (isset($_POST['update_group_membership']))
{
	if (user()->g_id != FORUM_ADMIN) {
		message(__('No permission'));
	}

	($hook = get_hook('pf_change_group_form_submitted')) ? eval($hook) : null;

	$new_group_id = intval($_POST['group_id']);

	$query = array(
		'UPDATE'	=> 'users',
		'SET'		=> 'group_id='.$new_group_id,
		'WHERE'		=> 'id='.$id
	);

	($hook = get_hook('pf_change_group_qr_update_group')) ? eval($hook) : null;
	db()->query_build($query) or error(__FILE__, __LINE__);

	$query = array(
		'SELECT'	=> 'g.g_moderator',
		'FROM'		=> 'groups AS g',
		'WHERE'		=> 'g.g_id='.$new_group_id
	);

	($hook = get_hook('pf_change_group_qr_check_new_group_mod')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);
	$new_group_mod = db()->result($result);

	// If the user was a moderator or an administrator (and no longer is), we remove him/her from the moderator list in all forums
	if (($user['g_id'] == FORUM_ADMIN || $user['g_moderator'] == '1') && $new_group_id != FORUM_ADMIN && $new_group_mod != '1')
		clean_forum_moderators();

	// Add flash message
	flash()->add_info(__('Group membership redirect', 'profile'));

	($hook = get_hook('pf_change_group_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['profile_admin'], $id), __('Group membership redirect', 'profile'));
}
else if (isset($_POST['update_forums']))
{
	if (user()->g_id != FORUM_ADMIN) {
		message(__('No permission'));
	}

	($hook = get_hook('pf_forum_moderators_form_submitted')) ? eval($hook) : null;

	$moderator_in = (isset($_POST['moderator_in'])) ? array_keys($_POST['moderator_in']) : array();

	// Loop through all forums
	$query = array(
		'SELECT'	=> 'f.id, f.moderators',
		'FROM'		=> 'forums AS f'
	);

	($hook = get_hook('pf_forum_moderators_qr_get_all_forum_mods')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);
	while ($cur_forum = db()->fetch_assoc($result))
	{
		$cur_moderators = ($cur_forum['moderators'] != '') ? unserialize($cur_forum['moderators']) : array();

		// If the user should have moderator access (and he/she doesn't already have it)
		if (in_array($cur_forum['id'], $moderator_in) && !in_array($id, $cur_moderators))
		{
			$cur_moderators[$user['username']] = $id;
			ksort($cur_moderators);
		}
		// If the user shouldn't have moderator access (and he/she already has it)
		else if (!in_array($cur_forum['id'], $moderator_in) && in_array($id, $cur_moderators))
			unset($cur_moderators[$user['username']]);

		$cur_moderators = (!empty($cur_moderators)) ? '\''.db()->escape(serialize($cur_moderators)).'\'' : 'NULL';

		$query = array(
			'UPDATE'	=> 'forums',
			'SET'		=> 'moderators='.$cur_moderators,
			'WHERE'		=> 'id='.$cur_forum['id']
		);

		($hook = get_hook('pf_forum_moderators_qr_update_forum_moderators')) ? eval($hook) : null;
		db()->query_build($query) or error(__FILE__, __LINE__);
	}

	// Add flash message
	flash()->add_info(__('Moderate forums redirect', 'profile'));

	($hook = get_hook('pf_forum_moderators_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['profile_admin'], $id), __('Moderate forums redirect', 'profile'));
}


else if (isset($_POST['ban']))
{
	if (user()->g_id != FORUM_ADMIN && (user()->g_moderator != '1' || user()->g_mod_ban_users == '0'))
		message(__('No permission'));

	($hook = get_hook('pf_ban_user_selected')) ? eval($hook) : null;

	redirect(forum_link($forum_url['admin_bans']).'&amp;add_ban='.$id, __('Ban redirect', 'profile'));
}


else if (isset($_POST['form_sent']))
{
	// Make sure we are allowed to edit this user's profile
	if (user()->id != $id &&
		user()->g_id != FORUM_ADMIN &&
		(user()->g_moderator != '1' || user()->g_mod_edit_users == '0' || $user['g_id'] == FORUM_ADMIN || $user['g_moderator'] == '1'))
		message(__('No permission'));

	($hook = get_hook('pf_change_details_form_submitted')) ? eval($hook) : null;

	// Extract allowed elements from $_POST['form']
	function extract_elements($allowed_elements)
	{
		$form = array();

		foreach ($_POST['form'] as $key => $value)
		{
			if (in_array($key, $allowed_elements))
				$form[$key] = $value;
		}

		return $form;
	}

	$username_updated = false;

	// Validate input depending on section
	switch ($section)
	{
		case 'identity':
		{
			$form = extract_elements(array('realname', 'url', 'location', 'jabber', 'icq', 'msn', 'aim', 'yahoo', 'facebook', 'twitter', 'linkedin', 'skype'));

			($hook = get_hook('pf_change_details_identity_validation')) ? eval($hook) : null;

			if (user()->is_admmod) {
				// Are we allowed to change usernames?
				if (user()->g_id == FORUM_ADMIN || (user()->g_moderator == '1' && user()->g_mod_rename_users == '1'))
				{
					$form['username'] = forum_trim($_POST['req_username']);
					$old_username = forum_trim($_POST['old_username']);

					// Validate the new username
					$errors = array_merge($errors, validate_username($form['username'], $id));

					if ($form['username'] != $old_username)
						$username_updated = true;
				}

				// We only allow administrators to update the post count
				if (user()->g_id == FORUM_ADMIN)
					$form['num_posts'] = intval($_POST['num_posts']);
			}

			if (user()->is_admmod) {
				if (!defined('FORUM_EMAIL_FUNCTIONS_LOADED'))
					require FORUM_ROOT.'include/email.php';

				// Validate the email-address
				$form['email'] = strtolower(forum_trim($_POST['req_email']));
				if (!is_valid_email($form['email']))
					$errors[] = __('Invalid e-mail');
			}

			if (user()->is_admmod) {
				$form['admin_note'] = forum_trim($_POST['admin_note']);
			}

			if (user()->g_id == FORUM_ADMIN)
				$form['title'] = forum_trim($_POST['title']);
			else if (user()->g_set_title == '1')
			{
				$form['title'] = forum_trim($_POST['title']);

				if ($form['title'] != '')
				{
					// A list of words that the title may not contain
					// If the language is English, there will be some duplicates, but it's not the end of the world
					$forbidden = array('Member', 'Moderator', 'Administrator', 'Banned', 'Guest', __('Member'), __('Moderator'), __('Administrator'), __('Banned'), __('Guest'));

					if (in_array($form['title'], $forbidden))
						$errors[] = __('Forbidden title', 'profile');
				}
			}

			// Add http:// if the URL doesn't contain it or https:// already
			if ($form['url'] != '' && strpos(strtolower($form['url']), 'http://') !== 0 && strpos(strtolower($form['url']), 'https://') !== 0)
				$form['url'] = 'http://'.$form['url'];

			//check Facebook for validity
			if (strpos($form['facebook'], 'http://') === 0 || strpos($form['facebook'], 'https://') === 0)
				if (!preg_match('#https?://(www\.)?facebook.com/.+?#', $form['facebook']))
					$errors[] = __('Bad Facebook', 'profile');

			//check Twitter for validity
			if (strpos($form['twitter'], 'http://') === 0 || strpos($form['twitter'], 'https://') === 0)
				if (!preg_match('#https?://twitter.com/.+?#', $form['twitter']))
					$errors[] = __('Bad Twitter', 'profile');

			//check LinkedIn for validity
			if (!preg_match('#https?://(www\.)?linkedin.com/.+?#', $form['linkedin']))
					$errors[] = __('Bad LinkedIn', 'profile');

			// Add http:// if the LinkedIn doesn't contain it or https:// already
			if ($form['linkedin'] != '' && strpos(strtolower($form['linkedin']), 'http://') !== 0 && strpos(strtolower($form['linkedin']), 'https://') !== 0)
				$form['linkedin'] = 'http://'.$form['linkedin'];

			// If the ICQ UIN contains anything other than digits it's invalid
			if ($form['icq'] != '' && !ctype_digit($form['icq']))
				$errors[] = __('Bad ICQ', 'profile');

			break;
		}

		case 'settings':
		{
			$form = extract_elements(array('dst', 'timezone', 'language', 'email_setting', 'notify_with_post', 'auto_notify', 'time_format', 'date_format', 'disp_topics', 'disp_posts', 'show_smilies', 'show_img', 'show_img_sig', 'show_avatars', 'show_sig', 'style'));

			($hook = get_hook('pf_change_details_settings_validation')) ? eval($hook) : null;

			$form['dst'] = (isset($form['dst'])) ? 1 : 0;
			$form['time_format'] = (isset($form['time_format'])) ? intval($form['time_format']) : 0;
			$form['date_format'] = (isset($form['date_format'])) ? intval($form['date_format']) : 0;
			$form['timezone'] = (isset($form['timezone'])) ?
				floatval($form['timezone']) : config()->o_default_timezone;

			// Validate timezone
			if (($form['timezone'] > 14.0) || ($form['timezone'] < -12.0)) {
				message(__('Bad request'));
			}

			$form['email_setting'] = intval($form['email_setting']);
			if ($form['email_setting'] < 0 || $form['email_setting'] > 2) $form['email_setting'] = 1;

			if (config()->o_subscriptions == '1')
			{
				if (!isset($form['notify_with_post']) || $form['notify_with_post'] != '1') $form['notify_with_post'] = '0';
				if (!isset($form['auto_notify']) || $form['auto_notify'] != '1') $form['auto_notify'] = '0';
			}

			// Make sure we got a valid language string
			if (isset($form['language']))
			{
				$form['language'] = preg_replace('#[\.\\\/]#', '', $form['language']);
				if (!file_exists(PUNBB::get('language')->path[$form['language']] . '/common.php'))
					message(__('Bad request'));
			}

			if ($form['disp_topics'] != '' && intval($form['disp_topics']) < 3) $form['disp_topics'] = 3;
			if ($form['disp_topics'] != '' && intval($form['disp_topics']) > 75) $form['disp_topics'] = 75;
			if ($form['disp_posts'] != '' && intval($form['disp_posts']) < 3) $form['disp_posts'] = 3;
			if ($form['disp_posts'] != '' && intval($form['disp_posts']) > 75) $form['disp_posts'] = 75;

			if (!isset($form['show_smilies']) || $form['show_smilies'] != '1') $form['show_smilies'] = '0';
			if (!isset($form['show_img']) || $form['show_img'] != '1') $form['show_img'] = '0';
			if (!isset($form['show_img_sig']) || $form['show_img_sig'] != '1') $form['show_img_sig'] = '0';
			if (!isset($form['show_avatars']) || $form['show_avatars'] != '1') $form['show_avatars'] = '0';
			if (!isset($form['show_sig']) || $form['show_sig'] != '1') $form['show_sig'] = '0';

			// Make sure we got a valid style string
			if (isset($form['style']))
			{
				$form['style'] = preg_replace('#[\.\\\/]#', '', $form['style']);
				if (!file_exists(PUNBB::get('theme')->path[$form['style']] .
							'/' . $form['style'] . '.php'))
					message(__('Bad request'));
			}
			break;
		}

		case 'signature':
		{
			if (config()->o_signatures == '0')
				message(__('Signatures disabled', 'profile'));

			($hook = get_hook('pf_change_details_signature_validation')) ? eval($hook) : null;

			// Clean up signature from POST
			$form['signature'] = forum_linebreaks(forum_trim($_POST['signature']));

			// Validate signature
			if (utf8_strlen($form['signature']) > config()->p_sig_length)
				$errors[] = sprintf(__('Sig too long', 'profile'), forum_number_format(config()->p_sig_length), forum_number_format(utf8_strlen($form['signature']) - config()->p_sig_length));
			if (substr_count($form['signature'], "\n") > (config()->p_sig_lines - 1))
				$errors[] = sprintf(__('Sig too many lines', 'profile'), forum_number_format(config()->p_sig_lines));

			if ($form['signature'] != '' && config()->p_sig_all_caps == '0' && check_is_all_caps($form['signature']) && !user()->is_admmod)
				$form['signature'] = utf8_ucwords(utf8_strtolower($form['signature']));

			// Validate BBCode syntax
			if (config()->p_sig_bbcode == '1' || config()->o_make_links == '1')
			{
				if (!defined('FORUM_PARSER_LOADED'))
					require FORUM_ROOT.'include/parser.php';

				$form['signature'] = preparse_bbcode($form['signature'], $errors, true);
			}

			break;
		}

		case 'avatar':
		{
			if (config()->o_avatars == '0')
				message(__('Avatars disabled', 'profile'));

			($hook = get_hook('pf_change_details_avatar_validation')) ? eval($hook) : null;

			if (!isset($_FILES['req_file']))
			{
				$errors[] = __('No file', 'profile');
				break;
			}
			else
				$uploaded_file = $_FILES['req_file'];

			// Make sure the upload went smooth
			if (isset($uploaded_file['error']) && empty($errors))
			{
				switch ($uploaded_file['error'])
				{
					case 1:	// UPLOAD_ERR_INI_SIZE
					case 2:	// UPLOAD_ERR_FORM_SIZE
						$errors[] = __('Too large ini', 'profile');
						break;

					case 3:	// UPLOAD_ERR_PARTIAL
						$errors[] = __('Partial upload', 'profile');
						break;

					case 4:	// UPLOAD_ERR_NO_FILE
						$errors[] = __('No file', 'profile');
						break;

					case 6:	// UPLOAD_ERR_NO_TMP_DIR
						$errors[] = __('No tmp directory', 'profile');
						break;

					default:
						// No error occured, but was something actually uploaded?
						if ($uploaded_file['size'] == 0)
							$errors[] = __('No file', 'profile');
						break;
				}
			}

			if (is_uploaded_file($uploaded_file['tmp_name']) && empty($errors))
			{
				// First check simple by size and mime type
				$allowed_mime_types = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png');
				$allowed_types = array(IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF);

				($hook = get_hook('pf_change_details_avatar_allowed_types')) ? eval($hook) : null;

				if (!in_array($uploaded_file['type'], $allowed_mime_types))
					$errors[] = __('Bad type', 'profile');
				else
				{
					// Make sure the file isn't too big
					if ($uploaded_file['size'] > config()->o_avatars_size)
						$errors[] = sprintf(__('Too large', 'profile'), forum_number_format(config()->o_avatars_size));
				}

				if (empty($errors))
				{
					$avatar_tmp_file = config()->o_avatars_dir.'/'.$id.'.tmp';

					// Move the file to the avatar directory. We do this before checking the width/height to circumvent open_basedir restrictions.
					if (!@move_uploaded_file($uploaded_file['tmp_name'], $avatar_tmp_file))
						$errors[] = sprintf(__('Move failed', 'profile'), '<a href="mailto:'.
							forum_htmlencode(config()->o_admin_email).'">'.
							forum_htmlencode(config()->o_admin_email).'</a>');

					if (empty($errors))
					{
						($hook = get_hook('pf_change_details_avatar_modify_size')) ? eval($hook) : null;

						// Now check the width, height, type
						list($width, $height, $type,) = @/**/getimagesize($avatar_tmp_file);
						if (empty($width) || empty($height) || $width > config()->o_avatars_width || $height > config()->o_avatars_height)
						{
							@unlink($avatar_tmp_file);
							$errors[] = sprintf(__('Too wide or high', 'profile'), config()->o_avatars_width, config()->o_avatars_height);
						}
						else if ($type == IMAGETYPE_GIF && $uploaded_file['type'] != 'image/gif')	// Prevent dodgy uploads
						{
							@unlink($avatar_tmp_file);
							$errors[] = __('Bad type', 'profile');
						}

						// Determine type
						$extension = null;
						$avatar_type = FORUM_AVATAR_NONE;
						if ($type == IMAGETYPE_GIF)
						{
							$extension = '.gif';
							$avatar_type = FORUM_AVATAR_GIF;
						}
						else if ($type == IMAGETYPE_JPEG)
						{
							$extension = '.jpg';
							$avatar_type = FORUM_AVATAR_JPG;
						}
						else if ($type == IMAGETYPE_PNG)
						{
							$extension = '.png';
							$avatar_type = FORUM_AVATAR_PNG;
						}

						($hook = get_hook('pf_change_details_avatar_determine_extension')) ? eval($hook) : null;

						// Check type from getimagesize type format
						if (!in_array($avatar_type, $allowed_types) || empty($extension))
						{
							@unlink($avatar_tmp_file);
							$errors[] = __('Bad type', 'profile');
						}

						($hook = get_hook('pf_change_details_avatar_validate_file')) ? eval($hook) : null;

						if (empty($errors))
						{
							// Delete any old avatars
							delete_avatar($id);

							// Put the new avatar in its place
							@rename($avatar_tmp_file, config()->o_avatars_dir.'/'.$id.$extension);
							@chmod(config()->o_avatars_dir.'/'.$id.$extension, 0644);

							// Avatar
							$avatar_width = (intval($width) > 0) ? intval($width) : 0;
							$avatar_height = (intval($height) > 0) ? intval($height) : 0;

							// Save to DB
							$query = array(
								'UPDATE'	=> 'users',
								'SET'		=> 'avatar=\''.$avatar_type.'\', avatar_height=\''.$avatar_height.'\', avatar_width=\''.$avatar_width.'\'',
								'WHERE'		=> 'id='.$id
							);
							($hook = get_hook('pf_change_details_avatar_qr_update_avatar')) ? eval($hook) : null;
							db()->query_build($query) or error(__FILE__, __LINE__);

							// Update avatar info
							$user['avatar'] = $avatar_type;
							$user['avatar_width'] = $width;
							$user['avatar_height'] = $height;
						}
					}
				}
			}
			else if (empty($errors))
				$errors[] = __('Unknown failure', 'profile');

			break;
		}

		default:
		{
			($hook = get_hook('pf_change_details_new_section_validation')) ? eval($hook) : null;
			break;
		}
	}

	$skip_db_update_sections = array('avatar');

	($hook = get_hook('pf_change_details_pre_database_validation')) ? eval($hook) : null;

	// All sections apart from avatar potentially affect the database
	if (!in_array($section, $skip_db_update_sections) && empty($errors))
	{
		($hook = get_hook('pf_change_details_database_validation')) ? eval($hook) : null;

		// Singlequotes around non-empty values and NULL for empty values
		$new_values = array();
		foreach ($form as $key => $input)
		{
			$value = ($input !== '') ? '\''.db()->escape($input).'\'' : 'NULL';

			$new_values[] = $key.'='.$value;
		}

		// Make sure we have something to update
		if (empty($new_values))
			message(__('Bad request'));

		// Run the update
		$query = array(
			'UPDATE'	=> 'users',
			'SET'		=> implode(',', $new_values),
			'WHERE'		=> 'id='.$id
		);

		($hook = get_hook('pf_change_details_qr_update_user')) ? eval($hook) : null;
		db()->query_build($query) or error(__FILE__, __LINE__);

		// If we changed the username we have to update some stuff
		if ($username_updated)
		{
			($hook = get_hook('pf_change_details_username_changed')) ? eval($hook) : null;

			$query = array(
				'UPDATE'	=> 'posts',
				'SET'		=> 'poster=\''.db()->escape($form['username']).'\'',
				'WHERE'		=> 'poster_id='.$id
			);

			($hook = get_hook('pf_change_details_qr_update_posts_poster')) ? eval($hook) : null;
			db()->query_build($query) or error(__FILE__, __LINE__);

			$query = array(
				'UPDATE'	=> 'topics',
				'SET'		=> 'poster=\''.db()->escape($form['username']).'\'',
				'WHERE'		=> 'poster=\''.db()->escape($old_username).'\''
			);

			($hook = get_hook('pf_change_details_qr_update_topics_poster')) ? eval($hook) : null;
			db()->query_build($query) or error(__FILE__, __LINE__);

			$query = array(
				'UPDATE'	=> 'topics',
				'SET'		=> 'last_poster=\''.db()->escape($form['username']).'\'',
				'WHERE'		=> 'last_poster=\''.db()->escape($old_username).'\''
			);

			($hook = get_hook('pf_change_details_qr_update_topics_last_poster')) ? eval($hook) : null;
			db()->query_build($query) or error(__FILE__, __LINE__);

			$query = array(
				'UPDATE'	=> 'forums',
				'SET'		=> 'last_poster=\''.db()->escape($form['username']).'\'',
				'WHERE'		=> 'last_poster=\''.db()->escape($old_username).'\''
			);

			($hook = get_hook('pf_change_details_qr_update_forums_last_poster')) ? eval($hook) : null;
			db()->query_build($query) or error(__FILE__, __LINE__);

			$query = array(
				'UPDATE'	=> 'online',
				'SET'		=> 'ident=\''.db()->escape($form['username']).'\'',
				'WHERE'		=> 'ident=\''.db()->escape($old_username).'\''
			);

			($hook = get_hook('pf_change_details_qr_update_online_ident')) ? eval($hook) : null;
			db()->query_build($query) or error(__FILE__, __LINE__);

			$query = array(
				'UPDATE'	=> 'posts',
				'SET'		=> 'edited_by=\''.db()->escape($form['username']).'\'',
				'WHERE'		=> 'edited_by=\''.db()->escape($old_username).'\''
			);

			($hook = get_hook('pf_change_details_qr_update_posts_edited_by')) ? eval($hook) : null;
			db()->query_build($query) or error(__FILE__, __LINE__);

			// If the user is a moderator or an administrator we have to update the moderator lists and bans cache
			if ($user['g_id'] == FORUM_ADMIN || $user['g_moderator'] == '1')
			{
				$query = array(
					'SELECT'	=> 'f.id, f.moderators',
					'FROM'		=> 'forums AS f'
				);

				($hook = get_hook('pf_change_details_qr_get_all_forum_mods')) ? eval($hook) : null;
				$result = db()->query_build($query) or error(__FILE__, __LINE__);
				while ($cur_forum = db()->fetch_assoc($result))
				{
					$cur_moderators = ($cur_forum['moderators'] != '') ? unserialize($cur_forum['moderators']) : array();

					if (in_array($id, $cur_moderators))
					{
						unset($cur_moderators[$old_username]);
						$cur_moderators[$form['username']] = $id;
						ksort($cur_moderators);

						$query = array(
							'UPDATE'	=> 'forums',
							'SET'		=> 'moderators=\''.db()->escape(serialize($cur_moderators)).'\'',
							'WHERE'		=> 'id='.$cur_forum['id']
						);

						($hook = get_hook('pf_change_details_qr_update_forum_moderators')) ? eval($hook) : null;
						db()->query_build($query) or error(__FILE__, __LINE__);
					}
				}

				// Regenerate the bans cache
				require FORUM_ROOT . 'include/cache.php';
				generate_bans_cache();
			}
		}

		// Add flash message
		flash()->add_info(__('Profile redirect', 'profile'));

		($hook = get_hook('pf_change_details_pre_redirect')) ? eval($hook) : null;

		redirect(forum_link($forum_url['profile_'.$section], $id), __('Profile redirect', 'profile'));
	}
}

($hook = get_hook('pf_new_action')) ? eval($hook) : null;


if ($user['signature'] != '')
{
	if (!defined('FORUM_PARSER_LOADED'))
		require FORUM_ROOT.'include/parser.php';

	$parsed_signature = parse_signature($user['signature']);
}


// View or edit?
if (user()->id != $id &&
	user()->g_id != FORUM_ADMIN &&
	(user()->g_moderator != '1' || user()->g_mod_edit_users == '0' || $user['g_id'] == FORUM_ADMIN || $user['g_moderator'] == '1'))
{
	// Setup user identification
	$forum_page['user_ident'] = array();

	($hook = get_hook('pf_view_details_selected')) ? eval($hook) : null;

	$forum_page['user_ident']['username'] = '<li class="username'.(($user['realname'] =='') ? ' fn nickname' : ' nickname').'"><strong>'.forum_htmlencode($user['username']).'</strong></li>';

	if (config()->o_avatars == '1')
	{
		$forum_page['avatar_markup'] = generate_avatar_markup($id, $user['avatar'], $user['avatar_width'], $user['avatar_height'], $user['username'], TRUE);

		if (!empty($forum_page['avatar_markup']))
			$forum_page['user_ident']['avatar'] = '<li class="useravatar">'.$forum_page['avatar_markup'].'</li>';
	}

	$forum_page['user_ident']['usertitle'] = '<li class="usertitle"><span>'.get_title($user).'</span></li>';

	// Setup user information
	$forum_page['user_info'] = array();

	if ($user['realname'] !='')
		$forum_page['user_info']['realname'] = '<li><span>'.
			__('Realname', 'profile') . ': <strong class="fn">'.forum_htmlencode((config()->o_censoring == '1') ? censor_words($user['realname']) : $user['realname']).'</strong></span></li>';

	if ($user['location'] !='')
		$forum_page['user_info']['location'] = '<li><span>'.
			__('From', 'profile') . ': <strong> '.forum_htmlencode((config()->o_censoring == '1') ? censor_words($user['location']) : $user['location']).'</strong></span></li>';

	$forum_page['user_info']['registered'] = '<li><span>'.
		__('Registered', 'profile') . ': <strong> '.format_time($user['registered'], 1).'</strong></span></li>';
	$forum_page['user_info']['lastpost'] = '<li><span>'.
		__('Last post', 'profile') . ': <strong> '.format_time($user['last_post']).'</strong></span></li>';

	if (config()->o_show_post_count == '1' || user()->is_admmod)
		$forum_page['user_info']['posts'] = '<li><span>'.
			__('Posts', 'profile') . ': <strong>'.forum_number_format($user['num_posts']).'</strong></span></li>';

	// Setup user address
	$forum_page['user_contact'] = array();

	if ($user['email_setting'] == '0' && !user()->is_guest && user()->g_send_email == '1')
		$forum_page['user_contact']['email'] = '<li><span>'.
			__('E-mail', 'profile') . ': <a href="mailto:'.forum_htmlencode($user['email']).'" class="email">'.forum_htmlencode((config()->o_censoring == '1' ? censor_words($user['email']) : $user['email'])).'</a></span></li>';

	if ($user['email_setting'] != '2' && !user()->is_guest && user()->g_send_email == '1')
		$forum_page['user_contact']['forum-mail'] = '<li><span>'.
			__('E-mail', 'profile') . ': <a href="'.forum_link($forum_url['email'], $id).'">'.
				__('Send forum e-mail', 'profile') . '</a></span></li>';

	if ($user['url'] != '')
	{
		$url_source = $user['url'];

		// IDNA url handling
		if (defined('FORUM_SUPPORT_PCRE_UNICODE') && defined('FORUM_ENABLE_IDNA'))
		{
			// Load the IDNA class for international url handling
			require_once FORUM_ROOT.'include/idna/idna_convert.class.php';

			$idn = new idna_convert();
			$idn->set_parameter('encoding', 'utf8');
			$idn->set_parameter('strict', false);

			if (preg_match('!^(https?|ftp|news){1}'.preg_quote('://xn--', '!').'!', $url_source))
			{
				$user['url'] = $idn->decode($url_source);
			}
			else
			{
				$url_source = $idn->encode($url_source);
			}
		}

		if (config()->o_censoring == '1')
			$user['url'] = censor_words($user['url']);

		$url_source = forum_htmlencode($url_source);
		$user['url'] = forum_htmlencode($user['url']);
		$forum_page['url'] = '<a href="'.$url_source.'" class="external url" rel="me">'.$user['url'].'</a>';

		$forum_page['user_contact']['website'] = '<li><span>'.
			__('Website', 'profile') . ': '.$forum_page['url'].'</span></li>';
	}

	// Facebook
	if ($user['facebook'] != '')
	{
		if (config()->o_censoring == '1')
		{
			$user['facebook'] = censor_words($user['facebook']);
		}

		$facebook_url = ((strpos($user['facebook'], 'http://') === 0) || (strpos($user['facebook'], 'https://') === 0)) ?
			forum_htmlencode($user['facebook']) :
			forum_htmlencode('https://www.facebook.com/'.$user['facebook'])
		;
		$forum_page['facebook'] = '<a href="'.$facebook_url.'" class="external url">'.$facebook_url.'</a>';
		$forum_page['user_contact']['facebook'] = '<li><span>'.
			__('Facebook', 'profile') . ': '.$forum_page['facebook'].'</span></li>';
	}

	// Twitter
	if ($user['twitter'] != '')
	{
		if (config()->o_censoring == '1')
		{
			$user['twitter'] = censor_words($user['twitter']);
		}

		$twitter_url = ((strpos($user['twitter'], 'http://') === 0) || (strpos($user['twitter'], 'https://') === 0)) ?
			forum_htmlencode($user['twitter']) :
			forum_htmlencode('https://twitter.com/'.$user['twitter'])
		;
		$forum_page['twitter'] = '<a href="'.$twitter_url.'" class="external url">'.$twitter_url.'</a>';
		$forum_page['user_contact']['twitter'] = '<li><span>'.
			__('Twitter', 'profile') . ': '.$forum_page['twitter'].'</span></li>';
	}

	// LinkedIn
	if ($user['linkedin'] != '')
	{
		if (config()->o_censoring == '1')
		{
			$user['linkedin'] = censor_words($user['linkedin']);
		}

		$linkedin_url = forum_htmlencode($user['linkedin']);
		$forum_page['linkedin'] = '<a href="'.$linkedin_url.'" class="external url" rel="me">'.$linkedin_url.'</a>';
		$forum_page['user_contact']['linkedin'] = '<li><span>'.
			__('LinkedIn', 'profile') . ': '.$forum_page['linkedin'].'</span></li>';
	}

	if ($user['jabber'] !='')
		$forum_page['user_contact']['jabber'] = '<li><span>'.
			__('Jabber', 'profile') . ': <strong> '.forum_htmlencode((config()->o_censoring == '1') ? censor_words($user['jabber']) : $user['jabber']).'</strong></span></li>';
	if ($user['icq'] !='')
		$forum_page['user_contact']['icq'] = '<li><span>'.
			__('ICQ', 'profile') . ': <strong> '.forum_htmlencode($user['icq']).'</strong></span></li>';
	if ($user['msn'] !='')
		$forum_page['user_contact']['msn'] = '<li><span>'.
			__('MSN', 'profile') . ': <strong> '.forum_htmlencode((config()->o_censoring == '1') ? censor_words($user['msn']) : $user['msn']).'</strong></span></li>';
	if ($user['aim'] !='')
		$forum_page['user_contact']['aim'] = '<li><span>'.
			__('AOL IM', 'profile') . ': <strong> '.forum_htmlencode((config()->o_censoring == '1') ? censor_words($user['aim']) : $user['aim']).'</strong></span></li>';
	if ($user['yahoo'] !='')
		$forum_page['user_contact']['yahoo'] = '<li><span>'.
			__('Yahoo', 'profile') . ': <strong> '.forum_htmlencode((config()->o_censoring == '1') ? censor_words($user['yahoo']) : $user['yahoo']).'</strong></span></li>';

	// Setup signature demo
	if (config()->o_signatures == '1' && isset($parsed_signature))
		$forum_page['sig_demo'] = $parsed_signature;

	// Setup search links
	if (user()->g_search == '1') {
		$forum_page['user_activity'] = array();
		$forum_page['user_activity']['search_posts'] = '<li class="first-item"><a href="'.forum_link($forum_url['search_user_posts'], $id).'">'.
			sprintf(__('View user posts', 'profile'), forum_htmlencode($user['username'])).'</a></li>';
		$forum_page['user_activity']['search_topics'] = '<li><a href="'.forum_link($forum_url['search_user_topics'], $id).'">'.
			sprintf(__('View user topics', 'profile'), forum_htmlencode($user['username'])).'</a></li>';
	}

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array(config()->o_board_title, forum_link($forum_url['index'])),
		sprintf(__('Users profile', 'profile'), $user['username'])
	);

	$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;

	($hook = get_hook('pf_view_details_pre_header_load')) ? eval($hook) : null;

	define('FORUM_ALLOW_INDEX', 1);
	define('FORUM_PAGE', 'profile');

	$forum_main_view = 'profile/profile';
	include FORUM_ROOT . 'include/render.php';
}
else
{
	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array(config()->o_board_title, forum_link($forum_url['index'])),
		sprintf(__('Users profile', 'profile'), $user['username'])
	);

	// Is this users own profile
	$forum_page['own_profile'] = (user()->id == $id) ? true : false;

	// Setup navigation menu
	$forum_page['main_menu'] = array();
	$forum_page['main_menu']['about'] = '<li class="first-item'.(($section == 'about') ? ' active' : '').'"><a href="'.forum_link($forum_url['profile_about'], $id).'"><span>'.
		__('Section about', 'profile') . '</span></a></li>';
	$forum_page['main_menu']['identity'] = '<li'.(($section == 'identity') ? ' class="active"' : '').'><a href="'.forum_link($forum_url['profile_identity'], $id).'"><span>'.
		__('Section identity', 'profile') . '</span></a></li>';
	$forum_page['main_menu']['settings'] = '<li'.(($section == 'settings') ? ' class="active"' : '').'><a href="'.forum_link($forum_url['profile_settings'], $id).'"><span>'.
		__('Section settings', 'profile') . '</span></a></li>';

	if (config()->o_signatures == '1')
		$forum_page['main_menu']['signature'] = '<li'.(($section == 'signature') ? ' class="active"' : '').'><a href="'.forum_link($forum_url['profile_signature'], $id).'"><span>'.
			__('Section signature', 'profile') . '</span></a></li>';

	if (config()->o_avatars == '1')
		$forum_page['main_menu']['avatar'] = '<li'.(($section == 'avatar') ? ' class="active"' : '').'><a href="'.forum_link($forum_url['profile_avatar'], $id).'"><span>'.
			__('Section avatar', 'profile') . '</span></a></li>';

	if (user()->g_id == FORUM_ADMIN || (user()->g_moderator == '1' && user()->g_mod_ban_users == '1' && !$forum_page['own_profile']))
		$forum_page['main_menu']['admin'] = '<li'.(($section == 'admin') ? ' class="active"' : '').'><a href="'.forum_link($forum_url['profile_admin'], $id).'"><span>'.
			__('Section admin', 'profile') . '</span></a></li>';

	($hook = get_hook('pf_change_details_modify_main_menu')) ? eval($hook) : null;
	// End navigation menu

	if ($section == 'about')
	{
		// Setup breadcrumbs
		$forum_page['crumbs'] = array(
			array(config()->o_board_title, forum_link($forum_url['index'])),
			array(sprintf(__('Users profile', 'profile'), $user['username']), forum_link($forum_url['user'], $id)),
			sprintf(__('Section about', 'profile'))
		);

		// Setup user identification
		$forum_page['user_ident'] = array();

		($hook = get_hook('pf_change_details_about_selected')) ? eval($hook) : null;

		$forum_page['user_ident']['username'] = '<li class="username'.(($user['realname'] =='') ? ' fn nickname' : ' nickname').'"><strong>'.forum_htmlencode($user['username']).'</strong></li>';

		if (config()->o_avatars == '1')
		{
			$forum_page['avatar_markup'] = generate_avatar_markup($id, $user['avatar'], $user['avatar_width'], $user['avatar_height'], $user['username'], TRUE);

			if (!empty($forum_page['avatar_markup']))
				$forum_page['user_ident']['avatar'] = '<li class="useravatar">'.$forum_page['avatar_markup'].'</li>';
		}

		$forum_page['user_ident']['usertitle'] = '<li class="usertitle"><span>'.get_title($user).'</span></li>';

		// Create array for private information
		$forum_page['user_private'] = array();

		// Setup user information
		$forum_page['user_info'] = array();

		if ($user['realname'] !='')
			$forum_page['user_info']['realname'] = '<li><span>'.
				__('Realname', 'profile') . ': <strong class="fn">'.forum_htmlencode((config()->o_censoring == '1') ? censor_words($user['realname']) : $user['realname']).'</strong></span></li>';

		if ($user['location'] !='')
			$forum_page['user_info']['location'] = '<li><span>'.
				__('From', 'profile') . ': <strong> '.forum_htmlencode((config()->o_censoring == '1') ? censor_words($user['location']) : $user['location']).'</strong></span></li>';

		$forum_page['user_info']['registered'] = '<li><span>'.
			__('Registered', 'profile') . ': <strong> '.format_time($user['registered'], 1).'</strong></span></li>';
		$forum_page['user_info']['lastvisit'] = '<li><span>'.
			__('Last visit', 'profile') . ': <strong> '.format_time($user['last_visit']).'</strong></span></li>';
		$forum_page['user_info']['lastpost'] = '<li><span>'.
			__('Last post', 'profile') . ': <strong> '.format_time($user['last_post']).'</strong></span></li>';

		if (config()->o_show_post_count == '1' || user()->is_admmod)
			$forum_page['user_info']['posts'] = '<li><span>'.
				__('Posts', 'profile') . ': <strong>'.forum_number_format($user['num_posts']).'</strong></span></li>';
		else
			$forum_page['user_private']['posts'] = '<li><span>'.
			__('Posts', 'profile') . ': <strong>'.forum_number_format($user['num_posts']).'</strong></span></li>';

		if (user()->is_admmod && $user['admin_note'] != '')
			$forum_page['user_private']['note'] = '<li><span>'.
			__('Note', 'profile') . ': <strong>'.forum_htmlencode($user['admin_note']).'</strong></span></li>';

		// Setup user address
		$forum_page['user_contact'] = array();

		if (($user['email_setting'] == '0' && !user()->is_guest) && user()->g_send_email == '1')
			$forum_page['user_contact']['email'] = '<li><span>'.
				__('E-mail', 'profile') . ': <a href="mailto:'.forum_htmlencode($user['email']).'" class="email">'.forum_htmlencode((config()->o_censoring == '1' ? censor_words($user['email']) : $user['email'])).'</a></span></li>';
		else if ($forum_page['own_profile'] || user()->is_admmod)
				$forum_page['user_private']['email'] = '<li><span>'.
				__('E-mail', 'profile') . ': <a href="mailto:'.forum_htmlencode($user['email']).'" class="email">'.forum_htmlencode((config()->o_censoring == '1' ? censor_words($user['email']) : $user['email'])).'</a></span></li>';

		if ($user['email_setting'] != '2')
			$forum_page['user_contact']['forum-mail'] = '<li><span>'.
				__('E-mail', 'profile') . ': <a href="'.forum_link($forum_url['email'], $id).'">'.
					__('Send forum e-mail', 'profile') . '</a></span></li>';
		else if (user()->id == $id || (user()->is_admmod && $user['email_setting'] == '2'))
			$forum_page['user_private']['forum-mail'] = '<li><span>'.
				__('E-mail', 'profile') . ': <a href="'.forum_link($forum_url['email'], $id).'">'.
					__('Send forum e-mail', 'profile') . '</a></span></li>';

		// Website
		if ($user['url'] != '')
		{
			$url_source = $user['url'];

			// IDNA url handling
			if (defined('FORUM_SUPPORT_PCRE_UNICODE') && defined('FORUM_ENABLE_IDNA'))
			{
				// Load the IDNA class for international url handling
				require_once FORUM_ROOT.'include/idna/idna_convert.class.php';

				$idn = new idna_convert();
				$idn->set_parameter('encoding', 'utf8');
				$idn->set_parameter('strict', false);

				if (preg_match('!^(https?|ftp|news){1}'.preg_quote('://xn--', '!').'!', $url_source))
				{
					$user['url'] = $idn->decode($url_source);
				}
				else
				{
					$url_source = $idn->encode($url_source);
				}
			}

			if (config()->o_censoring == '1')
				$user['url'] = censor_words($user['url']);

			$url_source = forum_htmlencode($url_source);
			$user['url'] = forum_htmlencode($user['url']);
			$forum_page['url'] = '<a href="'.$url_source.'" class="external url" rel="me">'.$user['url'].'</a>';

			$forum_page['user_contact']['website'] = '<li><span>'.
				__('Website', 'profile') . ': '.$forum_page['url'].'</span></li>';
		}

		// Facebook
		if ($user['facebook'] != '')
		{
			if (config()->o_censoring == '1')
			{
				$user['facebook'] = censor_words($user['facebook']);
			}

			$facebook_url = ((strpos($user['facebook'], 'http://') === 0) || (strpos($user['facebook'], 'https://') === 0)) ?
				forum_htmlencode($user['facebook']) :
				forum_htmlencode('https://www.facebook.com/'.$user['facebook'])
			;
			$forum_page['facebook'] = '<a href="'.$facebook_url.'" class="external url">'.$facebook_url.'</a>';
			$forum_page['user_contact']['facebook'] = '<li><span>'.
				__('Facebook', 'profile') . ': '.$forum_page['facebook'].'</span></li>';
		}

		// Twitter
		if ($user['twitter'] != '')
		{
			if (config()->o_censoring == '1')
			{
				$user['twitter'] = censor_words($user['twitter']);
			}

			$twitter_url = ((strpos($user['twitter'], 'http://') === 0) || (strpos($user['twitter'], 'https://') === 0)) ?
				forum_htmlencode($user['twitter']) :
				forum_htmlencode('https://twitter.com/'.$user['twitter'])
			;
			$forum_page['twitter'] = '<a href="'.$twitter_url.'" class="external url">'.$twitter_url.'</a>';
			$forum_page['user_contact']['twitter'] = '<li><span>'.
				__('Twitter', 'profile') . ': '.$forum_page['twitter'].'</span></li>';
		}

		// LinkedIn
		if ($user['linkedin'] != '')
		{
			if (config()->o_censoring == '1')
			{
				$user['linkedin'] = censor_words($user['linkedin']);
			}

			$linkedin_url = forum_htmlencode($user['linkedin']);
			$forum_page['linkedin'] = '<a href="'.$linkedin_url.'" class="external url" rel="me">'.$linkedin_url.'</a>';
			$forum_page['user_contact']['linkedin'] = '<li><span>'.
				__('LinkedIn', 'profile') . ': '.$forum_page['linkedin'].'</span></li>';
		}


		if (user()->is_admmod)
			$forum_page['user_private']['ip']= '<li><span>'.
				__('IP', 'profile') . ': <a href="'.forum_link($forum_url['get_host'], forum_htmlencode($user['registration_ip'])).'">'.forum_htmlencode($user['registration_ip']).'</a></span></li>';

		// Setup user messaging
		if ($user['jabber'] !='')
			$forum_page['user_contact']['jabber'] = '<li><span>'.
				__('Jabber', 'profile') . ': <strong>'.forum_htmlencode((config()->o_censoring == '1') ? censor_words($user['jabber']) : $user['jabber']).'</strong></span></li>';
		if ($user['skype'] !='')
			$forum_page['user_contact']['skype'] = '<li><span>'.
				__('Skype', 'profile') . ': <strong>'.forum_htmlencode((config()->o_censoring == '1') ? censor_words($user['skype']) : $user['skype']).'</strong></span></li>';
		if ($user['icq'] !='')
			$forum_page['user_contact']['icq'] = '<li><span>'.
				__('ICQ', 'profile') . ': <strong>'.forum_htmlencode($user['icq']).'</strong></span></li>';
		if ($user['msn'] !='')
			$forum_page['user_contact']['msn'] = '<li><span>'.
				__('MSN', 'profile') . ': <strong>'.forum_htmlencode((config()->o_censoring == '1') ? censor_words($user['msn']) : $user['msn']).'</strong></span></li>';
		if ($user['aim'] !='')
			$forum_page['user_contact']['aim'] = '<li><span>'.
				__('AOL IM', 'profile') . ': <strong>'.forum_htmlencode((config()->o_censoring == '1') ? censor_words($user['aim']) : $user['aim']).'</strong></span></li>';
		if ($user['yahoo'] !='')
			$forum_page['user_contact']['yahoo'] = '<li><span>'.
				__('Yahoo', 'profile') . ': <strong>'.forum_htmlencode((config()->o_censoring == '1') ? censor_words($user['yahoo']) : $user['yahoo']).'</strong></span></li>';

		// Setup signature demo
		if (config()->o_signatures == '1' && isset($parsed_signature))
			$forum_page['sig_demo'] = $parsed_signature;

		// Setup search links
		$forum_page['user_activity'] = array();
		if (user()->g_search == '1' || user()->is_admmod)
		{
			$forum_page['user_activity']['search_posts'] = '<li class="first-item"><a href="'.forum_link($forum_url['search_user_posts'], $id).'">'.(($forum_page['own_profile']) ?
				__('View your posts', 'profile') :
				sprintf(__('View user posts', 'profile'), forum_htmlencode($user['username']))).'</a></li>';
			$forum_page['user_activity']['search_topics'] = '<li><a href="'.forum_link($forum_url['search_user_topics'], $id).'">'.(($forum_page['own_profile']) ?
				__('View your topics', 'profile') :
				sprintf(__('View user topics', 'profile'), forum_htmlencode($user['username']))).'</a></li>';
		}

		// Subscriptions
		if (($forum_page['own_profile'] || user()->g_id == FORUM_ADMIN) && config()->o_subscriptions == '1')
		{
			// Topic subscriptions
			$forum_page['user_activity']['search_subs'] = '<li'.(empty($forum_page['user_activity']) ? ' class="first-item"' : '').'><a href="'.forum_link($forum_url['search_subscriptions'], $id).'">'.(($forum_page['own_profile']) ?
				__('View your subscriptions', 'profile') :
				sprintf(__('View user subscriptions', 'profile'), forum_htmlencode($user['username']))).'</a></li>';

			// Forum subscriptions
			$forum_page['user_activity']['search_forum_subs'] = '<li'.(empty($forum_page['user_activity']) ? ' class="first-item"' : '').'><a href="'.forum_link($forum_url['search_forum_subscriptions'], $id).'">'.(($forum_page['own_profile']) ?
				__('View your forum subscriptions', 'profile') :
				sprintf(__('View user forum subscriptions', 'profile'), forum_htmlencode($user['username']))).'</a></li>';
		}

		// Setup user options
		$forum_page['user_options'] = array();

		if ($forum_page['own_profile'] || user()->g_id == FORUM_ADMIN || (user()->g_moderator == '1' && user()->g_mod_change_passwords == '1'))
			$forum_page['user_options']['change_password'] = '<span'.(empty($forum_page['user_options']) ? ' class="first-item"' : '').'><a href="'.forum_link($forum_url['change_password'], $id).'">'.(($forum_page['own_profile']) ?
				__('Change your password', 'profile') :
				sprintf(__('Change user password', 'profile'), forum_htmlencode($user['username']))).'</a></span>';

		if (!user()->is_admmod)
			$forum_page['user_options']['change_email'] = '<span'.(empty($forum_page['user_options']) ? ' class="first-item"' : '').'><a href="'.forum_link($forum_url['change_email'], $id).'">'.(($forum_page['own_profile']) ?
				__('Change your e-mail', 'profile') :
				sprintf(__('Change user e-mail', 'profile'), forum_htmlencode($user['username']))).'</a></span>';

		$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;

		($hook = get_hook('pf_change_details_about_pre_header_load')) ? eval($hook) : null;

		define('FORUM_PAGE', 'profile-about');

		$forum_main_view = 'profile/profile_about';
		include FORUM_ROOT . 'include/render.php';
	}

	else if ($section == 'identity')
	{
		// Setup breadcrumbs
		$forum_page['crumbs'] = array(
			array(config()->o_board_title, forum_link($forum_url['index'])),
			array(sprintf(__('Users profile', 'profile'), $user['username']), forum_link($forum_url['user'], $id)),
			__('Section identity', 'profile')
		);
		// Setup the form
		$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;
		$forum_page['form_action'] = forum_link($forum_url['profile_identity'], $id);

		$forum_page['hidden_fields'] = array(
			'form_sent'		=> '<input type="hidden" name="form_sent" value="1" />',
			'csrf_token'	=> '<input type="hidden" name="csrf_token" value="'.generate_form_token($forum_page['form_action']).'" />'
		);

		if (user()->is_admmod && (user()->g_id == FORUM_ADMIN || user()->g_mod_rename_users == '1'))
			$forum_page['hidden_fields']['old_username'] = '<input type="hidden" name="old_username" value="'.forum_htmlencode($user['username']).'" />';

		// Does the form have required fields
		$forum_page['has_required'] = (((user()->is_admmod && (user()->g_id == FORUM_ADMIN || user()->g_mod_rename_users == '1')) || user()->is_admmod) ? true : false);

		($hook = get_hook('pf_change_details_identity_pre_header_load')) ? eval($hook) : null;

		define('FORUM_PAGE', 'profile-identity');

		$forum_main_view = 'profile/profile_identity';
		include FORUM_ROOT . 'include/render.php';
	}

	else if ($section == 'settings')
	{
		$forum_page['styles'] = get_style_packs();
		$forum_page['languages'] = get_language_packs();

		// Setup breadcrumbs
		$forum_page['crumbs'] = array(
			array(config()->o_board_title, forum_link($forum_url['index'])),
			array(sprintf(__('Users profile', 'profile'), $user['username']), forum_link($forum_url['user'], $id)),
			__('Section settings', 'profile')
		);

		// Setup the form
		$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;
		$forum_page['form_action'] = forum_link($forum_url['profile_settings'], $id);

		$forum_page['hidden_fields'] = array(
			'form_sent'		=> '<input type="hidden" name="form_sent" value="1" />',
			'csrf_token'	=> '<input type="hidden" name="csrf_token" value="'.generate_form_token($forum_page['form_action']).'" />'
		);

		($hook = get_hook('pf_change_details_settings_pre_header_load')) ? eval($hook) : null;

		define('FORUM_PAGE', 'profile-settings');

		$forum_main_view = 'profile/profile_settings';
		include FORUM_ROOT . 'include/render.php';
	}

	else if ($section == 'signature' && config()->o_signatures == '1')
	{
		$forum_page['sig_info'][] = '<li>' . __('Signature info', 'profile') . '</li>';

		if ($user['signature'] != '')
			$forum_page['sig_demo'] = $parsed_signature;

		// Setup breadcrumbs
		$forum_page['crumbs'] = array(
			array(config()->o_board_title, forum_link($forum_url['index'])),
			array(sprintf(__('Users profile', 'profile'), $user['username']), forum_link($forum_url['user'], $id)),
			__('Section signature', 'profile')
		);

		// Setup the form
		$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;
		$forum_page['form_action'] = forum_link($forum_url['profile_signature'], $id);

		$forum_page['hidden_fields'] = array(
			'form_sent'		=> '<input type="hidden" name="form_sent" value="1" />',
			'csrf_token'	=> '<input type="hidden" name="csrf_token" value="'.generate_form_token($forum_page['form_action']).'" />'
		);

		// Setup help
		$forum_page['text_options'] = array();
		if (config()->p_sig_bbcode == '1')
			$forum_page['text_options']['bbcode'] = '<span'.(empty($forum_page['text_options']) ? ' class="first-item"' : '').'><a class="exthelp" href="'.forum_link($forum_url['help'], 'bbcode').'" title="'.
			sprintf(__('Help page'), __('BBCode')).'">'.__('BBCode').'</a></span>';
		if (config()->p_sig_img_tag == '1')
			$forum_page['text_options']['img'] = '<span'.(empty($forum_page['text_options']) ? ' class="first-item"' : '').'><a class="exthelp" href="'.forum_link($forum_url['help'], 'img').'" title="'.
			sprintf(__('Help page'), __('Images')).'">'.__('Images').'</a></span>';
		if (config()->o_smilies_sig == '1')
			$forum_page['text_options']['smilies'] = '<span'.(empty($forum_page['text_options']) ? ' class="first-item"' : '').'><a class="exthelp" href="'.forum_link($forum_url['help'], 'smilies').'" title="'.
			sprintf(__('Help page'), __('Smilies')).'">'.__('Smilies').'</a></span>';

		($hook = get_hook('pf_change_details_signature_pre_header_load')) ? eval($hook) : null;

		define('FORUM_PAGE', 'profile-signature');

		$forum_main_view = 'profile/profile_signature';
		include FORUM_ROOT . 'include/render.php';
	}

	else if ($section == 'avatar' && config()->o_avatars == '1')
	{
		$forum_page['avatar_markup'] = generate_avatar_markup($id, $user['avatar'], $user['avatar_width'], $user['avatar_height'], $user['username'], TRUE);

		// Setup breadcrumbs
		$forum_page['crumbs'] = array(
			array(config()->o_board_title, forum_link($forum_url['index'])),
			array(sprintf(__('Users profile', 'profile'), $user['username']), forum_link($forum_url['user'], $id)),
			__('Section avatar', 'profile')
		);

		// Setup the form
		$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;
		$forum_page['form_action'] = forum_link($forum_url['profile_avatar'], $id);

		$forum_page['hidden_fields'] = array(
			'form_sent'		=> '<input type="hidden" name="form_sent" value="1" />',
			'max_file_size'	=> '<input type="hidden" name="MAX_FILE_SIZE" value="'.config()->o_avatars_size.'" />',
			'csrf_token'	=> '<input type="hidden" name="csrf_token" value="'.generate_form_token($forum_page['form_action']).'" />'
		);

		// Setup form information
		$forum_page['frm_info'] = array();

		if (!empty($forum_page['avatar_markup']))
		{
			$forum_page['frm_info']['avatar_replace'] = '<li><span>'.
				__('Avatar info replace', 'profile') . '</span></li>';
			$forum_page['frm_info']['avatar_type'] = '<li><span>'.
				__('Avatar info type', 'profile') . '</span></li>';
			$forum_page['frm_info']['avatar_size'] = '<li><span>'.
				sprintf(__('Avatar info size', 'profile'), config()->o_avatars_width, config()->o_avatars_height, forum_number_format(config()->o_avatars_size), forum_number_format(ceil(config()->o_avatars_size / 1024))).'</span></li>';
			$forum_page['avatar_demo'] = $forum_page['avatar_markup'];
		}
		else
		{
			$forum_page['frm_info']['avatar_none'] = '<li><span>'.
				__('Avatar info none', 'profile') . '</span></li>';
			$forum_page['frm_info']['avatar_info'] = '<li><span>'.
				__('Avatar info type', 'profile') . '</span></li>';
			$forum_page['frm_info']['avatar_size'] = '<li><span>'.
				sprintf(__('Avatar info size', 'profile'), config()->o_avatars_width, config()->o_avatars_height, forum_number_format(config()->o_avatars_size), forum_number_format(ceil(config()->o_avatars_size / 1024))).'</span></li>';
		}

		($hook = get_hook('pf_change_details_avatar_pre_header_load')) ? eval($hook) : null;

		define('FORUM_PAGE', 'profile-avatar');

		$forum_main_view = 'profile/profile_avatar';
		include FORUM_ROOT . 'include/render.php';
	}

	else if ($section == 'admin')
	{
		if (user()->g_id != FORUM_ADMIN && (user()->g_moderator != '1' || user()->g_mod_ban_users == '0' || user()->id == $id))
			message(__('Bad request'));

		// Setup breadcrumbs
		$forum_page['crumbs'] = array(
			array(config()->o_board_title, forum_link($forum_url['index'])),
			array(sprintf(__('Users profile', 'profile'), $user['username']), forum_link($forum_url['user'], $id)),
			__('Section admin', 'profile')
		);

		// Setup form
		$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;
		$forum_page['form_action'] = forum_link($forum_url['profile_admin'], $id);

		$forum_page['hidden_fields'] = array(
			'form_sent'		=> '<input type="hidden" name="form_sent" value="1" />',
			'csrf_token'	=> '<input type="hidden" name="csrf_token" value="'.generate_form_token($forum_page['form_action']).'" />'
		);

		// Setup ban and delete options
		$forum_page['user_management'] = array();

		if (user()->g_moderator == '1')
			$forum_page['user_management']['ban'] = '<div class="ct-set set'.++$forum_page['item_count'].'">'."\n\t\t\t\t".'<div class="ct-box">'."\n\t\t\t\t\t".'<h3 class="ct-legend hn">'.
				__('Ban user', 'profile') . '</h3>'."\n\t\t\t\t".'<p><a href="'.forum_link($forum_url['admin_bans']).'&amp;add_ban='.$id.'">'.
					__('Ban user info', 'profile') . '</a></p>'."\n\t\t\t\t".'</div>'."\n\t\t\t".'</div>';
		else if (user()->g_moderator != '1' && $user['g_id'] != FORUM_ADMIN )
		{
			$forum_page['user_management']['ban'] = '<div class="ct-set set'.++$forum_page['item_count'].'">'."\n\t\t\t\t".'<div class="ct-box">'."\n\t\t\t\t\t".'<h3 class="ct-legend hn">'.
				__('Ban user', 'profile') . '</h3>'."\n\t\t\t\t".'<p><a href="'.forum_link($forum_url['admin_bans']).'&amp;add_ban='.$id.'">'.
					__('Ban user info', 'profile') . '</a></p>'."\n\t\t\t\t".'</div>'."\n\t\t\t".'</div>';
			$forum_page['user_management']['delete'] = '<div class="ct-set set'.++$forum_page['item_count'].'">'."\n\t\t\t\t".'<div class="ct-box">'."\n\t\t\t\t\t".'<h3 class="ct-legend hn">'.
				__('Delete user', 'profile') . '</h3>'."\n\t\t\t\t".'<p><a href="'.forum_link($forum_url['delete_user'], $id).'">'.
					__('Delete user info', 'profile') . '</a></p>'."\n\t\t\t\t".'</div>'."\n\t\t\t".'</div>';
		}

		($hook = get_hook('pf_change_details_admin_pre_header_load')) ? eval($hook) : null;

		define('FORUM_PAGE', 'profile-admin');

		if (user()->g_moderator != '1' && !$forum_page['own_profile']) {
			($hook = get_hook('pf_change_details_admin_pre_group_membership')) ? eval($hook) : null;

			$query = array(
				'SELECT'	=> 'g.g_id, g.g_title',
				'FROM'		=> 'groups AS g',
				'WHERE'		=> 'g.g_id!='.FORUM_GUEST,
				'ORDER BY'	=> 'g.g_title'
			);

			($hook = get_hook('pf_change_details_admin_qr_get_groups')) ? eval($hook) : null;
			$result_group = db()->query_build($query) or error(__FILE__, __LINE__);
		}

		if (user()->g_id == FORUM_ADMIN &&
				($user['g_id'] == FORUM_ADMIN || $user['g_moderator'] == '1')) {
			($hook = get_hook('pf_change_details_admin_pre_mod_assignment_fieldset')) ? eval($hook) : null;

			$query = array(
				'SELECT'	=> 'c.id AS cid, c.cat_name, f.id AS fid, f.forum_name, f.moderators',
				'FROM'		=> 'categories AS c',
				'JOINS'		=> array(
					array(
						'INNER JOIN'	=> 'forums AS f',
						'ON'			=> 'c.id=f.cat_id'
					)
				),
				'WHERE'		=> 'f.redirect_url IS NULL',
				'ORDER BY'	=> 'c.disp_position, c.id, f.disp_position'
			);

			($hook = get_hook('pf_change_details_admin_qr_get_cats_and_forums')) ? eval($hook) : null;
			$result = db()->query_build($query) or error(__FILE__, __LINE__);
		}

		$forum_main_view = 'profile/profile_admin';
		include FORUM_ROOT . 'include/render.php';
	}

	($hook = get_hook('pf_change_details_new_section')) ? eval($hook) : null;

	message(__('Bad request'));
}
