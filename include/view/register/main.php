<?php

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
?>

	<?= helper('errors', array(
		'errors_title' => $lang_profile['Register errors']
	)) ?>

		<div id="req-msg" class="req-warn ct-box error-box">
			<p class="important"><?= __('Required warn') ?></p>
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
