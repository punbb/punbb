<?php
namespace punbb;

($hook = get_hook('aop_email_output_start')) ? eval($hook) : null;

?>
	<div class="main-content frm parted">
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo forum_link('admin_settings_email') ?>">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token(forum_link('admin_settings_email')) ?>" />
				<input type="hidden" name="form_sent" value="1" />
			</div>
			<div class="content-head">
				<h2 class="hn"><span><?php echo __('E-mail addresses', 'admin_settings') ?></span></h2>
			</div>
<?php ($hook = get_hook('aop_email_pre_addresses_fieldset')) ? eval($hook) : null; ?>
				<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
					<legend class="group-legend"><strong><?php echo __('E-mail addresses legend', 'admin_settings') ?></strong></legend>
<?php ($hook = get_hook('aop_email_pre_admin_email')) ? eval($hook) : null; ?>
					<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
						<div class="sf-box text">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Admin e-mail', 'admin_settings') ?></span></label><br />
							<span class="fld-input"><input type="email" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[admin_email]" size="50" maxlength="80"
								value="<?php echo forum_htmlencode(config()->o_admin_email) ?>" /></span>
						</div>
					</div>
<?php ($hook = get_hook('aop_email_pre_webmaster_email')) ? eval($hook) : null; ?>
					<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
						<div class="sf-box text">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Webmaster e-mail label', 'admin_settings') ?></span><small><?php echo __('Webmaster e-mail help', 'admin_settings') ?></small></label><br />
							<span class="fld-input"><input type="email" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[webmaster_email]" size="50" maxlength="80"
								value="<?php echo forum_htmlencode(config()->o_webmaster_email) ?>" /></span>
						</div>
					</div>
<?php ($hook = get_hook('aop_email_pre_mailing_list')) ? eval($hook) : null; ?>
					<div class="txt-set set<?php echo ++$forum_page['item_count'] ?>">
						<div class="txt-box textarea">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Mailing list label', 'admin_settings') ?></span><small><?php echo __('Mailing list help', 'admin_settings') ?></small></label>
							<div class="txt-input"><span class="fld-input"><textarea id="fld<?php echo $forum_page['fld_count'] ?>" name="form[mailing_list]"
								rows="5" cols="55"><?php echo forum_htmlencode(config()->o_mailing_list) ?></textarea></span></div>
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
				<h2 class="hn"><span><?php echo __('E-mail server', 'admin_settings') ?></span></h2>
			</div>
				<div class="ct-box">
					<p><?php echo __('E-mail server info', 'admin_settings') ?></p>
				</div>
<?php ($hook = get_hook('aop_email_pre_smtp_fieldset')) ? eval($hook) : null; ?>
				<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
					<legend class="group-legend"><strong><?php echo __('E-mail server legend', 'admin_settings') ?></strong></legend>
<?php ($hook = get_hook('aop_email_pre_smtp_host')) ? eval($hook) : null; ?>
					<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
						<div class="sf-box text">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('SMTP address label', 'admin_settings') ?></span><small><?php echo __('SMTP address help', 'admin_settings') ?></small></label><br />
							<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[smtp_host]" size="35" maxlength="100"
								value="<?php echo forum_htmlencode(config()->o_smtp_host) ?>" /></span>
						</div>
					</div>
<?php ($hook = get_hook('aop_email_pre_smtp_user')) ? eval($hook) : null; ?>
					<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
						<div class="sf-box text">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('SMTP username label', 'admin_settings') ?></span><small><?php echo __('SMTP username help', 'admin_settings') ?></small></label><br />
							<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[smtp_user]" size="35" maxlength="50"
								value="<?php echo forum_htmlencode(config()->o_smtp_user) ?>" /></span>
						</div>
					</div>
<?php ($hook = get_hook('aop_email_pre_smtp_pass')) ? eval($hook) : null; ?>
					<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
						<div class="sf-box text">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('SMTP password label', 'admin_settings') ?></span><small><?php echo __('SMTP password help', 'admin_settings') ?></small></label><br />
							<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[smtp_pass]" size="35" maxlength="50"
								value="<?php echo forum_htmlencode(config()->o_smtp_pass) ?>" /></span>
						</div>
					</div>
<?php ($hook = get_hook('aop_email_pre_smtp_ssl')) ? eval($hook) : null; ?>
					<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
						<div class="sf-box checkbox">
							<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[smtp_ssl]" value="1"
								<?php if (config()->o_smtp_ssl == '1') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo __('SMTP SSL', 'admin_settings') ?></span> <?php echo __('SMTP SSL label', 'admin_settings') ?></label>
						</div>
					</div>
<?php ($hook = get_hook('aop_email_pre_smtp_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php ($hook = get_hook('aop_email_smtp_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="save" value="<?php echo __('Save changes', 'admin_common') ?>" /></span>
			</div>
		</form>
	</div>
