<?php
namespace punbb;

($hook = get_hook('aop_registration_output_start')) ? eval($hook) : null;

?>
	<div class="main-content main-frm">
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo link('admin_settings_registration') ?>">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token(link('admin_settings_registration')) ?>" />
				<input type="hidden" name="form_sent" value="1" />
			</div>
			<div class="content-head">
				<h2 class="hn"><span><?php echo __('Registration new', 'admin_settings') ?></span></h2>
			</div>
			<div class="ct-box">
				<p><?php echo __('New reg info', 'admin_settings') ?></p>
			</div>
<?php ($hook = get_hook('aop_registration_pre_new_regs_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><span><?php echo __('Registration new legend', 'admin_settings') ?></span></legend>
<?php ($hook = get_hook('aop_registration_pre_allow_new_regs_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[regs_allow]" value="1"
							<?php if (config()->o_regs_allow == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo __('Allow new reg', 'admin_settings') ?></span> <?php echo __('Allow new reg label', 'admin_settings') ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_registration_pre_verify_regs_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[regs_verify]" value="1"
							<?php if (config()->o_regs_verify == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo __('Verify reg', 'admin_settings') ?></span> <?php echo __('Verify reg label', 'admin_settings') ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_registration_pre_email_fieldset')) ? eval($hook) : null; ?>
				<fieldset class="mf-set set<?php echo ++$forum_page['item_count'] ?>">
					<legend><span><?php echo __('Reg e-mail group', 'admin_settings') ?></span></legend>
					<div class="mf-box">
						<div class="mf-item">
							<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[allow_banned_email]" value="1"
								<?php if (config()->p_allow_banned_email == '1') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo __('Allow banned label', 'admin_settings') ?></label>
						</div>
						<div class="mf-item">
							<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[allow_dupe_email]" value="1"
								<?php if (config()->p_allow_dupe_email == '1') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo __('Allow dupe label', 'admin_settings') ?></label>
						</div>
<?php ($hook = get_hook('aop_registration_new_email_option')) ? eval($hook) : null; ?>
					</div>
<?php ($hook = get_hook('aop_registration_pre_email_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php ($hook = get_hook('aop_registration_email_fieldset_end')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[regs_report]" value="1"
							<?php if (config()->o_regs_report == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo __('Report new reg', 'admin_settings') ?></span> <?php echo __('Report new reg label', 'admin_settings') ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_registration_pre_email_setting_fieldset')) ? eval($hook) : null; ?>
				<fieldset class="mf-set set<?php echo ++$forum_page['item_count'] ?>">
					<legend><span><?php echo __('E-mail setting group', 'admin_settings') ?></span></legend>
					<div class="mf-box">
						<div class="mf-item">
							<span class="fld-input"><input type="radio" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[default_email_setting]" value="0"
								<?php if (config()->o_default_email_setting == '0') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo __('Display e-mail label', 'admin_settings') ?></label>
						</div>
						<div class="mf-item">
							<span class="fld-input"><input type="radio" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[default_email_setting]" value="1"
								<?php if (config()->o_default_email_setting == '1') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo __('Allow form e-mail label', 'admin_settings') ?></label>
						</div>
						<div class="mf-item">
							<span class="fld-input"><input type="radio" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[default_email_setting]" value="2"
								<?php if (config()->o_default_email_setting == '2') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo __('Disallow form e-mail label', 'admin_settings') ?></label>
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
				<h2 class="hn"><span><?php echo __('Registration rules', 'admin_settings') ?></span></h2>
			</div>
				<div class="ct-box">
					<p><?php echo __('Registration rules info', 'admin_settings') ?></p>
				</div>
<?php ($hook = get_hook('aop_registration_pre_rules_fieldset')) ? eval($hook) : null; ?>
				<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
					<legend class="group-legend"><span><?php echo __('Registration rules legend', 'admin_settings') ?></span></legend>
<?php ($hook = get_hook('aop_registration_pre_rules_checkbox')) ? eval($hook) : null; ?>
					<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
						<div class="sf-box checkbox">
							<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[rules]" value="1"
								<?php if (config()->o_rules == '1') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo __('Require rules', 'admin_settings') ?></span><?php echo __('Require rules label', 'admin_settings') ?></label>
						</div>
					</div>
<?php ($hook = get_hook('aop_registration_pre_rules_text')) ? eval($hook) : null; ?>
					<div class="txt-set set<?php echo ++$forum_page['item_count'] ?>">
						<div class="txt-box textarea">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Compose rules label', 'admin_settings') ?></span><small><?php echo __('Compose rules help', 'admin_settings') ?></small></label>
							<div class="txt-input"><span class="fld-input"><textarea id="fld<?php echo $forum_page['fld_count'] ?>" name="form[rules_message]"
								rows="10" cols="55"><?php echo forum_htmlencode(config()->o_rules_message) ?></textarea></span></div>
						</div>
					</div>
<?php ($hook = get_hook('aop_registration_pre_rules_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php ($hook = get_hook('aop_registration_rules_fieldset_end')) ? eval($hook) : null; ?>
				<div class="frm-buttons">
					<span class="submit primary"><input type="submit" name="save" value="<?php echo __('Save changes', 'admin_common') ?>" /></span>
				</div>
		</form>
	</div>
