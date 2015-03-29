<?php

($hook = get_hook('aop_email_output_start')) ? eval($hook) : null;

?>
	<div class="main-content frm parted">
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo forum_link($forum_url['admin_settings_email']) ?>">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token(forum_link($forum_url['admin_settings_email'])) ?>" />
				<input type="hidden" name="form_sent" value="1" />
			</div>
			<div class="content-head">
				<h2 class="hn"><span><?php echo $lang_admin_settings['E-mail addresses'] ?></span></h2>
			</div>
<?php ($hook = get_hook('aop_email_pre_addresses_fieldset')) ? eval($hook) : null; ?>
				<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
					<legend class="group-legend"><strong><?php echo $lang_admin_settings['E-mail addresses legend'] ?></strong></legend>
<?php ($hook = get_hook('aop_email_pre_admin_email')) ? eval($hook) : null; ?>
					<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
						<div class="sf-box text">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Admin e-mail'] ?></span></label><br />
							<span class="fld-input"><input type="email" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[admin_email]" size="50" maxlength="80" value="<?php echo forum_htmlencode($forum_config['o_admin_email']) ?>" /></span>
						</div>
					</div>
<?php ($hook = get_hook('aop_email_pre_webmaster_email')) ? eval($hook) : null; ?>
					<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
						<div class="sf-box text">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Webmaster e-mail label'] ?></span><small><?php echo $lang_admin_settings['Webmaster e-mail help'] ?></small></label><br />
							<span class="fld-input"><input type="email" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[webmaster_email]" size="50" maxlength="80" value="<?php echo forum_htmlencode($forum_config['o_webmaster_email']) ?>" /></span>
						</div>
					</div>
<?php ($hook = get_hook('aop_email_pre_mailing_list')) ? eval($hook) : null; ?>
					<div class="txt-set set<?php echo ++$forum_page['item_count'] ?>">
						<div class="txt-box textarea">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Mailing list label'] ?></span><small><?php echo $lang_admin_settings['Mailing list help'] ?></small></label>
							<div class="txt-input"><span class="fld-input"><textarea id="fld<?php echo $forum_page['fld_count'] ?>" name="form[mailing_list]" rows="5" cols="55"><?php echo forum_htmlencode($forum_config['o_mailing_list']) ?></textarea></span></div>
						</div>
					</div>
<?php ($hook = get_hook('aop_email_pre_addresses_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php

($hook = get_hook('aop_email_addresses_fieldset_end')) ? eval($hook) : null;

// Reset counter
$forum_page['group_count'] = $forum_page['item_count'] = 0;

?>
			<div class="content-head">
				<h2 class="hn"><span><?php echo $lang_admin_settings['E-mail server'] ?></span></h2>
			</div>
				<div class="ct-box">
					<p><?php echo $lang_admin_settings['E-mail server info'] ?></p>
				</div>
<?php ($hook = get_hook('aop_email_pre_smtp_fieldset')) ? eval($hook) : null; ?>
				<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
					<legend class="group-legend"><strong><?php echo $lang_admin_settings['E-mail server legend'] ?></strong></legend>
<?php ($hook = get_hook('aop_email_pre_smtp_host')) ? eval($hook) : null; ?>
					<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
						<div class="sf-box text">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['SMTP address label'] ?></span><small><?php echo $lang_admin_settings['SMTP address help'] ?></small></label><br />
							<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[smtp_host]" size="35" maxlength="100" value="<?php echo forum_htmlencode($forum_config['o_smtp_host']) ?>" /></span>
						</div>
					</div>
<?php ($hook = get_hook('aop_email_pre_smtp_user')) ? eval($hook) : null; ?>
					<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
						<div class="sf-box text">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['SMTP username label'] ?></span><small><?php echo $lang_admin_settings['SMTP username help'] ?></small></label><br />
							<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[smtp_user]" size="35" maxlength="50" value="<?php echo forum_htmlencode($forum_config['o_smtp_user']) ?>" /></span>
						</div>
					</div>
<?php ($hook = get_hook('aop_email_pre_smtp_pass')) ? eval($hook) : null; ?>
					<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
						<div class="sf-box text">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['SMTP password label'] ?></span><small><?php echo $lang_admin_settings['SMTP password help'] ?></small></label><br />
							<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[smtp_pass]" size="35" maxlength="50" value="<?php echo forum_htmlencode($forum_config['o_smtp_pass']) ?>" /></span>
						</div>
					</div>
<?php ($hook = get_hook('aop_email_pre_smtp_ssl')) ? eval($hook) : null; ?>
					<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
						<div class="sf-box checkbox">
							<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[smtp_ssl]" value="1"<?php if ($forum_config['o_smtp_ssl'] == '1') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['SMTP SSL'] ?></span> <?php echo $lang_admin_settings['SMTP SSL label'] ?></label>
						</div>
					</div>
<?php ($hook = get_hook('aop_email_pre_smtp_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php ($hook = get_hook('aop_email_smtp_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="save" value="<?php echo $lang_admin_common['Save changes'] ?>" /></span>
			</div>
		</form>
	</div>
