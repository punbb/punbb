<?php

($hook = get_hook('pf_change_pass_normal_output_start')) ? eval($hook) : null;

?>
	<div class="main-head">
		<h2 class="hn"><span><?php echo $forum_page['own_profile'] ? $lang_profile['Change your password'] : sprintf($lang_profile['Change user password'], forum_htmlencode($user['username'])) ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<?= helper('errors', array(
			'errors_title' => $lang_profile['Change pass errors']
		)) ?>

		<div id="req-msg" class="req-warn ct-box error-box">
			<p class="important"><?php echo $lang_common['Required warn'] ?></p>
		</div>
		<form id="afocus" class="frm-form" method="post" accept-charset="utf-8" action="<?php echo $forum_page['form_action'] ?>" autocomplete="off">
			<div class="hidden">
				<?php echo implode("\n\t\t\t\t", $forum_page['hidden_fields'])."\n" ?>
			</div>
<?php ($hook = get_hook('pf_change_pass_normal_pre_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo $lang_common['Required information'] ?></strong></legend>
<?php ($hook = get_hook('pf_change_pass_normal_pre_old_password')) ? eval($hook) : null; ?>
<?php if (!$forum_user['is_admmod'] || $forum_user['id'] == $id): ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text required">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_profile['Old password'] ?></span> <small><?php echo $lang_profile['Old password help'] ?></small></label><br />
						<span class="fld-input"><input type="<?php echo($forum_config['o_mask_passwords'] == '1' ? 'password' : 'text') ?>" id="fld<?php echo $forum_page['fld_count'] ?>" name="req_old_password" size="35" value="<?php if (isset($_POST['req_old_password'])) echo forum_htmlencode($_POST['req_old_password']); ?>" required /></span>
					</div>
				</div>
<?php endif; ($hook = get_hook('pf_change_pass_normal_pre_new_password')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count']; if ($forum_config['o_mask_passwords'] == '1') echo ' prepend-top'; ?>">
					<div class="sf-box text required">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_profile['New password'] ?></span> <small><?php echo $lang_profile['Password help'] ?></small></label><br />
						<span class="fld-input"><input type="<?php echo($forum_config['o_mask_passwords'] == '1' ? 'password' : 'text') ?>" id="fld<?php echo $forum_page['fld_count'] ?>" name="req_new_password1" size="35" value="<?php if (isset($_POST['req_new_password1'])) echo forum_htmlencode($_POST['req_new_password1']); ?>" required /></span><br />
					</div>
				</div>
<?php ($hook = get_hook('pf_change_pass_normal_pre_new_password_confirm')) ? eval($hook) : null; ?>
<?php if ($forum_config['o_mask_passwords'] == '1'): ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text required">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_profile['Confirm new password'] ?></span> <small><?php echo $lang_profile['Confirm password help'] ?></small></label><br />
						<span class="fld-input"><input type="<?php echo($forum_config['o_mask_passwords'] == '1' ? 'password' : 'text') ?>" id="fld<?php echo $forum_page['fld_count'] ?>" name="req_new_password2" size="35" value="<?php if (isset($_POST['req_new_password2'])) echo forum_htmlencode($_POST['req_new_password2']); ?>" required /></span><br />
					</div>
				</div>
<?php endif; ?>
<?php ($hook = get_hook('pf_change_pass_normal_pre_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('pf_change_pass_normal_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="update" value="<?php echo $lang_common['Submit'] ?>" /></span>
				<span class="cancel"><input type="submit" name="cancel" value="<?php echo $lang_common['Cancel'] ?>" formnovalidate /></span>
			</div>
		</form>
	</div>
<?php

($hook = get_hook('pf_change_pass_normal_end')) ? eval($hook) : null;