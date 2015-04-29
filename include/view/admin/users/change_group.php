<?php
namespace punbb;

($hook = get_hook('aus_change_group_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo __('Change group head', 'admin_users') ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo link('admin_users') ?>?action=modify_users">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token(link('admin_users').'?action=modify_users') ?>" />
				<input type="hidden" name="users" value="<?php echo implode(',', $users) ?>" />
			</div>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><span><?php echo __('Move users legend', 'admin_users') ?></span></legend>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box select">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Move users to label', 'admin_users') ?></span></label><br />
						<span class="fld-input"><select id="fld<?php echo $forum_page['fld_count'] ?>" name="move_to_group">
<?php
	while ($cur_group = db()->fetch_assoc($result)) {
		if ($cur_group['g_id'] == config()->o_default_user_group)	// Pre-select the default Members group
			echo "\t\t\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'" selected="selected">'.forum_htmlencode($cur_group['g_title']).'</option>'."\n";
		else
			echo "\t\t\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'">'.forum_htmlencode($cur_group['g_title']).'</option>'."\n";
	}
?>
						</select></span>
					</div>
				</div>
			</fieldset>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="change_group_comply" value="<?php echo __('Change group', 'admin_users') ?>" /></span>
				<span class="cancel"><input type="submit" name="change_group_cancel" value="<?php echo __('Cancel', 'admin_common') ?>" /></span>
			</div>
		</form>
	</div>
<?php

($hook = get_hook('aus_change_group_end')) ? eval($hook) : null;
