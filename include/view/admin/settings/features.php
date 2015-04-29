<?php
namespace punbb;

($hook = get_hook('aop_features_output_start')) ? eval($hook) : null;

?>
	<div class="main-content main-frm">
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo link('admin_settings_features') ?>">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token(link('admin_settings_features')) ?>" />
				<input type="hidden" name="form_sent" value="1" />
			</div>
			<div class="content-head">
				<h2 class="hn"><span><?php echo __('Features general', 'admin_settings') ?></span></h2>
			</div>
<?php ($hook = get_hook('aop_features_pre_general_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo __('Features general legend', 'admin_settings') ?></strong></legend>
<?php ($hook = get_hook('aop_features_pre_search_all_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[search_all_forums]" value="1"
							<?php if (config()->o_search_all_forums == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo __('Searching', 'admin_settings') ?></span> <?php echo __('Search all label', 'admin_settings') ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_ranks_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[ranks]" value="1"
							<?php if (config()->o_ranks == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo __('User ranks', 'admin_settings') ?></span> <?php echo __('User ranks label', 'admin_settings') ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_censoring_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[censoring]" value="1"
							<?php if (config()->o_censoring == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo __('Censor words', 'admin_settings') ?></span> <?php echo __('Censor words label', 'admin_settings') ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_quickjump_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[quickjump]" value="1"
							<?php if (config()->o_quickjump == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo __('Quick jump', 'admin_settings') ?></span> <?php echo __('Quick jump label', 'admin_settings') ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_show_version_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[show_version]" value="1"
							<?php if (config()->o_show_version == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo __('Show version', 'admin_settings') ?></span> <?php echo __('Show version label', 'admin_settings') ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_show_moderators_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[show_moderators]" value="1"
							<?php if (config()->o_show_moderators == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo __('Show moderators', 'admin_settings') ?></span> <?php echo __('Show moderators label', 'admin_settings') ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_users_online_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[users_online]" value="1"
							<?php if (config()->o_users_online == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo __('Online list', 'admin_settings') ?></span> <?php echo __('Users online label', 'admin_settings') ?></label>
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
				<h2 class="hn"><span><?php echo __('Features posting', 'admin_settings') ?></span></h2>
			</div>
<?php ($hook = get_hook('aop_features_pre_posting_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><span><?php echo __('Features posting legend', 'admin_settings') ?></span></legend>
<?php ($hook = get_hook('aop_features_pre_quickpost_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[quickpost]" value="1"
							<?php if (config()->o_quickpost == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo __('Quick post', 'admin_settings') ?></span> <?php echo __('Quick post label', 'admin_settings') ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_subscriptions_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[subscriptions]" value="1"
							<?php if (config()->o_subscriptions == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo __('Subscriptions', 'admin_settings') ?></span> <?php echo __('Subscriptions label', 'admin_settings') ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_force_guest_email_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[force_guest_email]" value="1"
							<?php if (config()->p_force_guest_email == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo __('Guest posting', 'admin_settings') ?></span> <?php echo __('Guest posting label', 'admin_settings') ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_show_dot_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[show_dot]" value="1"
							<?php if (config()->o_show_dot == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo __('User has posted', 'admin_settings') ?></span> <?php echo __('User has posted label', 'admin_settings') ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_topic_views_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[topic_views]" value="1"
							<?php if (config()->o_topic_views == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo __('Topic views', 'admin_settings') ?></span> <?php echo __('Topic views label', 'admin_settings') ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_show_post_count_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[show_post_count]" value="1"
							<?php if (config()->o_show_post_count == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo __('User post count', 'admin_settings') ?></span> <?php echo __('User post count label', 'admin_settings') ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_show_user_info_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[show_user_info]" value="1"
							<?php if (config()->o_show_user_info == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo __('User info', 'admin_settings') ?></span> <?php echo __('User info label', 'admin_settings') ?></label>
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
				<h2 class="hn"><span><?php echo __('Features posts', 'admin_settings') ?></span></h2>
			</div>
<?php ($hook = get_hook('aop_features_pre_message_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><span><?php echo __('Features posts legend', 'admin_settings') ?></span></legend>
<?php ($hook = get_hook('aop_features_pre_message_content_fieldset')) ? eval($hook) : null; ?>
				<fieldset class="mf-set set<?php echo ++$forum_page['item_count'] ?>">
					<legend><span><?php echo __('Post content group', 'admin_settings') ?></span></legend>
					<div class="mf-box">
						<div class="mf-item">
							<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[message_bbcode]" value="1"
								<?php if (config()->p_message_bbcode == '1') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo __('Allow BBCode label', 'admin_settings') ?></label>
						</div>
						<div class="mf-item">
							<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[message_img_tag]" value="1"
								<?php if (config()->p_message_img_tag == '1') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo __('Allow img label', 'admin_settings') ?></label>
						</div>
						<div class="mf-item">
							<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[smilies]" value="1"
								<?php if (config()->o_smilies == '1') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo __('Smilies in posts label', 'admin_settings') ?></label>
						</div>
						<div class="mf-item">
							<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[make_links]" value="1"
								<?php if (config()->o_make_links == '1') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo __('Make clickable links label', 'admin_settings') ?></label>
						</div>
<?php ($hook = get_hook('aop_features_new_message_content_option')) ? eval($hook) : null; ?>
					</div>
<?php ($hook = get_hook('aop_features_pre_message_content_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php ($hook = get_hook('aop_features_message_content_fieldset_end')) ? eval($hook) : null; ?>
				<fieldset class="mf-set set<?php echo ++$forum_page['item_count'] ?>">
					<legend><span><?php echo __('Allow capitals group', 'admin_settings') ?></span></legend>
					<div class="mf-box">
						<div class="mf-item">
							<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[message_all_caps]" value="1"
								<?php if (config()->p_message_all_caps == '1') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo __('All caps message label', 'admin_settings') ?></label>
						</div>
						<div class="mf-item">
							<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[subject_all_caps]" value="1"
								<?php if (config()->p_subject_all_caps == '1') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo __('All caps subject label', 'admin_settings') ?></label>
						</div>
<?php ($hook = get_hook('aop_features_new_message_caps_option')) ? eval($hook) : null; ?>
					</div>
<?php ($hook = get_hook('aop_features_pre_message_caps_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php ($hook = get_hook('aop_features_message_caps_fieldset_end')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Indent size label', 'admin_settings') ?></span><small><?php echo __('Indent size help', 'admin_settings') ?></small></label><br />
						<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[indent_num_spaces]" size="5" maxlength="3"
							value="<?php echo config()->o_indent_num_spaces ?>" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_quote_depth')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Quote depth label', 'admin_settings') ?></span><small><?php echo __('Quote depth help', 'admin_settings') ?></small></label><br />
						<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[quote_depth]" size="5" maxlength="3"
							value="<?php echo config()->o_quote_depth ?>" /></span>
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
				<h2 class="hn"><span><?php echo __('Features sigs', 'admin_settings') ?></span></h2>
			</div>
<?php ($hook = get_hook('aop_features_pre_sig_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><span><?php echo __('Features sigs legend', 'admin_settings') ?></span></legend>
<?php ($hook = get_hook('aop_features_pre_signature_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[signatures]" value="1"
							<?php if (config()->o_signatures == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo __('Allow signatures', 'admin_settings') ?></span> <?php echo __('Allow signatures label', 'admin_settings') ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_sig_content_fieldset')) ? eval($hook) : null; ?>
				<fieldset class="mf-set set<?php echo ++$forum_page['item_count'] ?>">
					<legend><span><?php echo __('Signature content group', 'admin_settings') ?></span></legend>
					<div class="mf-box">
						<div class="mf-item">
							<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[sig_bbcode]" value="1"
								<?php if (config()->p_sig_bbcode == '1') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo __('BBCode in sigs label', 'admin_settings') ?></label>
						</div>
						<div class="mf-item">
							<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[sig_img_tag]" value="1"
								<?php if (config()->p_sig_img_tag == '1') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo __('Img in sigs label', 'admin_settings') ?></label>
						</div>
						<div class="mf-item">
							<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[smilies_sig]" value="1"
								<?php if (config()->o_smilies_sig == '1') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo __('Smilies in sigs label', 'admin_settings') ?></label>
						</div>
<?php ($hook = get_hook('aop_features_new_sig_content_option')) ? eval($hook) : null; ?>
					</div>
<?php ($hook = get_hook('aop_features_pre_sig_content_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php ($hook = get_hook('aop_features_sig_content_fieldset_end')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[sig_all_caps]" value="1"
							<?php if (config()->p_sig_all_caps == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo __('Allow capitals group', 'admin_settings') ?></span> <?php echo __('All caps sigs label', 'admin_settings') ?></label>
					</div>
				</div>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Max sig length label', 'admin_settings') ?></span></label><br />
						<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[sig_length]" size="5" maxlength="5"
							value="<?php echo config()->p_sig_length ?>" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_max_sig_lines')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Max sig lines label', 'admin_settings') ?></span></label><br />
						<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[sig_lines]" size="5" maxlength="3"
							value="<?php echo config()->p_sig_lines ?>" /></span>
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
				<h2 class="hn"><span><?php echo __('Features Avatars', 'admin_settings') ?></span></h2>
			</div>
<?php ($hook = get_hook('aop_features_pre_avatars_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><span><?php echo __('Features Avatars legend', 'admin_settings') ?></span></legend>
<?php ($hook = get_hook('aop_features_pre_avatar_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[avatars]" value="1"
							<?php if (config()->o_avatars == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo __('Allow avatars', 'admin_settings') ?></span> <?php echo __('Allow avatars label', 'admin_settings') ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_avatar_directory')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Avatar directory label', 'admin_settings') ?></span><small><?php echo __('Avatar directory help', 'admin_settings') ?></small></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[avatars_dir]" size="35" maxlength="50"
							value="<?php echo forum_htmlencode(config()->o_avatars_dir) ?>" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_avatar_max_width')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Avatar Max width label', 'admin_settings') ?></span><small><?php echo __('Avatar Max width help', 'admin_settings') ?></small></label><br />
						<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[avatars_width]" size="6" maxlength="5"
							value="<?php echo config()->o_avatars_width ?>" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_avatar_max_height')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Avatar Max height label', 'admin_settings') ?></span><small><?php echo __('Avatar Max height help', 'admin_settings') ?></small></label><br />
						<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[avatars_height]" size="6" maxlength="5"
							value="<?php echo config()->o_avatars_height ?>" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_avatar_max_size')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Avatar Max size label', 'admin_settings') ?></span><small><?php echo __('Avatar Max size help', 'admin_settings') ?></small></label><br />
						<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[avatars_size]" size="6" maxlength="6"
							value="<?php echo config()->o_avatars_size ?>" /></span>
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
				<h2 class="hn"><span><?php echo __('Features update', 'admin_settings') ?></span></h2>
			</div>
<?php if (function_exists('curl_init') || function_exists('fsockopen') || in_array(strtolower(@ini_get('allow_url_fopen')), array('on', 'true', '1'))): ?>
			<div class="ct-box">
				<p><?php echo __('Features update info', 'admin_settings') ?></p>
			</div>
<?php ($hook = get_hook('aop_features_pre_updates_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo __('Features update legend', 'admin_settings') ?></strong></legend>
<?php ($hook = get_hook('aop_features_pre_updates_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[check_for_updates]" value="1"
							<?php if (config()->o_check_for_updates == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo __('Update check', 'admin_settings') ?></span> <?php echo __('Update check label', 'admin_settings') ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_version_updates_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[check_for_versions]" value="1"
							<?php if (config()->o_check_for_versions == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo __('Check for versions', 'admin_settings') ?></span> <?php echo __('Auto check for versions', 'admin_settings') ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_updates_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('aop_features_updates_fieldset_end')) ? eval($hook) : null; ?>
<?php else: ?>
			<div class="ct-box">
				<p><?php echo __('Features update disabled info', 'admin_settings') ?></p>
			</div>
<?php ($hook = get_hook('aop_features_post_updates_disabled_box')) ? eval($hook) : null; ?>
<?php endif; ?>
<?php
	// Reset counter
	$forum_page['group_count'] = $forum_page['item_count'] = 0;

?>
			<div class="content-head">
				<h2 class="hn"><span><?php echo __('Features mask passwords', 'admin_settings') ?></span></h2>
			</div>
			<div class="ct-box">
				<p><?php echo __('Features mask passwords info', 'admin_settings') ?></p>
			</div>
<?php ($hook = get_hook('aop_features_pre_mask_passwords_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo __('Features mask passwords legend', 'admin_settings') ?></strong></legend>
<?php ($hook = get_hook('aop_features_pre_mask_passwords_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[mask_passwords]" value="1"
							<?php if (config()->o_mask_passwords == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo __('Enable mask passwords', 'admin_settings') ?></span> <?php echo __('Enable mask passwords label', 'admin_settings') ?></label>
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
				<h2 class="hn"><span><?php echo __('Features gzip', 'admin_settings') ?></span></h2>
			</div>
			<div class="ct-box">
				<p><?php echo __('Features gzip info', 'admin_settings') ?></p>
			</div>
<?php ($hook = get_hook('aop_features_pre_gzip_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo __('Features gzip legend', 'admin_settings') ?></strong></legend>
<?php ($hook = get_hook('aop_features_pre_gzip_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[gzip]" value="1"
							<?php if (config()->o_gzip == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo __('Enable gzip', 'admin_settings') ?></span> <?php echo __('Enable gzip label', 'admin_settings') ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_features_pre_gzip_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('aop_features_gzip_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="save" value="<?php echo __('Save changes', 'admin_common') ?>" /></span>
			</div>
		</form>
	</div>
