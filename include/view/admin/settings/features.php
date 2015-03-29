<?php

($hook = get_hook('aop_features_output_start')) ? eval($hook) : null;

?>
	<div class="main-content main-frm">
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo forum_link($forum_url['admin_settings_features']) ?>">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token(forum_link($forum_url['admin_settings_features'])) ?>" />
				<input type="hidden" name="form_sent" value="1" />
			</div>
			<div class="content-head">
				<h2 class="hn"><span><?php echo $lang_admin_settings['Features general'] ?></span></h2>
			</div>
<?php ($hook = get_hook('aop_features_pre_general_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo $lang_admin_settings['Features general legend'] ?></strong></legend>
<?php ($hook = get_hook('aop_features_pre_search_all_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[search_all_forums]" value="1"<?php if ($forum_config['o_search_all_forums'] == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Searching'] ?></span> <?php echo $lang_admin_settings['Search all label'] ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_ranks_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[ranks]" value="1"<?php if ($forum_config['o_ranks'] == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['User ranks'] ?></span> <?php echo $lang_admin_settings['User ranks label'] ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_censoring_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[censoring]" value="1"<?php if ($forum_config['o_censoring'] == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Censor words'] ?></span> <?php echo $lang_admin_settings['Censor words label'] ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_quickjump_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[quickjump]" value="1"<?php if ($forum_config['o_quickjump'] == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Quick jump'] ?></span> <?php echo $lang_admin_settings['Quick jump label'] ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_show_version_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[show_version]" value="1"<?php if ($forum_config['o_show_version'] == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Show version'] ?></span> <?php echo $lang_admin_settings['Show version label'] ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_show_moderators_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[show_moderators]" value="1"<?php if ($forum_config['o_show_moderators'] == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Show moderators'] ?></span> <?php echo $lang_admin_settings['Show moderators label'] ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_users_online_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[users_online]" value="1"<?php if ($forum_config['o_users_online'] == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Online list'] ?></span> <?php echo $lang_admin_settings['Users online label'] ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_general_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php

	($hook = get_hook('aop_features_general_fieldset_end')) ? eval($hook) : null;

	// Reset counter
	$forum_page['group_count'] = $forum_page['item_count'] = 0;

?>
			<div class="content-head">
				<h2 class="hn"><span><?php echo $lang_admin_settings['Features posting'] ?></span></h2>
			</div>
<?php ($hook = get_hook('aop_features_pre_posting_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><span><?php echo $lang_admin_settings['Features posting legend'] ?></span></legend>
<?php ($hook = get_hook('aop_features_pre_quickpost_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[quickpost]" value="1"<?php if ($forum_config['o_quickpost'] == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Quick post'] ?></span> <?php echo $lang_admin_settings['Quick post label'] ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_subscriptions_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[subscriptions]" value="1"<?php if ($forum_config['o_subscriptions'] == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Subscriptions'] ?></span> <?php echo $lang_admin_settings['Subscriptions label'] ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_force_guest_email_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[force_guest_email]" value="1"<?php if ($forum_config['p_force_guest_email'] == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Guest posting'] ?></span> <?php echo $lang_admin_settings['Guest posting label'] ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_show_dot_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[show_dot]" value="1"<?php if ($forum_config['o_show_dot'] == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['User has posted'] ?></span> <?php echo $lang_admin_settings['User has posted label'] ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_topic_views_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[topic_views]" value="1"<?php if ($forum_config['o_topic_views'] == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Topic views'] ?></span> <?php echo $lang_admin_settings['Topic views label'] ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_show_post_count_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[show_post_count]" value="1"<?php if ($forum_config['o_show_post_count'] == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['User post count'] ?></span> <?php echo $lang_admin_settings['User post count label'] ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_show_user_info_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[show_user_info]" value="1"<?php if ($forum_config['o_show_user_info'] == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['User info'] ?></span> <?php echo $lang_admin_settings['User info label'] ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_posting_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php

	($hook = get_hook('aop_features_posting_fieldset_end')) ? eval($hook) : null;

	// Reset counter
	$forum_page['group_count'] = $forum_page['item_count'] = 0;

?>
			<div class="content-head">
				<h2 class="hn"><span><?php echo $lang_admin_settings['Features posts'] ?></span></h2>
			</div>
<?php ($hook = get_hook('aop_features_pre_message_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><span><?php echo $lang_admin_settings['Features posts legend'] ?></span></legend>
<?php ($hook = get_hook('aop_features_pre_message_content_fieldset')) ? eval($hook) : null; ?>
				<fieldset class="mf-set set<?php echo ++$forum_page['item_count'] ?>">
					<legend><span><?php echo $lang_admin_settings['Post content group'] ?></span></legend>
					<div class="mf-box">
						<div class="mf-item">
							<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[message_bbcode]" value="1"<?php if ($forum_config['p_message_bbcode'] == '1') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo $lang_admin_settings['Allow BBCode label'] ?></label>
						</div>
						<div class="mf-item">
							<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[message_img_tag]" value="1"<?php if ($forum_config['p_message_img_tag'] == '1') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo $lang_admin_settings['Allow img label'] ?></label>
						</div>
						<div class="mf-item">
							<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[smilies]" value="1"<?php if ($forum_config['o_smilies'] == '1') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo $lang_admin_settings['Smilies in posts label'] ?></label>
						</div>
						<div class="mf-item">
							<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[make_links]" value="1"<?php if ($forum_config['o_make_links'] == '1') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo $lang_admin_settings['Make clickable links label'] ?></label>
						</div>
<?php ($hook = get_hook('aop_features_new_message_content_option')) ? eval($hook) : null; ?>
					</div>
<?php ($hook = get_hook('aop_features_pre_message_content_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php ($hook = get_hook('aop_features_message_content_fieldset_end')) ? eval($hook) : null; ?>
				<fieldset class="mf-set set<?php echo ++$forum_page['item_count'] ?>">
					<legend><span><?php echo $lang_admin_settings['Allow capitals group'] ?></span></legend>
					<div class="mf-box">
						<div class="mf-item">
							<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[message_all_caps]" value="1"<?php if ($forum_config['p_message_all_caps'] == '1') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo $lang_admin_settings['All caps message label'] ?></label>
						</div>
						<div class="mf-item">
							<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[subject_all_caps]" value="1"<?php if ($forum_config['p_subject_all_caps'] == '1') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo $lang_admin_settings['All caps subject label'] ?></label>
						</div>
<?php ($hook = get_hook('aop_features_new_message_caps_option')) ? eval($hook) : null; ?>
					</div>
<?php ($hook = get_hook('aop_features_pre_message_caps_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php ($hook = get_hook('aop_features_message_caps_fieldset_end')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Indent size label'] ?></span><small><?php echo $lang_admin_settings['Indent size help'] ?></small></label><br />
						<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[indent_num_spaces]" size="5" maxlength="3" value="<?php echo $forum_config['o_indent_num_spaces'] ?>" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_quote_depth')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Quote depth label'] ?></span><small><?php echo $lang_admin_settings['Quote depth help'] ?></small></label><br />
						<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[quote_depth]" size="5" maxlength="3" value="<?php echo $forum_config['o_quote_depth'] ?>" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_message_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php

	($hook = get_hook('aop_features_message_fieldset_end')) ? eval($hook) : null;

	// Reset counter
	$forum_page['group_count'] = $forum_page['item_count'] = 0;

?>
			<div class="content-head">
				<h2 class="hn"><span><?php echo $lang_admin_settings['Features sigs'] ?></span></h2>
			</div>
<?php ($hook = get_hook('aop_features_pre_sig_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><span><?php echo $lang_admin_settings['Features sigs legend'] ?></span></legend>
<?php ($hook = get_hook('aop_features_pre_signature_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[signatures]" value="1"<?php if ($forum_config['o_signatures'] == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Allow signatures'] ?></span> <?php echo $lang_admin_settings['Allow signatures label'] ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_sig_content_fieldset')) ? eval($hook) : null; ?>
				<fieldset class="mf-set set<?php echo ++$forum_page['item_count'] ?>">
					<legend><span><?php echo $lang_admin_settings['Signature content group'] ?></span></legend>
					<div class="mf-box">
						<div class="mf-item">
							<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[sig_bbcode]" value="1"<?php if ($forum_config['p_sig_bbcode'] == '1') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo $lang_admin_settings['BBCode in sigs label'] ?></label>
						</div>
						<div class="mf-item">
							<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[sig_img_tag]" value="1"<?php if ($forum_config['p_sig_img_tag'] == '1') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo $lang_admin_settings['Img in sigs label'] ?></label>
						</div>
						<div class="mf-item">
							<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[smilies_sig]" value="1"<?php if ($forum_config['o_smilies_sig'] == '1') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo $lang_admin_settings['Smilies in sigs label'] ?></label>
						</div>
<?php ($hook = get_hook('aop_features_new_sig_content_option')) ? eval($hook) : null; ?>
					</div>
<?php ($hook = get_hook('aop_features_pre_sig_content_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php ($hook = get_hook('aop_features_sig_content_fieldset_end')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[sig_all_caps]" value="1"<?php if ($forum_config['p_sig_all_caps'] == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Allow capitals group'] ?></span> <?php echo $lang_admin_settings['All caps sigs label'] ?></label>
					</div>
				</div>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Max sig length label'] ?></span></label><br />
						<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[sig_length]" size="5" maxlength="5" value="<?php echo $forum_config['p_sig_length'] ?>" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_max_sig_lines')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Max sig lines label'] ?></span></label><br />
						<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[sig_lines]" size="5" maxlength="3" value="<?php echo $forum_config['p_sig_lines'] ?>" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_sig_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php

	($hook = get_hook('aop_features_sig_fieldset_end')) ? eval($hook) : null;

	// Reset counter
	$forum_page['group_count'] = $forum_page['item_count'] = 0;

?>
			<div class="content-head">
				<h2 class="hn"><span><?php echo $lang_admin_settings['Features Avatars'] ?></span></h2>
			</div>
<?php ($hook = get_hook('aop_features_pre_avatars_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><span><?php echo $lang_admin_settings['Features Avatars legend'] ?></span></legend>
<?php ($hook = get_hook('aop_features_pre_avatar_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[avatars]" value="1"<?php if ($forum_config['o_avatars'] == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Allow avatars'] ?></span> <?php echo $lang_admin_settings['Allow avatars label'] ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_avatar_directory')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Avatar directory label'] ?></span><small><?php echo $lang_admin_settings['Avatar directory help'] ?></small></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[avatars_dir]" size="35" maxlength="50" value="<?php echo forum_htmlencode($forum_config['o_avatars_dir']) ?>" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_avatar_max_width')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Avatar Max width label'] ?></span><small><?php echo $lang_admin_settings['Avatar Max width help'] ?></small></label><br />
						<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[avatars_width]" size="6" maxlength="5" value="<?php echo $forum_config['o_avatars_width'] ?>" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_avatar_max_height')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Avatar Max height label'] ?></span><small><?php echo $lang_admin_settings['Avatar Max height help'] ?></small></label><br />
						<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[avatars_height]" size="6" maxlength="5" value="<?php echo $forum_config['o_avatars_height'] ?>" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_avatar_max_size')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Avatar Max size label'] ?></span><small><?php echo $lang_admin_settings['Avatar Max size help'] ?></small></label><br />
						<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[avatars_size]" size="6" maxlength="6" value="<?php echo $forum_config['o_avatars_size'] ?>" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_avatars_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php

	($hook = get_hook('aop_features_avatars_fieldset_end')) ? eval($hook) : null;

	// Reset counter
	$forum_page['group_count'] = $forum_page['item_count'] = 0;

?>
			<div class="content-head">
				<h2 class="hn"><span><?php echo $lang_admin_settings['Features update'] ?></span></h2>
			</div>
<?php if (function_exists('curl_init') || function_exists('fsockopen') || in_array(strtolower(@ini_get('allow_url_fopen')), array('on', 'true', '1'))): ?>
			<div class="ct-box">
				<p><?php echo $lang_admin_settings['Features update info'] ?></p>
			</div>
<?php ($hook = get_hook('aop_features_pre_updates_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo $lang_admin_settings['Features update legend'] ?></strong></legend>
<?php ($hook = get_hook('aop_features_pre_updates_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[check_for_updates]" value="1"<?php if ($forum_config['o_check_for_updates'] == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Update check'] ?></span> <?php echo $lang_admin_settings['Update check label'] ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_version_updates_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[check_for_versions]" value="1"<?php if ($forum_config['o_check_for_versions'] == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Check for versions'] ?></span> <?php echo $lang_admin_settings['Auto check for versions'] ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_updates_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('aop_features_updates_fieldset_end')) ? eval($hook) : null; ?>
<?php else: ?>
			<div class="ct-box">
				<p><?php echo $lang_admin_settings['Features update disabled info'] ?></p>
			</div>
<?php ($hook = get_hook('aop_features_post_updates_disabled_box')) ? eval($hook) : null; ?>
<?php endif; ?>
<?php
	// Reset counter
	$forum_page['group_count'] = $forum_page['item_count'] = 0;

?>
			<div class="content-head">
				<h2 class="hn"><span><?php echo $lang_admin_settings['Features mask passwords'] ?></span></h2>
			</div>
			<div class="ct-box">
				<p><?php echo $lang_admin_settings['Features mask passwords info'] ?></p>
			</div>
<?php ($hook = get_hook('aop_features_pre_mask_passwords_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo $lang_admin_settings['Features mask passwords legend'] ?></strong></legend>
<?php ($hook = get_hook('aop_features_pre_mask_passwords_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[mask_passwords]" value="1"<?php if ($forum_config['o_mask_passwords'] == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Enable mask passwords'] ?></span> <?php echo $lang_admin_settings['Enable mask passwords label'] ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_mask_passwords_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('aop_features_mask_passwords_fieldset_end')) ? eval($hook) : null; ?>
<?php
	// Reset counter
	$forum_page['group_count'] = $forum_page['item_count'] = 0;

?>
			<div class="content-head">
				<h2 class="hn"><span><?php echo $lang_admin_settings['Features gzip'] ?></span></h2>
			</div>
			<div class="ct-box">
				<p><?php echo $lang_admin_settings['Features gzip info'] ?></p>
			</div>
<?php ($hook = get_hook('aop_features_pre_gzip_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo $lang_admin_settings['Features gzip legend'] ?></strong></legend>
<?php ($hook = get_hook('aop_features_pre_gzip_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[gzip]" value="1"<?php if ($forum_config['o_gzip'] == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Enable gzip'] ?></span> <?php echo $lang_admin_settings['Enable gzip label'] ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_gzip_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('aop_features_gzip_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="save" value="<?php echo $lang_admin_common['Save changes'] ?>" /></span>
			</div>
		</form>
	</div>
