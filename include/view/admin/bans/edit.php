<?php
namespace punbb;

($hook = get_hook('aba_add_edit_ban_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?= __('Ban advanced heading', 'admin_bans') ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<div class="ct-box warn-box">
			<p class="warn"><?= __('Ban IP warning', 'admin_bans') ?></p>
		</div>
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo forum_link($forum_url['admin_bans']) ?>">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token(forum_link($forum_url['admin_bans'])) ?>" />
				<input type="hidden" name="mode" value="<?php echo $mode ?>" />
<?php if ($mode == 'edit'): ?>
				<input type="hidden" name="ban_id" value="<?php echo $ban_id ?>" />
<?php endif; ?>
			</div>
<?php ($hook = get_hook('aba_add_edit_ban_pre_criteria_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><span><?= __('Ban criteria legend', 'admin_bans') ?></span></legend>
<?php ($hook = get_hook('aba_add_edit_ban_pre_username')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Username to ban label', 'admin_bans') ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="ban_user" size="40" maxlength="25" value="<?php if (isset($ban_user)) echo forum_htmlencode($ban_user); ?>" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aba_add_edit_ban_pre_email')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('E-mail/domain to ban label', 'admin_bans') ?></span> <small><?= __('E-mail/domain help', 'admin_bans') ?></small></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="ban_email" size="40" maxlength="80" value="<?php if (isset($ban_email)) echo forum_htmlencode(strtolower($ban_email)); ?>" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aba_add_edit_ban_pre_ip')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('IP-addresses to ban label', 'admin_bans') ?></span> <small><?= __('IP-addresses help', 'admin_bans'); if ($ban_user != '' && isset($user_id)) echo ' '.
							__('IP-addresses help stats', 'admin_bans') . '<a href="'.forum_link($forum_url['admin_users']).'?ip_stats='.$user_id.'">'.
							__('IP-addresses help link', 'admin_bans') . '</a>' ?></small></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="ban_ip" size="40" maxlength="255" value="<?php if (isset($ban_ip)) echo $ban_ip; ?>" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aba_add_edit_ban_pre_message')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Ban message label', 'admin_bans') ?></span> <small><?= __('Ban message help', 'admin_bans') ?></small></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="ban_message" size="40" maxlength="255" value="<?php if (isset($ban_message)) echo forum_htmlencode($ban_message); ?>" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aba_add_edit_ban_pre_expire')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Expire date label', 'admin_bans') ?></span> <small><?= __('Expire date help', 'admin_bans') ?></small></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="ban_expire" size="20" maxlength="10" value="<?php if (isset($ban_expire)) echo $ban_expire; ?>" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aba_add_edit_ban_criteria_pre_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('aba_add_edit_ban_criteria_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="add_edit_ban" value=" <?= __('Save ban', 'admin_bans') ?>" /></span>
			</div>
		</form>
	</div>
<?php

($hook = get_hook('aba_add_edit_ban_end')) ? eval($hook) : null;
