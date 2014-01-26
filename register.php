<?php
/**
 * Allows the creation of new user accounts.
 *
 * @copyright (C) 2008-2014 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */


if (!defined('FORUM_ROOT'))
	define('FORUM_ROOT', './');
require FORUM_ROOT.'include/common.php';

($hook = get_hook('rg_start')) ? eval($hook) : null;

// If we are logged in, we shouldn't be here
if (!$forum_user['is_guest'])
{
	header('Location: '.forum_link($forum_url['index']));
	exit;
}

// Load the profile.php language file
require FORUM_ROOT.'lang/'.$forum_user['language'].'/profile.php';

if ($forum_config['o_regs_allow'] == '0')
	message($lang_profile['No new regs']);

$errors = array();


// User pressed the cancel button
if (isset($_GET['cancel']))
	redirect(forum_link($forum_url['index']), $lang_profile['Reg cancel redirect']);

// User pressed agree but failed to tick checkbox
else if (isset($_GET['agree']) && !isset($_GET['req_agreement']))
	redirect(forum_link($forum_url['index']), $lang_profile['Reg cancel redirect']);

// Show the rules
else if ($forum_config['o_rules'] == '1' && !isset($_GET['agree']) && !isset($_POST['form_sent']))
{
	// Setup form
	$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array($forum_config['o_board_title'], forum_link($forum_url['index'])),
		array($lang_common['Register'], forum_link($forum_url['register'])),
		$lang_common['Rules']
	);

	($hook = get_hook('rg_rules_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE', 'rules-register');
	require FORUM_ROOT.'header.php';

	// START SUBST - <!-- forum_main -->
	ob_start();

	($hook = get_hook('rg_rules_output_start')) ? eval($hook) : null;

	$forum_page['set_count'] = $forum_page['fld_count'] = 0;

?>
	<div class="main-head">
		<h2 class="hn"><span><?php echo sprintf($lang_profile['Register at'], $forum_config['o_board_title']) ?></span></h2>
	</div>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo $lang_profile['Reg rules head'] ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<div id="rules-content" class="ct-box user-box">
			<?php echo $forum_config['o_rules_message'] ?>
		</div>
		<form class="frm-form" method="get" accept-charset="utf-8" action="<?php echo forum_link($forum_url['register']) ?>">
<?php ($hook = get_hook('rg_rules_pre_group')) ? eval($hook) : null; ?>
			<div class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
<?php ($hook = get_hook('rg_rules_pre_agree_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="req_agreement" value="1" required /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo $lang_profile['Agreement'] ?></span> <?php echo $lang_profile['Agreement label'] ?></label>
					</div>
				</div>
<?php ($hook = get_hook('rg_rules_pre_group_end')) ? eval($hook) : null; ?>
			</div>
<?php ($hook = get_hook('rg_rules_group_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="agree" value="<?php echo $lang_profile['Agree'] ?>" /></span>
				<span class="cancel"><input type="submit" name="cancel" value="<?php echo $lang_common['Cancel'] ?>" formnovalidate /></span>
			</div>
		</form>
	</div>
<?php

	($hook = get_hook('rg_rules_end')) ? eval($hook) : null;

	$tpl_temp = forum_trim(ob_get_contents());
	$tpl_main = str_replace('<!-- forum_main -->', $tpl_temp, $tpl_main);
	ob_end_clean();
	// END SUBST - <!-- forum_main -->

	require FORUM_ROOT.'footer.php';
}

else if (isset($_POST['form_sent']))
{
	($hook = get_hook('rg_register_form_submitted')) ? eval($hook) : null;

	// Check that someone from this IP didn't register a user within the last hour (DoS prevention)
	$query = array(
		'SELECT'	=> 'COUNT(u.id)',
		'FROM'		=> 'users AS u',
		'WHERE'		=> 'u.registration_ip=\''.$forum_db->escape(get_remote_address()).'\' AND u.registered>'.(time() - 3600)
	);

	($hook = get_hook('rg_register_qr_check_register_flood')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	if ($forum_db->result($result) > 0)
	{
		$errors[] = $lang_profile['Registration flood'];
	}

	// Did everything go according to plan so far?
	if (empty($errors))
	{
		$username = forum_trim($_POST['req_username']);
		$email1 = strtolower(forum_trim($_POST['req_email1']));

		if ($forum_config['o_regs_verify'] == '1')
		{
			$password1 = random_key(8, true);
			$password2 = $password1;
		}
		else
		{
			$password1 = forum_trim($_POST['req_password1']);
			$password2 = ($forum_config['o_mask_passwords'] == '1') ? forum_trim($_POST['req_password2']) : $password1;
		}

		// Validate the username
		$errors = array_merge($errors, validate_username($username));

		// ... and the password
		if (utf8_strlen($password1) < 4)
			$errors[] = $lang_profile['Pass too short'];
		else if ($password1 != $password2)
			$errors[] = $lang_profile['Pass not match'];

		// ... and the e-mail address
		if (!defined('FORUM_EMAIL_FUNCTIONS_LOADED'))
			require FORUM_ROOT.'include/email.php';

		if (!is_valid_email($email1))
			$errors[] = $lang_profile['Invalid e-mail'];

		// Check if it's a banned e-mail address
		$banned_email = is_banned_email($email1);
		if ($banned_email && $forum_config['p_allow_banned_email'] == '0')
			$errors[] = $lang_profile['Banned e-mail'];

		// Clean old unverified registrators - delete older than 72 hours
		$query = array(
			'DELETE'	=> 'users',
			'WHERE'		=> 'group_id='.FORUM_UNVERIFIED.' AND activate_key IS NOT NULL AND registered < '.(time() - 259200)
		);
		($hook = get_hook('rg_register_qr_delete_unverified')) ? eval($hook) : null;
		$forum_db->query_build($query) or error(__FILE__, __LINE__);

		// Check if someone else already has registered with that e-mail address
		$dupe_list = array();

		$query = array(
			'SELECT'	=> 'u.username',
			'FROM'		=> 'users AS u',
			'WHERE'		=> 'u.email=\''.$forum_db->escape($email1).'\''
		);

		($hook = get_hook('rg_register_qr_check_email_dupe')) ? eval($hook) : null;
		$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

		while ($cur_dupe = $forum_db->fetch_assoc($result))
		{
			$dupe_list[] = $cur_dupe['username'];
		}

		if (!empty($dupe_list) && empty($errors))
		{
			if ($forum_config['p_allow_dupe_email'] == '0')
				$errors[] = $lang_profile['Dupe e-mail'];
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
					message($lang_common['Bad request']);
			}
			else
				$language = $forum_config['o_default_lang'];

			$initial_group_id = ($forum_config['o_regs_verify'] == '0') ? $forum_config['o_default_user_group'] : FORUM_UNVERIFIED;
			$salt = random_key(12);
			$password_hash = forum_hash($password1, $salt);

			// Validate timezone and DST
			$timezone = (isset($_POST['timezone'])) ? floatval($_POST['timezone']) : $forum_config['o_default_timezone'];

			// Validate timezone — on error use default value
			if (($timezone > 14.0) || ($timezone < -12.0)) {
				$timezone = $forum_config['o_default_timezone'];
			}

			// DST
			$dst = (isset($_POST['dst']) && intval($_POST['dst']) === 1) ? 1 : $forum_config['o_default_dst'];


			// Insert the new user into the database. We do this now to get the last inserted id for later use.
			$user_info = array(
				'username'				=>	$username,
				'group_id'				=>	$initial_group_id,
				'salt'					=>	$salt,
				'password'				=>	$password1,
				'password_hash'			=>	$password_hash,
				'email'					=>	$email1,
				'email_setting'			=>	$forum_config['o_default_email_setting'],
				'timezone'				=>	$timezone,
				'dst'					=>	$dst,
				'language'				=>	$language,
				'style'					=>	$forum_config['o_default_style'],
				'registered'			=>	time(),
				'registration_ip'		=>	get_remote_address(),
				'activate_key'			=>	($forum_config['o_regs_verify'] == '1') ? '\''.random_key(8, true).'\'' : 'NULL',
				'require_verification'	=>	($forum_config['o_regs_verify'] == '1'),
				'notify_admins'			=>	($forum_config['o_regs_report'] == '1')
			);

			($hook = get_hook('rg_register_pre_add_user')) ? eval($hook) : null;
			add_user($user_info, $new_uid);

			// If we previously found out that the e-mail was banned
			if ($banned_email && $forum_config['o_mailing_list'] != '')
			{
				$mail_subject = 'Alert - Banned e-mail detected';
				$mail_message = 'User \''.$username.'\' registered with banned e-mail address: '.$email1."\n\n".'User profile: '.forum_link($forum_url['user'], $new_uid)."\n\n".'-- '."\n".'Forum Mailer'."\n".'(Do not reply to this message)';

				($hook = get_hook('rg_register_banned_email')) ? eval($hook) : null;

				forum_mail($forum_config['o_mailing_list'], $mail_subject, $mail_message);
			}

			// If we previously found out that the e-mail was a dupe
			if (!empty($dupe_list) && $forum_config['o_mailing_list'] != '')
			{
				$mail_subject = 'Alert - Duplicate e-mail detected';
				$mail_message = 'User \''.$username.'\' registered with an e-mail address that also belongs to: '.implode(', ', $dupe_list)."\n\n".'User profile: '.forum_link($forum_url['user'], $new_uid)."\n\n".'-- '."\n".'Forum Mailer'."\n".'(Do not reply to this message)';

				($hook = get_hook('rg_register_dupe_email')) ? eval($hook) : null;

				forum_mail($forum_config['o_mailing_list'], $mail_subject, $mail_message);
			}

			($hook = get_hook('rg_register_pre_login_redirect')) ? eval($hook) : null;

			// Must the user verify the registration or do we log him/her in right now?
			if ($forum_config['o_regs_verify'] == '1')
			{
				message(sprintf($lang_profile['Reg e-mail'], '<a href="mailto:'.forum_htmlencode($forum_config['o_admin_email']).'">'.forum_htmlencode($forum_config['o_admin_email']).'</a>'));
			}
			else
			{
				// Remove cache file with forum stats
				if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
				{
					require FORUM_ROOT.'include/cache.php';
				}

				clean_stats_cache();
			}

			$expire = time() + $forum_config['o_timeout_visit'];

			forum_setcookie($cookie_name, base64_encode($new_uid.'|'.$password_hash.'|'.$expire.'|'.sha1($salt.$password_hash.forum_hash($expire, $salt))), $expire);

			redirect(forum_link($forum_url['index']), $lang_profile['Reg complete']);
		}
	}
}

// Setup form
$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;
$forum_page['form_action'] = forum_link($forum_url['register']).'?action=register';

// Setup form information
$forum_page['frm_info'] = array();
if ($forum_config['o_regs_verify'] != '0')
	$forum_page['frm_info']['email'] = '<p class="warn">'.$lang_profile['Reg e-mail info'].'</p>';

// Setup breadcrumbs
$forum_page['crumbs'] = array(
	array($forum_config['o_board_title'], forum_link($forum_url['index'])),
	sprintf($lang_profile['Register at'], $forum_config['o_board_title'])
);

// Load JS for timezone detection
$forum_loader->add_js($base_url.'/include/js/min/punbb.timezone.min.js');
$forum_loader->add_js('PUNBB.timezone.detect_on_register_form();', array('type' => 'inline'));


($hook = get_hook('rg_register_pre_header_load')) ? eval($hook) : null;

define('FORUM_PAGE', 'register');
require FORUM_ROOT.'header.php';

// START SUBST - <!-- forum_main -->
ob_start();

($hook = get_hook('rg_register_output_start')) ? eval($hook) : null;

?>
	<div class="main-head">
		<h2 class="hn"><span><?php echo sprintf($lang_profile['Register at'], $forum_config['o_board_title']) ?></span></h2>
	</div>
	<div class="main-content main-frm">
<?php
	if (!empty($forum_page['frm_info'])):
?>
		<div class="ct-box info-box">
			<?php echo implode("\n\t\t\t", $forum_page['frm_info'])."\n" ?>
		</div>
<?php
	endif;

	// If there were any errors, show them
	if (!empty($errors))
	{
		$forum_page['errors'] = array();
		foreach ($errors as $cur_error)
			$forum_page['errors'][] = '<li class="warn"><span>'.$cur_error.'</span></li>';

		($hook = get_hook('rg_pre_register_errors')) ? eval($hook) : null;

?>
		<div class="ct-box error-box">
			<h2 class="warn hn"><span><?php echo $lang_profile['Register errors'] ?></span></h2>
			<ul class="error-list">
				<?php echo implode("\n\t\t\t\t", $forum_page['errors'])."\n" ?>
			</ul>
		</div>
<?php

	}

?>
		<div id="req-msg" class="req-warn ct-box error-box">
			<p class="important"><?php echo $lang_common['Required warn'] ?></p>
		</div>
		<form class="frm-form frm-suggest-username" id="afocus" method="post" accept-charset="utf-8" action="<?php echo $forum_page['form_action'] ?>" autocomplete="off">
			<div class="hidden">
				<input type="hidden" name="form_sent" value="1" />
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token($forum_page['form_action']) ?>" />
				<input type="hidden" name="timezone" id="register_timezone" value="<?php echo forum_htmlencode($forum_config['o_default_timezone']) ?>" />
				<input type="hidden" name="dst" id="register_dst" value="<?php echo forum_htmlencode($forum_config['o_default_dst']) ?>" />
			</div>
<?php ($hook = get_hook('rg_register_pre_group')) ? eval($hook) : null; ?>
			<div class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
<?php ($hook = get_hook('rg_register_pre_email')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text required">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_profile['E-mail'] ?></span> <small><?php echo $lang_profile['E-mail help'] ?></small></label><br />
						<span class="fld-input"><input type="email" data-suggest-role="email" id="fld<?php echo $forum_page['fld_count'] ?>" name="req_email1" value="<?php echo(isset($_POST['req_email1']) ? forum_htmlencode($_POST['req_email1']) : '') ?>" size="35" maxlength="80" required spellcheck="false" /></span>
					</div>
				</div>
<?php ($hook = get_hook('rg_register_pre_username')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count']; if ($forum_config['o_regs_verify'] == '0') echo ' prepend-top'; ?>">
					<div class="sf-box text required">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_profile['Username'] ?></span> <small><?php echo $lang_profile['Username help'] ?></small></label><br />
						<span class="fld-input"><input type="text" data-suggest-role="username" id="fld<?php echo $forum_page['fld_count'] ?>" name="req_username" value="<?php echo(isset($_POST['req_username']) ? forum_htmlencode($_POST['req_username']) : '') ?>" size="35" maxlength="25" required spellcheck="false" /></span>
					</div>
				</div>
<?php ($hook = get_hook('rg_register_pre_password')) ? eval($hook) : null; ?>
<?php if ($forum_config['o_regs_verify'] == '0'): ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text required">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_profile['Password'] ?></span> <small><?php echo $lang_profile['Password help'] ?></small></label><br />
						<span class="fld-input"><input type="<?php echo($forum_config['o_mask_passwords'] == '1' ? 'password' : 'text') ?>" id="fld<?php echo $forum_page['fld_count'] ?>" name="req_password1" size="35" value="<?php if (isset($_POST['req_password1'])) echo forum_htmlencode($_POST['req_password1']); ?>" required autocomplete="off" /></span>
					</div>
				</div>
	<?php ($hook = get_hook('rg_register_pre_confirm_password')) ? eval($hook) : null; ?>
	<?php if ($forum_config['o_mask_passwords'] == '1'): ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text required">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_profile['Confirm password'] ?></span> <small><?php echo $lang_profile['Confirm password help'] ?></small></label><br />
						<span class="fld-input"><input type="password" id="fld<?php echo $forum_page['fld_count'] ?>" name="req_password2" size="35" value="<?php if (isset($_POST['req_password2'])) echo forum_htmlencode($_POST['req_password2']); ?>" required autocomplete="off" /></span>
					</div>
				</div>
	<?php endif; ?>
<?php endif; ?>
<?php ($hook = get_hook('rg_register_pre_email_confirm')) ? eval($hook) : null;

		$languages = array();
		$d = dir(FORUM_ROOT.'lang');
		while (($entry = $d->read()) !== false)
		{
			if ($entry != '.' && $entry != '..' && is_dir(FORUM_ROOT.'lang/'.$entry) && file_exists(FORUM_ROOT.'lang/'.$entry.'/common.php'))
				$languages[] = $entry;
		}
		$d->close();

		($hook = get_hook('rg_register_pre_language')) ? eval($hook) : null;

		// Only display the language selection box if there's more than one language available
		if (count($languages) > 1)
		{
			natcasesort($languages);

?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box select">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_profile['Language'] ?></span></label><br />
						<span class="fld-input"><select id="fld<?php echo $forum_page['fld_count'] ?>" name="language">
<?php

			$select_lang = isset($_POST['language']) ? $_POST['language'] : $forum_config['o_default_lang'];
			foreach ($languages as $lang)
			{
				if ($select_lang == $lang)
					echo "\t\t\t\t\t\t".'<option value="'.$lang.'" selected="selected">'.$lang.'</option>'."\n";
				else
					echo "\t\t\t\t\t\t".'<option value="'.$lang.'">'.$lang.'</option>'."\n";
			}

?>
						</select></span>
					</div>
				</div>
<?php

		}


		($hook = get_hook('rg_register_pre_group_end')) ? eval($hook) : null;
?>
			</div>
<?php ($hook = get_hook('rg_register_group_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="register" value="<?php echo $lang_profile['Register'] ?>" /></span>
			</div>
		</form>
	</div>
<?php

($hook = get_hook('rg_end')) ? eval($hook) : null;

$tpl_temp = forum_trim(ob_get_contents());
$tpl_main = str_replace('<!-- forum_main -->', $tpl_temp, $tpl_main);
ob_end_clean();
// END SUBST - <!-- forum_main -->

require FORUM_ROOT.'footer.php';
