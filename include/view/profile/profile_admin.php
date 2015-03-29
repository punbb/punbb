<?php

($hook = get_hook('pf_change_details_admin_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo $lang_profile['User management'] ?></span></h2>
	</div>
	<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo $forum_page['form_action'] ?>">
		<div class="hidden">
			<?php echo implode("\n\t\t\t\t", $forum_page['hidden_fields'])."\n" ?>
		</div>
		<div class="main-content main-frm">
			<div class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
<?php

		($hook = get_hook('pf_change_details_admin_pre_user_management')) ? eval($hook) : null;

		if (!empty($forum_page['user_management']))
		{

			echo "\t\t\t".implode("\n\t\t\t", $forum_page['user_management'])."\n";

			($hook = get_hook('pf_change_details_admin_pre_membership')) ? eval($hook) : null;

			if ($forum_user['g_moderator'] != '1' && !$forum_page['own_profile'])
			{

				($hook = get_hook('pf_change_details_admin_pre_group_membership')) ? eval($hook) : null;

?>
			<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
				<div class="sf-box select">
					<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_profile['User group'] ?></span></label><br />
					<span class="fld-input"><select id="fld<?php echo $forum_page['fld_count'] ?>" name="group_id">
<?php

				$query = array(
					'SELECT'	=> 'g.g_id, g.g_title',
					'FROM'		=> 'groups AS g',
					'WHERE'		=> 'g.g_id!='.FORUM_GUEST,
					'ORDER BY'	=> 'g.g_title'
				);

				($hook = get_hook('pf_change_details_admin_qr_get_groups')) ? eval($hook) : null;
				$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
				while ($cur_group = $forum_db->fetch_assoc($result))
				{
					if ($cur_group['g_id'] == $user['g_id'] || ($cur_group['g_id'] == $forum_config['o_default_user_group'] && $user['g_id'] == ''))
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
					<span class="submit primary"><input type="submit" name="update_group_membership" value="<?php echo $lang_profile['Update groups'] ?>" /></span>
				</div>
			</div>
<?php

			}
		}

		($hook = get_hook('pf_change_details_admin_pre_mod_assignment')) ? eval($hook) : null;

		if ($forum_user['g_id'] == FORUM_ADMIN && ($user['g_id'] == FORUM_ADMIN || $user['g_moderator'] == '1'))
		{
			($hook = get_hook('pf_change_details_admin_pre_mod_assignment_fieldset')) ? eval($hook) : null;

?>
			<fieldset class="mf-set set<?php echo ++$forum_page['item_count'] ?>">
				<legend><span><?php echo $lang_profile['Moderator assignment'] ?></span></legend>
<?php ($hook = get_hook('pf_change_details_admin_pre_forum_checklist')) ? eval($hook) : null; ?>
				<div class="mf-box">
					<div class="checklist">
<?php

			$query = array(
				'SELECT'	=> 'c.id AS cid, c.cat_name, f.id AS fid, f.forum_name, f.moderators',
				'FROM'		=> 'categories AS c',
				'JOINS'		=> array(
					array(
						'INNER JOIN'	=> 'forums AS f',
						'ON'			=> 'c.id=f.cat_id'
					)
				),
				'WHERE'		=> 'f.redirect_url IS NULL',
				'ORDER BY'	=> 'c.disp_position, c.id, f.disp_position'
			);

			($hook = get_hook('pf_change_details_admin_qr_get_cats_and_forums')) ? eval($hook) : null;
			$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

			$cur_category = 0;
			while ($cur_forum = $forum_db->fetch_assoc($result))
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
					<span class="submit primary"><input type="submit" name="update_forums" value="<?php echo $lang_profile['Update forums'] ?>" /></span>
				</div>
			</div>
<?php

			($hook = get_hook('pf_change_details_admin_form_end')) ? eval($hook) : null;
		}

?>
		</div>
		<div class="frm-buttons">
			<span class="submit primary"><?php echo $lang_profile['Instructions'] ?></span>
		</div>
	</div>
	</form>
<?php

($hook = get_hook('pf_change_details_admin_end')) ? eval($hook) : null;
