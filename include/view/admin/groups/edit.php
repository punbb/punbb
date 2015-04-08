<?php

($hook = get_hook('agr_add_edit_group_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo __('Group settings heading', 'admin_groups') ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<div id="req-msg" class="req-warn ct-box error-box">
			<p class="important"><?php echo __('Required warn', 'admin_common') ?></p>
		</div>
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo forum_link($forum_url['admin_groups']) ?>">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token(forum_link($forum_url['admin_groups'])) ?>" />
				<input type="hidden" name="mode" value="<?php echo $mode ?>" />
<?php if ($mode == 'edit'): ?>				<input type="hidden" name="group_id" value="<?php echo $group_id ?>" />
<?php endif; if ($mode == 'add'): ?>				<input type="hidden" name="base_group" value="<?php echo $base_group ?>" />
<?php endif; ?>			</div>
			<div class="content-head">
				<h3 class="hn"><span><?php echo __('Group title head', 'admin_groups') ?></span></h3>
			</div>
<?php ($hook = get_hook('agr_add_edit_group_pre_basic_details_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><span><?php echo __('Group title legend', 'admin_groups') ?></span></legend>
<?php ($hook = get_hook('agr_add_edit_group_pre_group_title')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text required">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Group title label', 'admin_groups') ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="req_title" size="25" maxlength="50" value="<?php if ($mode == 'edit') echo forum_htmlencode($group['g_title']); ?>" required /></span>
					</div>
				</div>
<?php ($hook = get_hook('agr_add_edit_group_pre_user_title')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text required">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('User title label', 'admin_groups') ?></span> <small><?php echo __('User title help', 'admin_groups') ?></small></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="user_title" size="25" maxlength="50" value="<?php echo forum_htmlencode($group['g_user_title']) ?>" /></span>
					</div>
				</div>
<?php ($hook = get_hook('agr_add_edit_group_pre_basic_details_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php

	($hook = get_hook('agr_add_edit_group_basic_details_fieldset_end')) ? eval($hook) : null;

	// The rest of the form is for non-admin groups only
	if ($group['g_id'] != FORUM_ADMIN)
	{
		// Reset fieldset counter
		$forum_page['group_count'] = $forum_page['item_count'] = 0;

?>
			<div class="content-head">
				<h3 class="hn"><span><?php echo __('Group perms head', 'admin_groups') ?></span></h3>
			</div>
<?php if ($mode == 'edit' && $forum_config['o_default_user_group'] == $group['g_id']): ?>
				<div class="ct-box">
					<p class="warn"><?php echo __('Moderator default group', 'admin_groups') ?></p>
				</div>
<?php endif; ($hook = get_hook('agr_add_edit_group_pre_permissions_fieldset')) ? eval($hook) : null; ?>
				<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
					<legend class="group-legend"><strong><?php echo __('Permissions', 'admin_groups') ?></strong></legend>
<?php ($hook = get_hook('agr_add_edit_group_pre_mod_permissions_fieldset')) ? eval($hook) : null; if ($group['g_id'] != FORUM_GUEST): if ($mode != 'edit' || $forum_config['o_default_user_group'] != $group['g_id']): ?>
					<fieldset class="mf-set set<?php echo ++$forum_page['item_count'] ?>">
						<legend><span><?php echo __('Mod permissions', 'admin_groups') ?></span></legend>
						<div class="mf-box">
<?php ($hook = get_hook('agr_add_edit_group_pre_allow_moderate_checkbox')) ? eval($hook) : null; ?>
							<div class="mf-item">
								<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="moderator" value="1"<?php if ($group['g_moderator'] == '1') echo ' checked="checked"' ?> /></span>
								<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo __('Allow moderate label', 'admin_groups') ?> <?php echo __('Allow moderate help', 'admin_groups') ?></label>
							</div>
<?php ($hook = get_hook('agr_add_edit_group_pre_allow_mod_edit_profiles_checkbox')) ? eval($hook) : null; ?>
							<div class="mf-item">
								<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="mod_edit_users" value="1"<?php if ($group['g_mod_edit_users'] == '1') echo ' checked="checked"' ?> /></span>
								<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo __('Allow mod edit profiles label', 'admin_groups') ?></label>
							</div>
<?php ($hook = get_hook('agr_add_edit_group_pre_allow_mod_edit_userbane_checkbox')) ? eval($hook) : null; ?>
							<div class="mf-item">
								<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="mod_rename_users" value="1"<?php if ($group['g_mod_rename_users'] == '1') echo ' checked="checked"' ?> /></span>
								<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo __('Allow mod edit username label', 'admin_groups') ?></label>
							</div>
<?php ($hook = get_hook('agr_add_edit_group_pre_allow_mod_change_pass_checkbox')) ? eval($hook) : null; ?>
							<div class="mf-item">
								<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="mod_change_passwords" value="1"<?php if ($group['g_mod_change_passwords'] == '1') echo ' checked="checked"' ?> /></span>
								<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo __('Allow mod change pass label', 'admin_groups') ?></label>
							</div>
<?php ($hook = get_hook('agr_add_edit_group_pre_allow_mod_ban_users_checkbox')) ? eval($hook) : null; ?>
							<div class="mf-item">
								<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="mod_ban_users" value="1"<?php if ($group['g_mod_ban_users'] == '1') echo ' checked="checked"' ?> /></span>
								<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo __('Allow mod bans label', 'admin_groups') ?></label>
							</div>
<?php ($hook = get_hook('agr_add_edit_group_pre_mod_permissions_fieldset_end')) ? eval($hook) : null; ?>
						</div>
					</fieldset>
<?php ($hook = get_hook('agr_add_edit_group_mod_permissions_fieldset_end')) ? eval($hook) : null; endif; endif; ?>
					<fieldset class="mf-set set<?php echo ++$forum_page['item_count'] ?>">
						<legend><span><?php echo __('User permissions', 'admin_groups') ?></span></legend>
						<div class="mf-box">
<?php ($hook = get_hook('agr_add_edit_group_pre_allow_read_board_checkbox')) ? eval($hook) : null; ?>
							<div class="mf-item">
								<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="read_board" value="1"<?php if ($group['g_read_board'] == '1') echo ' checked="checked"' ?> /></span>
								<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo __('Allow read board label', 'admin_groups') ?> <?php echo __('Allow read board help', 'admin_groups') ?></label>
							</div>
<?php ($hook = get_hook('agr_add_edit_group_pre_allow_view_users_checkbox')) ? eval($hook) : null; ?>
							<div class="mf-item">
								<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="view_users" value="1"<?php if ($group['g_view_users'] == '1') echo ' checked="checked"' ?> /></span>
								<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo __('Allow view users label', 'admin_groups') ?></label>
							</div>
<?php ($hook = get_hook('agr_add_edit_group_pre_allow_post_replies_checkbox')) ? eval($hook) : null; ?>
							<div class="mf-item">
								<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="post_replies" value="1"<?php if ($group['g_post_replies'] == '1') echo ' checked="checked"' ?> /></span>
								<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo __('Allow post replies label', 'admin_groups') ?></label>
							</div>
<?php ($hook = get_hook('agr_add_edit_group_pre_allow_post_topics_checkbox')) ? eval($hook) : null; ?>
							<div class="mf-item">
								<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="post_topics" value="1"<?php if ($group['g_post_topics'] == '1') echo ' checked="checked"' ?> /></span>
								<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo __('Allow post topics label', 'admin_groups') ?></label>
							</div>
<?php ($hook = get_hook('agr_add_edit_group_pre_allow_edit_posts_checkbox')) ? eval($hook) : null; if ($group['g_id'] != FORUM_GUEST): ?>
							<div class="mf-item">
								<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="edit_posts" value="1"<?php if ($group['g_edit_posts'] == '1') echo ' checked="checked"' ?> /></span>
								<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo __('Allow edit posts label', 'admin_groups') ?></label>
							</div>
<?php ($hook = get_hook('agr_add_edit_group_pre_allow_delete_posts_checkbox')) ? eval($hook) : null; ?>
							<div class="mf-item">
								<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="delete_posts" value="1"<?php if ($group['g_delete_posts'] == '1') echo ' checked="checked"' ?> /></span>
								<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo __('Allow delete posts label', 'admin_groups') ?></label>
							</div>
<?php ($hook = get_hook('agr_add_edit_group_pre_allow_delete_topics_checkbox')) ? eval($hook) : null; ?>
							<div class="mf-item">
								<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="delete_topics" value="1"<?php if ($group['g_delete_topics'] == '1') echo ' checked="checked"' ?> /></span>
								<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo __('Allow delete topics label', 'admin_groups') ?></label>
							</div>
<?php ($hook = get_hook('agr_add_edit_group_pre_allow_set_user_title_checkbox')) ? eval($hook) : null; ?>
							<div class="mf-item">
								<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="set_title" value="1"<?php if ($group['g_set_title'] == '1') echo ' checked="checked"' ?> /></span>
								<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo __('Allow set user title label', 'admin_groups') ?></label>
							</div>
<?php endif; ($hook = get_hook('agr_add_edit_group_pre_allow_search_checkbox')) ? eval($hook) : null; ?>
							<div class="mf-item">
								<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="search" value="1"<?php if ($group['g_search'] == '1') echo ' checked="checked"' ?> /></span>
								<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo __('Allow use search label', 'admin_groups') ?></label>
							</div>
<?php ($hook = get_hook('agr_add_edit_group_pre_allow_search_users_checkbox')) ? eval($hook) : null; ?>
							<div class="mf-item">
								<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="search_users" value="1"<?php if ($group['g_search_users'] == '1') echo ' checked="checked"' ?> /></span>
								<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo __('Allow search users label', 'admin_groups') ?></label>
							</div>
<?php ($hook = get_hook('agr_add_edit_group_pre_allow_send_email_checkbox')) ? eval($hook) : null; if ($group['g_id'] != FORUM_GUEST): ?>
							<div class="mf-item">
								<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="send_email" value="1"<?php if ($group['g_send_email'] == '1') echo ' checked="checked"' ?> /></span>
								<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo __('Allow send email label', 'admin_groups') ?></label>
							</div>
<?php endif; ($hook = get_hook('agr_add_edit_group_pre_user_permissions_fieldset_end')) ? eval($hook) : null; ?>
						</div>
					</fieldset>
<?php ($hook = get_hook('agr_add_edit_group_user_permissions_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php

		// Reset counter
		$forum_page['group_count'] = $forum_page['item_count'] = 0;

?>
			<div class="content-head">
				<h3 class="hn"><span><?php echo __('Group flood head', 'admin_groups') ?></span></h3>
			</div>
<?php ($hook = get_hook('agr_add_edit_group_pre_flood_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><span><?php echo __('Restrictions', 'admin_groups') ?></span></legend>
<?php ($hook = get_hook('agr_add_edit_group_pre_post_interval')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Flood interval label', 'admin_groups') ?></span> <small><?php echo __('Flood interval help', 'admin_groups') ?></small></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="post_flood" size="5" maxlength="4" value="<?php echo $group['g_post_flood'] ?>" /></span>
					</div>
				</div>
<?php ($hook = get_hook('agr_add_edit_group_pre_search_interval')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Search interval label', 'admin_groups') ?></span> <small><?php echo __('Search interval help', 'admin_groups') ?></small></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="search_flood" size="5" maxlength="4" value="<?php echo $group['g_search_flood'] ?>" /></span>
					</div>
				</div>
<?php

		($hook = get_hook('agr_add_edit_group_pre_email_interval')) ? eval($hook) : null;

		// The rest of the form is for non-guest groups only
		if ($group['g_id'] != FORUM_GUEST)
		{

?>
				<?php ($hook = get_hook('agr_add_edit_group_pre_email_interval')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Email flood interval label', 'admin_groups') ?></span> <small><?php echo __('Email flood interval help', 'admin_groups') ?></small></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="email_flood" size="5" maxlength="4" value="<?php echo $group['g_email_flood'] ?>" /></span>
					</div>
				</div>
<?php

		}

		($hook = get_hook('agr_add_edit_group_pre_flood_fieldset_end')) ? eval($hook) : null;

?>
			</fieldset>
<?php

		($hook = get_hook('agr_add_edit_group_flood_fieldset_end')) ? eval($hook) : null;
	}

?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="add_edit_group" value=" <?php echo __('Update group', 'admin_groups') ?> " /></span>
			</div>
		</form>
	</div>
<?php

($hook = get_hook('agr_add_edit_group_end')) ? eval($hook) : null;
