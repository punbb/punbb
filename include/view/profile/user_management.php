<?php
namespace punbb;

($hook = get_hook('pf_change_details_admin_pre_user_management')) ? eval($hook) : null;

if (!empty($forum_page['user_management'])) {

	echo "\t\t\t".implode("\n\t\t\t", $forum_page['user_management'])."\n";

	($hook = get_hook('pf_change_details_admin_pre_membership')) ? eval($hook) : null;

	if (user()->g_moderator != '1' && !$forum_page['own_profile'])
	{

		($hook = get_hook('pf_change_details_admin_pre_group_membership')) ? eval($hook) : null;

?>
	<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
		<div class="sf-box select">
			<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('User group', 'profile') ?></span></label><br />
			<span class="fld-input"><select id="fld<?php echo $forum_page['fld_count'] ?>" name="group_id">
<?php

		while ($cur_group = db()->fetch_assoc($result_group))
		{
			if ($cur_group['g_id'] == $user['g_id'] || ($cur_group['g_id'] == config()->o_default_user_group && $user['g_id'] == ''))
				echo "\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'" selected="selected">'.forum_htmlencode($cur_group['g_title']).'</option>'."\n";
			else
				echo "\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'">'.forum_htmlencode($cur_group['g_title']).'</option>'."\n";
		}

?>
			</select></span>
		</div>
	</div>
<?php ($hook = get_hook('pf_change_details_admin_pre_group_membership_submit')) ? eval($hook) : null; ?>
	<div class="sf-set button-set set<?php echo ++$forum_page['item_count'] ?>">
		<div class="sf-box text">
			<span class="submit primary"><input type="submit" name="update_group_membership" value="<?= __('Update groups', 'profile') ?>" /></span>
		</div>
	</div>
<?php

	}
}