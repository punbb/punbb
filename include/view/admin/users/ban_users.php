<?php
namespace punbb;

($hook = get_hook('aus_ban_users_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo __('Ban users', 'admin_users') ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<div class="ct-box">
			<p><?php echo __('Mass ban info', 'admin_users') ?></p>
		</div>
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo link('admin_users') ?>?action=modify_users">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token(link('admin_users').'?action=modify_users') ?>" />
				<input type="hidden" name="users" value="<?php echo implode(',', $users) ?>" />
			</div>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><span><?php echo __('Ban settings legend', 'admin_users') ?></span></legend>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Ban message label', 'admin_bans') ?></span> <small><?= __('Ban message help', 'admin_bans') ?></small></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="ban_message" size="50" maxlength="255" /></span>
					</div>
				</div>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Expire date label', 'admin_bans') ?></span> <small><?= __('Expire date help', 'admin_bans') ?></small></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="ban_expire" size="17" maxlength="10" /></span>
					</div>
				</div>
			</fieldset>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="ban_users_comply" value="<?php echo __('Ban', 'admin_users') ?>" /></span>
			</div>
		</form>
	</div>
<?php

($hook = get_hook('aus_ban_users_end')) ? eval($hook) : null;
