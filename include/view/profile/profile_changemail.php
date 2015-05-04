<?php
namespace punbb;

global $errors;

($hook = get_hook('pf_change_email_normal_output_start')) ? eval($hook) : null;

?>
	<div class="main-head">
		<h2 class="hn"><span><?php printf((user()->id == $id) ?
			__('Profile welcome', 'profile') : __('Profile welcome user', 'profile'), forum_htmlencode($user['username'])) ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<div class="ct-box info-box">
			<?php echo $forum_page['frm_info']."\n" ?>
		</div>

		<?php template()->helper('errors', [
			'errors_title' => __('Change e-mail errors', 'profile'),
			'errors' => $errors
		]) ?>

		<div id="req-msg" class="req-warn ct-box error-box">
			<p class="important"><?= __('Required warn') ?></p>
		</div>
		<form id="afocus" class="frm-form" method="post" accept-charset="utf-8" action="<?php echo $forum_page['form_action'] ?>">
			<div class="hidden">
				<?php echo implode("\n\t\t\t", $forum_page['hidden_fields'])."\n" ?>
			</div>
<?php ($hook = get_hook('pf_change_email_normal_pre_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?= __('Required information') ?></strong></legend>
<?php ($hook = get_hook('pf_change_email_normal_pre_new_email')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text required">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('New e-mail', 'profile') ?></span></label><br />
						<span class="fld-input"><input type="email" id="fld<?php echo $forum_page['fld_count'] ?>" name="req_new_email" size="35" maxlength="80" value="<?php if (isset($_POST['req_new_email'])) echo forum_htmlencode($_POST['req_new_email']); ?>" required /></span>
					</div>
				</div>
<?php ($hook = get_hook('pf_change_email_normal_pre_password')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text required">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Password', 'profile') ?></span><small><?= __('Old password help', 'profile') ?></small></label><br />
						<span class="fld-input"><input type="<?php echo(config()->o_mask_passwords == '1' ? 'password' : 'text') ?>" id="fld<?php echo $forum_page['fld_count'] ?>" name="req_password" size="35" value="<?php if (isset($_POST['req_password'])) echo forum_htmlencode($_POST['req_password']); ?>" required autocomplete="off" /></span>
					</div>
				</div>
<?php ($hook = get_hook('pf_change_email_normal_pre_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('pf_change_email_normal_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="update" value="<?= __('Submit') ?>" /></span>
				<span class="cancel"><input type="submit" name="cancel" value="<?= __('Cancel') ?>" formnovalidate /></span>
			</div>
		</form>
	</div>
<?php

($hook = get_hook('pf_change_email_normal_end')) ? eval($hook) : null;
