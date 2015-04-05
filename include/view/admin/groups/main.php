<?php

($hook = get_hook('agr_main_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo $lang_admin_groups['Add group heading'] ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo forum_link($forum_url['admin_groups']) ?>?action=foo">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token(forum_link($forum_url['admin_groups']).'?action=foo') ?>" />
			</div>
<?php ($hook = get_hook('agr_pre_add_group_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><span><?php echo $lang_admin_groups['Add group legend'] ?></span></legend>
<?php ($hook = get_hook('agr_pre_add_base_group')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box select">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_groups['Base new group label'] ?></span></label><br />
						<span class="fld-input"><select id="fld<?php echo $forum_page['fld_count'] ?>" name="base_group">
<?php
while ($cur_group = $forum_db->fetch_assoc($result_groups))
	echo "\t\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].($cur_group['g_id'] == $forum_config['o_default_user_group'] ? '" selected="selected">' : '">').forum_htmlencode($cur_group['g_title']).'</option>'."\n";
?>
						</select></span>
					</div>
				</div>
<?php ($hook = get_hook('agr_pre_add_group_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('agr_add_group_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="add_group" value="<?php echo $lang_admin_groups['Add group'] ?> " /></span>
			</div>
		</form>
	</div>
<?php

	// Reset fieldset counter
	$forum_page['group_count'] = $forum_page['item_count'] = 0;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo $lang_admin_groups['Default group heading'] ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo forum_link($forum_url['admin_groups']) ?>?action=foo">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token(forum_link($forum_url['admin_groups']).'?action=foo') ?>" />
			</div>
<?php ($hook = get_hook('agr_pre_default_group_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><span><?php echo $lang_admin_groups['Default group legend'] ?></span></legend>
<?php ($hook = get_hook('agr_pre_default_group')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box select">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_groups['Default group label'] ?></span></label><br />
						<span class="fld-input"><select id="fld<?php echo $forum_page['fld_count'] ?>" name="default_group">
<?php

while ($cur_group = $forum_db->fetch_assoc($result_groups_default))
{
	if ($cur_group['g_id'] == $forum_config['o_default_user_group'])
		echo "\t\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'" selected="selected">'.forum_htmlencode($cur_group['g_title']).'</option>'."\n";
	else
		echo "\t\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'">'.forum_htmlencode($cur_group['g_title']).'</option>'."\n";
}

?>
						</select></span>
					</div>
				</div>
<?php ($hook = get_hook('agr_pre_default_group_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('agr_default_group_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="set_default_group" value="<?php echo $lang_admin_groups['Set default'] ?>" /></span>
			</div>
		</form>
	</div>
<?php

	// Reset counter
	$forum_page['group_count'] = $forum_page['item_count'] = 0;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo $lang_admin_groups['Existing groups heading'] ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<div class="ct-box">
			<p><?php echo $lang_admin_groups['Existing groups intro'] ?></p>
		</div>
		<div class="ct-group">
<?php

$forum_page['item_num'] = 0;
while ($cur_group = $forum_db->fetch_assoc($result))
{
	$forum_page['group_options'] = array(
		'edit' => '<span class="first-item"><a href="'.forum_link($forum_url['admin_groups']).'?edit_group='.$cur_group['g_id'].'">'.$lang_admin_groups['Edit group'].'</a></span>'
	);

	if ($cur_group['g_id'] > FORUM_GUEST)
	{
		if ($cur_group['g_id'] != $forum_config['o_default_user_group'])
			$forum_page['group_options']['remove'] = '<span'.((empty($forum_page['group_options'])) ? ' class="first-item"' : '').'><a href="'.forum_link($forum_url['admin_groups']).'?del_group='.$cur_group['g_id'].'">'.$lang_admin_groups['Remove group'].'</a></span>';
		else
			$forum_page['group_options']['remove'] = '<span'.((empty($forum_page['group_options'])) ? ' class="first-item"' : '').'>'.$lang_admin_groups['Cannot remove default'].'</span>';
	}
	else
		$forum_page['group_options']['remove'] = '<span'.((empty($forum_page['group_options'])) ? ' class="first-item"' : '').'>'.$lang_admin_groups['Cannot remove group'].'</span>';

	($hook = get_hook('agr_edit_group_row_pre_output')) ? eval($hook) : null;

?>
			<div class="ct-set set<?php echo ++$forum_page['item_count'] ?>">
				<div class="ct-box">
					<h3 class="ct-legend hn"><span><?php echo forum_htmlencode($cur_group['g_title']) ?> <?php if ($cur_group['g_id'] == $forum_config['o_default_user_group']) echo $lang_admin_groups['default']; ?></span></h3>
					<p class="options"><?php echo implode(' ', $forum_page['group_options']) ?></p>
				</div>
			</div>
<?php

	($hook = get_hook('agr_edit_group_row_post_output')) ? eval($hook) : null;
}

?>
		</div>
	</div>
<?php

($hook = get_hook('agr_end')) ? eval($hook) : null;
