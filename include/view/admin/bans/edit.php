<?php
namespace punbb;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?= __('Ban advanced heading', 'admin_bans') ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<div class="ct-box warn-box">
			<p class="warn"><?= __('Ban IP warning', 'admin_bans') ?></p>
		</div>
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo link('admin_bans') ?>">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token(link('admin_bans')) ?>" />
				<input type="hidden" name="mode" value="<?php echo $mode ?>" />
				<?php if ($mode == 'edit') { ?>
					<input type="hidden" name="ban_id" value="<?php echo $ban_id ?>" />
				<?php } ?>
			</div>

			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><span><?= __('Ban criteria legend', 'admin_bans') ?></span></legend>

				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Username to ban label', 'admin_bans') ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="ban_user" size="40" maxlength="25" value="<?php if (isset($ban_user)) echo forum_htmlencode($ban_user); ?>" /></span>
					</div>
				</div>

				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('E-mail/domain to ban label', 'admin_bans') ?></span> <small><?= __('E-mail/domain help', 'admin_bans') ?></small></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="ban_email" size="40" maxlength="80" value="<?php if (isset($ban_email)) echo forum_htmlencode(strtolower($ban_email)); ?>" /></span>
					</div>
				</div>

				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('IP-addresses to ban label', 'admin_bans') ?></span> <small><?= __('IP-addresses help', 'admin_bans'); if ($ban_user != '' && isset($user_id)) echo ' '.
							__('IP-addresses help stats', 'admin_bans') . '<a href="'.link('admin_users').'?ip_stats='.$user_id.'">'.
							__('IP-addresses help link', 'admin_bans') . '</a>' ?></small></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="ban_ip" size="40" maxlength="255" value="<?php if (isset($ban_ip)) echo $ban_ip; ?>" /></span>
					</div>
				</div>

				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Ban message label', 'admin_bans') ?></span> <small><?= __('Ban message help', 'admin_bans') ?></small></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="ban_message" size="40" maxlength="255" value="<?php if (isset($ban_message)) echo forum_htmlencode($ban_message); ?>" /></span>
					</div>
				</div>

				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Expire date label', 'admin_bans') ?></span> <small><?= __('Expire date help', 'admin_bans') ?></small></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="ban_expire" size="20" maxlength="10" value="<?php if (isset($ban_expire)) echo $ban_expire; ?>" /></span>
					</div>
				</div>

			</fieldset>

			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="add_edit_ban" value=" <?= __('Save ban', 'admin_bans') ?>" /></span>
			</div>
		</form>
	</div>
<?php

