<?php
namespace punbb;

($hook = get_hook('pf_change_details_settings_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php printf(($forum_page['own_profile']) ?
			__('Settings welcome', 'profile') :
			__('Settings welcome user', 'profile'), forum_htmlencode($user['username'])) ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?= $form_action ?>">
			<div class="hidden">
				<?php echo implode("\n\t\t\t\t", $forum_page['hidden_fields'])."\n" ?>
			</div>
<?php ($hook = get_hook('pf_change_details_settings_pre_local_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?= __('Local settings', 'profile') ?></strong></legend>
<?php

		($hook = get_hook('pf_change_details_settings_pre_language')) ? eval($hook) : null;

		// Only display the language selection box if there's more than one language available
		if (count($forum_page['languages']) > 1)
		{
			natcasesort($forum_page['languages']);

?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box select">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Language', 'profile') ?></span></label><br />
						<span class="fld-input"><select id="fld<?php echo $forum_page['fld_count'] ?>" name="form[language]">
<?php

			foreach ($forum_page['languages'] as $temp)
			{
				if (user()->language == $temp)
					echo "\t\t\t\t\t\t".'<option value="'.$temp.'" selected="selected">'.$temp.'</option>'."\n";
				else
					echo "\t\t\t\t\t\t".'<option value="'.$temp.'">'.$temp.'</option>'."\n";
			}

?>
						</select></span>
					</div>
				</div>
<?php

		}

		($hook = get_hook('pf_change_details_settings_pre_timezone')) ? eval($hook) : null;

?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box select">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Timezone', 'profile') ?></span>
						<small><?= __('Timezone info', 'profile') ?></small></label><br />
						<span class="fld-input"><select id="fld<?php echo $forum_page['fld_count'] ?>" name="form[timezone]">
						<option value="-12"<?php if ($user['timezone'] == -12) echo ' selected="selected"' ?>><?php echo __('UTC-12:00', 'profile') ?></option>
						<option value="-11"<?php if ($user['timezone'] == -11) echo ' selected="selected"' ?>><?= __('UTC-11:00', 'profile') ?></option>
						<option value="-10"<?php if ($user['timezone'] == -10) echo ' selected="selected"' ?>><?= __('UTC-10:00', 'profile') ?></option>
						<option value="-9.5"<?php if ($user['timezone'] == -9.5) echo ' selected="selected"' ?>><?= __('UTC-09:30', 'profile') ?></option>
						<option value="-9"<?php if ($user['timezone'] == -9) echo ' selected="selected"' ?>><?= __('UTC-09:00', 'profile') ?></option>
						<option value="-8"<?php if ($user['timezone'] == -8) echo ' selected="selected"' ?>><?= __('UTC-08:00', 'profile') ?></option>
						<option value="-7"<?php if ($user['timezone'] == -7) echo ' selected="selected"' ?>><?= __('UTC-07:00', 'profile') ?></option>
						<option value="-6"<?php if ($user['timezone'] == -6) echo ' selected="selected"' ?>><?= __('UTC-06:00', 'profile') ?></option>
						<option value="-5"<?php if ($user['timezone'] == -5) echo ' selected="selected"' ?>><?= __('UTC-05:00', 'profile') ?></option>
						<option value="-4"<?php if ($user['timezone'] == -4) echo ' selected="selected"' ?>><?= __('UTC-04:00', 'profile') ?></option>
						<option value="-3.5"<?php if ($user['timezone'] == -3.5) echo ' selected="selected"' ?>><?= __('UTC-03:30', 'profile') ?></option>
						<option value="-3"<?php if ($user['timezone'] == -3) echo ' selected="selected"' ?>><?= __('UTC-03:00', 'profile') ?></option>
						<option value="-2"<?php if ($user['timezone'] == -2) echo ' selected="selected"' ?>><?= __('UTC-02:00', 'profile') ?></option>
						<option value="-1"<?php if ($user['timezone'] == -1) echo ' selected="selected"' ?>><?= __('UTC-01:00', 'profile') ?></option>
						<option value="0"<?php if ($user['timezone'] == 0) echo ' selected="selected"' ?>><?= __('UTC', 'profile') ?></option>
						<option value="1"<?php if ($user['timezone'] == 1) echo ' selected="selected"' ?>><?= __('UTC+01:00', 'profile') ?></option>
						<option value="2"<?php if ($user['timezone'] == 2) echo ' selected="selected"' ?>><?= __('UTC+02:00', 'profile') ?></option>
						<option value="3"<?php if ($user['timezone'] == 3) echo ' selected="selected"' ?>><?= __('UTC+03:00', 'profile') ?></option>
						<option value="3.5"<?php if ($user['timezone'] == 3.5) echo ' selected="selected"' ?>><?= __('UTC+03:30', 'profile') ?></option>
						<option value="4"<?php if ($user['timezone'] == 4) echo ' selected="selected"' ?>><?= __('UTC+04:00', 'profile') ?></option>
						<option value="4.5"<?php if ($user['timezone'] == 4.5) echo ' selected="selected"' ?>><?= __('UTC+04:30', 'profile') ?></option>
						<option value="5"<?php if ($user['timezone'] == 5) echo ' selected="selected"' ?>><?= __('UTC+05:00', 'profile') ?></option>
						<option value="5.5"<?php if ($user['timezone'] == 5.5) echo ' selected="selected"' ?>><?= __('UTC+05:30', 'profile') ?></option>
						<option value="5.75"<?php if ($user['timezone'] == 5.75) echo ' selected="selected"' ?>><?= __('UTC+05:45', 'profile') ?></option>
						<option value="6"<?php if ($user['timezone'] == 6) echo ' selected="selected"' ?>><?= __('UTC+06:00', 'profile') ?></option>
						<option value="6.5"<?php if ($user['timezone'] == 6.5) echo ' selected="selected"' ?>><?= __('UTC+06:30', 'profile') ?></option>
						<option value="7"<?php if ($user['timezone'] == 7) echo ' selected="selected"' ?>><?= __('UTC+07:00', 'profile') ?></option>
						<option value="8"<?php if ($user['timezone'] == 8) echo ' selected="selected"' ?>><?= __('UTC+08:00', 'profile') ?></option>
						<option value="8.75"<?php if ($user['timezone'] == 8.75) echo ' selected="selected"' ?>><?= __('UTC+08:45', 'profile') ?></option>
						<option value="9"<?php if ($user['timezone'] == 9) echo ' selected="selected"' ?>><?= __('UTC+09:00', 'profile') ?></option>
						<option value="9.5"<?php if ($user['timezone'] == 9.5) echo ' selected="selected"' ?>><?= __('UTC+09:30', 'profile') ?></option>
						<option value="10"<?php if ($user['timezone'] == 10) echo ' selected="selected"' ?>><?= __('UTC+10:00', 'profile') ?></option>
						<option value="10.5"<?php if ($user['timezone'] == 10.5) echo ' selected="selected"' ?>><?= __('UTC+10:30', 'profile') ?></option>
						<option value="11"<?php if ($user['timezone'] == 11) echo ' selected="selected"' ?>><?= __('UTC+11:00', 'profile') ?></option>
						<option value="11.5"<?php if ($user['timezone'] == 11.5) echo ' selected="selected"' ?>><?= __('UTC+11:30', 'profile') ?></option>
						<option value="12"<?php if ($user['timezone'] == 12) echo ' selected="selected"' ?>><?= __('UTC+12:00', 'profile') ?></option>
						<option value="12.75"<?php if ($user['timezone'] == 12.75) echo ' selected="selected"' ?>><?= __('UTC+12:45', 'profile') ?></option>
						<option value="13"<?php if ($user['timezone'] == 13) echo ' selected="selected"' ?>><?= __('UTC+13:00', 'profile') ?></option>
						<option value="14"<?php if ($user['timezone'] == 14) echo ' selected="selected"' ?>><?= __('UTC+14:00', 'profile') ?></option>
						</select></span>
					</div>
				</div>
<?php ($hook = get_hook('pf_change_details_settings_pre_dst_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[dst]" value="1"<?php if ($user['dst'] == 1) echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><?= __('DST label', 'profile') ?></label>
					</div>
				</div>
<?php ($hook = get_hook('pf_change_details_settings_pre_time_format')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box select">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Time format', 'profile') ?></span></label><br />
						<span class="fld-input"><select id="fld<?php echo $forum_page['fld_count'] ?>" name="form[time_format]">
<?php

		foreach (array_unique($forum_time_formats) as $key => $time_format)
		{
			echo "\t\t\t\t\t\t".'<option value="'.$key.'"';
			if ($user['time_format'] == $key)
				echo ' selected="selected"';
			echo '>'. format_time(time(), 2, null, $time_format);
			if ($key == 0)
				echo ' (' . __('Default', 'profile') . ')';
			echo "</option>\n";
		}

?>
						</select></span>
					</div>
				</div>
<?php ($hook = get_hook('pf_change_details_settings_pre_date_format')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box select">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span class="legend"><?= __('Date format', 'profile') ?></span></label><br />
						<span class="fld-input"><select id="fld<?php echo $forum_page['fld_count'] ?>" name="form[date_format]">
<?php

		foreach (array_unique($forum_date_formats) as $key => $date_format)
		{
			echo "\t\t\t\t\t\t\t".'<option value="'.$key.'"';
			if ($user['date_format'] == $key)
				echo ' selected="selected"';
			echo '>'. format_time(time(), 1, $date_format, null, true);
			if ($key == 0)
				echo ' (' . __('Default', 'profile') . ')';
			echo "</option>\n";
		}

?>
						</select></span>
					</div>
				</div>
<?php ($hook = get_hook('pf_change_details_settings_pre_local_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('pf_change_details_settings_local_fieldset_end')) ? eval($hook) : null; ?>
<?php $forum_page['item_count'] = 0; ?>
<?php ($hook = get_hook('pf_change_details_settings_pre_display_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?= __('Display settings', 'profile') ?></strong></legend>
<?php

		($hook = get_hook('pf_change_details_settings_pre_style')) ? eval($hook) : null;

		// Only display the style selection box if there's more than one style available
		if (count($forum_page['styles']) == 1)
			echo "\t\t\t\t".'<input type="hidden" name="form[style]" value="'.$forum_page['styles'][0].'" />'."\n";
		else if (count($forum_page['styles']) > 1)
		{
			natcasesort($forum_page['styles']);

?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box select">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Styles', 'profile') ?></span></label><br />
						<span class="fld-input"><select id="fld<?php echo $forum_page['fld_count'] ?>" name="form[style]">
<?php

			foreach ($forum_page['styles'] as $temp)
			{
				if ($user['style'] == $temp)
					echo "\t\t\t\t\t\t".'<option value="'.$temp.'" selected="selected">'.str_replace('_', ' ', $temp).'</option>'."\n";
				else
					echo "\t\t\t\t\t\t".'<option value="'.$temp.'">'.str_replace('_', ' ', $temp).'</option>'."\n";
			}

?>
						</select></span>
					</div>
				</div>
<?php

		}

		($hook = get_hook('pf_change_details_settings_pre_image_display_fieldset')) ? eval($hook) : null;

?>
				<fieldset class="mf-set set<?php echo ++$forum_page['item_count'] ?>">
					<legend><span><?= __('Image display', 'profile') ?></span></legend>
					<div class="mf-box">
<?php if (config()->o_smilies == '1' || config()->o_smilies_sig == '1'): ?>
						<div class="mf-item">
							<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[show_smilies]" value="1"<?php if ($user['show_smilies'] == '1') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?= __('Show smilies', 'profile') ?></label>
						</div>
<?php endif; if (config()->o_avatars == '1'): ?>
						<div class="mf-item">
							<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[show_avatars]" value="1"<?php if ($user['show_avatars'] == '1') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?= __('Show avatars', 'profile') ?></label>
						</div>
<?php endif; if (config()->p_message_img_tag == '1'): ?>
						<div class="mf-item">
							<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[show_img]" value="1"<?php if ($user['show_img'] == '1') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?= __('Show images', 'profile') ?></label>
						</div>
<?php endif; if (config()->o_signatures == '1' && config()->p_sig_img_tag == '1'): ?>
						<div class="mf-item">
							<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[show_img_sig]" value="1"<?php if ($user['show_img_sig'] == '1') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?= __('Show images sigs', 'profile') ?></label>
						</div>
<?php endif; ?>
<?php ($hook = get_hook('pf_change_details_settings_new_image_display_option')) ? eval($hook) : null; ?>
					</div>
<?php ($hook = get_hook('pf_change_details_settings_pre_image_display_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php ($hook = get_hook('pf_change_details_settings_pre_show_sigs_checkbox')) ? eval($hook) : null; ?>
<?php if (config()->o_signatures == '1'): ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[show_sig]" value="1"<?php if ($user['show_sig'] == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?= __('Signature display', 'profile') ?></span> <?= __('Show sigs', 'profile') ?></label>
					</div>
				</div>
<?php ($hook = get_hook('pf_change_details_settings_pre_display_fieldset_end')) ? eval($hook) : null; ?>
<?php endif; ?>
			</fieldset>
<?php ($hook = get_hook('pf_change_details_settings_display_fieldset_end')) ? eval($hook) : null; ?>
<?php $forum_page['item_count'] = 0; ?>
<?php ($hook = get_hook('pf_change_details_settings_pre_pagination_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?= __('Pagination settings', 'profile') ?></strong></legend>
<?php ($hook = get_hook('pf_change_details_settings_pre_disp_topics')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box sf-short text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Topics per page', 'profile') ?></span> <small><?= __('Leave blank', 'profile') ?></small></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[disp_topics]" value="<?php echo $user['disp_topics'] ?>" size="6" maxlength="3" /></span>
					</div>
				</div>
<?php ($hook = get_hook('pf_change_details_settings_pre_disp_posts')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box sf-short text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Posts per page', 'profile') ?></span>	<small><?= __('Leave blank', 'profile') ?></small></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[disp_posts]" value="<?php echo $user['disp_posts'] ?>" size="6" maxlength="3" /></span>
					</div>
				</div>
<?php ($hook = get_hook('pf_change_details_settings_pre_pagination_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('pf_change_details_settings_pagination_fieldset_end')) ? eval($hook) : null; ?>
<?php $forum_page['item_count'] = 0; ?>
<?php ($hook = get_hook('pf_change_details_settings_pre_email_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?= __('E-mail and sub settings', 'profile') ?></strong></legend>
<?php ($hook = get_hook('pf_change_details_settings_pre_email_settings_fieldset')) ? eval($hook) : null; ?>
				<fieldset class="mf-set set<?php echo ++$forum_page['item_count'] ?>">
					<legend><span><?= __('E-mail settings', 'profile') ?></span></legend>
					<div class="mf-box">
						<div class="mf-item">
							<span class="fld-input"><input type="radio" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[email_setting]" value="0"<?php if ($user['email_setting'] == '0') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?= __('E-mail setting 1', 'profile') ?></label>
						</div>
						<div class="mf-item">
							<span class="fld-input"><input type="radio" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[email_setting]" value="1"<?php if ($user['email_setting'] == '1') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?= __('E-mail setting 2', 'profile') ?></label>
						</div>
						<div class="mf-item">
							<span class="fld-input"><input type="radio" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[email_setting]" value="2"<?php if ($user['email_setting'] == '2') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?= __('E-mail setting 3', 'profile') ?></label>
						</div>
<?php ($hook = get_hook('pf_change_details_settings_new_email_setting_option')) ? eval($hook) : null; ?>
					</div>
<?php ($hook = get_hook('pf_change_details_settings_pre_email_settings_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php ($hook = get_hook('pf_change_details_settings_email_settings_fieldset_end')) ? eval($hook) : null; ?>
<?php if (config()->o_subscriptions == '1'): ?>
				<fieldset class="mf-set set<?php echo ++$forum_page['item_count'] ?>">
					<legend><span><?= __('Subscription settings', 'profile') ?></span></legend>
					<div class="mf-box">
						<div class="mf-item">
							<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[notify_with_post]" value="1"<?php if ($user['notify_with_post'] == '1') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?= __('Notify full', 'profile') ?></label>
						</div>
						<div class="mf-item">
							<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[auto_notify]" value="1"<?php if ($user['auto_notify'] == '1') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?= __('Subscribe by default', 'profile') ?></label>
						</div>
<?php ($hook = get_hook('pf_change_details_settings_new_subscription_option')) ? eval($hook) : null; ?>
					</div>
<?php ($hook = get_hook('pf_change_details_settings_pre_subscription_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php ($hook = get_hook('pf_change_details_settings_subscription_fieldset_end')) ? eval($hook) : null; ?>
<?php endif; ?>
<?php ($hook = get_hook('pf_change_details_settings_pre_email_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php $forum_page['item_count'] = 0; ?>
<?php ($hook = get_hook('pf_change_details_settings_email_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="update" value="<?= __('Update profile', 'profile') ?>" /></span>
			</div>
		</form>
	</div>
<?php

($hook = get_hook('pf_change_details_settings_end')) ? eval($hook) : null;
