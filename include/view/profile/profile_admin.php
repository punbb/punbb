<?php
namespace punbb;

($hook = get_hook('pf_change_details_admin_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?= __('User management', 'profile') ?></span></h2>
	</div>
	<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo $forum_page['form_action'] ?>">
		<div class="hidden">
			<?php echo implode("\n\t\t\t\t", $forum_page['hidden_fields'])."\n" ?>
		</div>
		<div class="main-content main-frm">
			<div class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
		<?php

		include view('profile/user_management');

		($hook = get_hook('pf_change_details_admin_pre_mod_assignment')) ? eval($hook) : null;

		if ($forum_user['g_id'] == FORUM_ADMIN && ($user['g_id'] == FORUM_ADMIN || $user['g_moderator'] == '1'))
		{
			($hook = get_hook('pf_change_details_admin_pre_mod_assignment_fieldset')) ? eval($hook) : null;

?>
			<fieldset class="mf-set set<?php echo ++$forum_page['item_count'] ?>">
				<legend><span><?= __('Moderator assignment', 'profile') ?></span></legend>
<?php ($hook = get_hook('pf_change_details_admin_pre_forum_checklist')) ? eval($hook) : null; ?>
				<div class="mf-box">
					<div class="checklist">
<?php
			$cur_category = 0;
			while ($cur_forum = db()->fetch_assoc($result))
			{
				($hook = get_hook('pf_change_details_admin_forum_loop_start')) ? eval($hook) : null;

				if ($cur_forum['cid'] != $cur_category)	// A new category since last iteration?
				{
					if ($cur_category)
						 echo "\n\t\t\t\t\t\t".'</fieldset>'."\n";

					echo "\t\t\t\t\t\t".'<fieldset>'."\n\t\t\t\t\t\t\t".'<legend><span>'.$cur_forum['cat_name'].':</span></legend>'."\n";
					$cur_category = $cur_forum['cid'];
				}

				$moderators = ($cur_forum['moderators'] != '') ? unserialize($cur_forum['moderators']) : array();

				echo "\t\t\t\t\t\t\t".'<div class="checklist-item"><span class="fld-input"><input type="checkbox" id="fld'.(++$forum_page['fld_count']).'" name="moderator_in['.$cur_forum['fid'].']" value="1"'.((in_array($id, $moderators)) ? ' checked="checked"' : '').' /></span> <label for="fld'.$forum_page['fld_count'].'">'.forum_htmlencode($cur_forum['forum_name']).'</label></div>'."\n";

				($hook = get_hook('pf_change_details_admin_forum_loop_end')) ? eval($hook) : null;
			}

			if ($cur_category)
				echo "\t\t\t\t\t\t".'</fieldset>'."\n";
?>
					</div>
				</div>
<?php ($hook = get_hook('pf_change_details_admin_pre_mod_assignment_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('pf_change_details_admin_mod_assignment_fieldset_end')) ? eval($hook) : null; ?>
			<div class="mf-set button-set set<?php echo ++$forum_page['item_count'] ?>">
				<div class="mf-box text">
					<span class="submit primary"><input type="submit" name="update_forums" value="<?= __('Update forums', 'profile') ?>" /></span>
				</div>
			</div>
<?php

			($hook = get_hook('pf_change_details_admin_form_end')) ? eval($hook) : null;
		}

?>
		</div>
		<div class="frm-buttons">
			<span class="submit primary"><?= __('Instructions', 'profile') ?></span>
		</div>
	</div>
	</form>
<?php

($hook = get_hook('pf_change_details_admin_end')) ? eval($hook) : null;
