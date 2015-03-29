<?php

($hook = get_hook('aop_registration_output_start')) ? eval($hook) : null;

?>
	<div class="main-content main-frm">
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo forum_link($forum_url['admin_settings_registration']) ?>">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token(forum_link($forum_url['admin_settings_registration'])) ?>" />
				<input type="hidden" name="form_sent" value="1" />
			</div>
			<div class="content-head">
				<h2 class="hn"><span><?php echo $lang_admin_settings['Registration new'] ?></span></h2>
			</div>
			<div class="ct-box">
				<p><?php echo $lang_admin_settings['New reg info'] ?></p>
			</div>
<?php ($hook = get_hook('aop_registration_pre_new_regs_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><span><?php echo $lang_admin_settings['Registration new legend'] ?></span></legend>
<?php ($hook = get_hook('aop_registration_pre_allow_new_regs_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[regs_allow]" value="1"<?php if ($forum_config['o_regs_allow'] == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Allow new reg'] ?></span> <?php echo $lang_admin_settings['Allow new reg label'] ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_registration_pre_verify_regs_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[regs_verify]" value="1"<?php if ($forum_config['o_regs_verify'] == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Verify reg'] ?></span> <?php echo $lang_admin_settings['Verify reg label'] ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_registration_pre_email_fieldset')) ? eval($hook) : null; ?>
				<fieldset class="mf-set set<?php echo ++$forum_page['item_count'] ?>">
					<legend><span><?php echo $lang_admin_settings['Reg e-mail group'] ?></span></legend>
					<div class="mf-box">
						<div class="mf-item">
							<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[allow_banned_email]" value="1"<?php if ($forum_config['p_allow_banned_email'] == '1') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo $lang_admin_settings['Allow banned label'] ?></label>
						</div>
						<div class="mf-item">
							<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[allow_dupe_email]" value="1"<?php if ($forum_config['p_allow_dupe_email'] == '1') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo $lang_admin_settings['Allow dupe label'] ?></label>
						</div>
<?php ($hook = get_hook('aop_registration_new_email_option')) ? eval($hook) : null; ?>
					</div>
<?php ($hook = get_hook('aop_registration_pre_email_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php ($hook = get_hook('aop_registration_email_fieldset_end')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[regs_report]" value="1"<?php if ($forum_config['o_regs_report'] == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Report new reg'] ?></span> <?php echo $lang_admin_settings['Report new reg label'] ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_registration_pre_email_setting_fieldset')) ? eval($hook) : null; ?>
				<fieldset class="mf-set set<?php echo ++$forum_page['item_count'] ?>">
					<legend><span><?php echo $lang_admin_settings['E-mail setting group'] ?></span></legend>
					<div class="mf-box">
						<div class="mf-item">
							<span class="fld-input"><input type="radio" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[default_email_setting]" value="0"<?php if ($forum_config['o_default_email_setting'] == '0') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo $lang_admin_settings['Display e-mail label'] ?></label>
						</div>
						<div class="mf-item">
							<span class="fld-input"><input type="radio" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[default_email_setting]" value="1"<?php if ($forum_config['o_default_email_setting'] == '1') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo $lang_admin_settings['Allow form e-mail label'] ?></label>
						</div>
						<div class="mf-item">
							<span class="fld-input"><input type="radio" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[default_email_setting]" value="2"<?php if ($forum_config['o_default_email_setting'] == '2') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo $lang_admin_settings['Disallow form e-mail label'] ?></label>
						</div>
<?php ($hook = get_hook('aop_registration_new_email_setting_option')) ? eval($hook) : null; ?>
					</div>
<?php ($hook = get_hook('aop_registration_pre_email_setting_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php ($hook = get_hook('aop_registration_email_setting_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php

	($hook = get_hook('aop_registration_new_regs_fieldset_end')) ? eval($hook) : null;

	// Reset counter
	$forum_page['group_count'] = $forum_page['item_count'] = 0;

?>
			<div class="content-head">
				<h2 class="hn"><span><?php echo $lang_admin_settings['Registration rules'] ?></span></h2>
			</div>
				<div class="ct-box">
					<p><?php echo $lang_admin_settings['Registration rules info'] ?></p>
				</div>
<?php ($hook = get_hook('aop_registration_pre_rules_fieldset')) ? eval($hook) : null; ?>
				<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
					<legend class="group-legend"><span><?php echo $lang_admin_settings['Registration rules legend'] ?></span></legend>
<?php ($hook = get_hook('aop_registration_pre_rules_checkbox')) ? eval($hook) : null; ?>
					<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
						<div class="sf-box checkbox">
							<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[rules]" value="1"<?php if ($forum_config['o_rules'] == '1') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Require rules'] ?></span><?php echo $lang_admin_settings['Require rules label'] ?></label>
						</div>
					</div>
<?php ($hook = get_hook('aop_registration_pre_rules_text')) ? eval($hook) : null; ?>
					<div class="txt-set set<?php echo ++$forum_page['item_count'] ?>">
						<div class="txt-box textarea">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Compose rules label'] ?></span><small><?php echo $lang_admin_settings['Compose rules help'] ?></small></label>
							<div class="txt-input"><span class="fld-input"><textarea id="fld<?php echo $forum_page['fld_count'] ?>" name="form[rules_message]" rows="10" cols="55"><?php echo forum_htmlencode($forum_config['o_rules_message']) ?></textarea></span></div>
						</div>
					</div>
<?php ($hook = get_hook('aop_registration_pre_rules_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php ($hook = get_hook('aop_registration_rules_fieldset_end')) ? eval($hook) : null; ?>
				<div class="frm-buttons">
					<span class="submit primary"><input type="submit" name="save" value="<?php echo $lang_admin_common['Save changes'] ?>" /></span>
				</div>
		</form>
	</div>
