<?php

($hook = get_hook('pf_change_pass_key_output_start')) ? eval($hook) : null;

?>
	<div class="main-head">
		<h2 class="hn"><span><?php echo $forum_page['own_profile'] ?
			__('Change your password', 'profile') :
			sprintf(__('Change user password', 'profile'), forum_htmlencode($user['username'])) ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<?= helper('errors', array(
			'errors_title' => __('Change pass errors', 'profile')
		)) ?>

		<div id="req-msg" class="req-warn ct-box error-box">
			<p class="important"><?= __('Required warn') ?></p>
		</div>
		<form id="afocus" class="frm-form" method="post" accept-charset="utf-8" action="<?php echo $forum_page['form_action'] ?>" autocomplete="off">
			<div class="hidden">
				<input type="hidden" name="form_sent" value="1" />
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token($forum_page['form_action']) ?>" />
			</div>
<?php ($hook = get_hook('pf_change_pass_key_pre_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?= __('Required information') ?></strong></legend>
<?php ($hook = get_hook('pf_change_pass_key_pre_new_password')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text required">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('New password', 'profile') ?></span> <small><?= __('Password help', 'profile') ?></small></label><br />
						<span class="fld-input"><input type="<?php echo($forum_config['o_mask_passwords'] == '1' ? 'password' : 'text') ?>" id="fld<?php echo $forum_page['fld_count'] ?>" name="req_new_password1" size="35" value="<?php if (isset($_POST['req_new_password1'])) echo forum_htmlencode($_POST['req_new_password1']); ?>" required autocomplete="off" /></span><br />
					</div>
				</div>
<?php ($hook = get_hook('pf_change_pass_key_pre_new_password_confirm')) ? eval($hook) : null; ?>
<?php if ($forum_config['o_mask_passwords'] == '1'): ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text required">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Confirm new password', 'profile') ?></span> <small><?= __('Confirm password help', 'profile') ?></small></label><br />
						<span class="fld-input"><input type="password" id="fld<?php echo $forum_page['fld_count'] ?>" name="req_new_password2" size="35" value="<?php if (isset($_POST['req_new_password2'])) echo forum_htmlencode($_POST['req_new_password2']); ?>" required autocomplete="off" /></span><br />
					</div>
				</div>
<?php endif; ?>
<?php ($hook = get_hook('pf_change_pass_key_pre_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('pf_change_pass_key_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="update" value="<?= __('Submit') ?>" /></span>
				<span class="cancel"><input type="submit" name="cancel" value="<?= __('Cancel') ?>" formnovalidate /></span>
			</div>
		</form>
	</div>

<?php

($hook = get_hook('pf_change_pass_key_end')) ? eval($hook) : null;
