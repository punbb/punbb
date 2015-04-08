<?php

($hook = get_hook('agr_del_group_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php printf(__('Remove group head', 'admin_groups'), forum_htmlencode($group_info['title']), $group_info['num_members']) ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo forum_link($forum_url['admin_groups']) ?>?del_group=<?php echo $group_id ?>">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token(forum_link($forum_url['admin_groups']).'?del_group='.$group_id) ?>" />
			</div>
<?php ($hook = get_hook('agr_del_group_pre_del_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group set<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><span><?php echo __('Remove group legend', 'admin_groups') ?></span></legend>
<?php ($hook = get_hook('agr_del_group_pre_move_to_group')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box select">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Move users to', 'admin_groups') ?></span> <small><?php echo __('Remove group help', 'admin_groups') ?></small></label><br />
						<span class="fld-input"><select id="fld<?php echo $forum_page['fld_count'] ?>" name="move_to_group">
<?php
	while ($cur_group = $forum_db->fetch_assoc($result)) {
		if ($cur_group['g_id'] == $forum_config['o_default_user_group'])	// Pre-select the default Members group
			echo "\t\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'" selected="selected">'.forum_htmlencode($cur_group['g_title']).'</option>'."\n";
		else
			echo "\t\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'">'.forum_htmlencode($cur_group['g_title']).'</option>'."\n";
	}
?>

						</select></span>
					</div>
				</div>
<?php ($hook = get_hook('agr_del_group_pre_del_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('agr_del_group_del_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="del_group" value="<?php echo __('Remove group', 'admin_groups') ?>" /></span>
				<span class="cancel"><input type="submit" name="del_group_cancel" value="<?php echo __('Cancel', 'admin_common') ?>" /></span>
			</div>
		</form>
	</div>
<?php

($hook = get_hook('agr_del_group_end')) ? eval($hook) : null;
