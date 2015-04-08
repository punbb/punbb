<?php

($hook = get_hook('aop_setup_output_start')) ? eval($hook) : null;

?>
	<div class="main-content main-frm">
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo forum_link($forum_url['admin_settings_setup']) ?>">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token(forum_link($forum_url['admin_settings_setup'])) ?>" />
				<input type="hidden" name="form_sent" value="1" />
			</div>
				<div class="content-head">
					<h2 class="hn"><span><?php echo $lang_admin_settings['Setup personal'] ?></span></h2>
				</div>
<?php ($hook = get_hook('aop_setup_pre_personal_fieldset')) ? eval($hook) : null; ?>
				<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
					<legend class="group-legend"><strong><?php echo $lang_admin_settings['Setup personal legend'] ?></strong></legend>
<?php ($hook = get_hook('aop_setup_pre_board_title')) ? eval($hook) : null; ?>
					<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
						<div class="sf-box text">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>">
								<span><?php echo $lang_admin_settings['Board title label'] ?></span>
							</label><br />
							<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[board_title]" size="50" maxlength="255" value="<?php echo forum_htmlencode($forum_config['o_board_title']) ?>" /></span>
						</div>
					</div>
<?php ($hook = get_hook('aop_setup_pre_board_descrip')) ? eval($hook) : null; ?>
					<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
						<div class="sf-box text">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>">
								<span><?php echo $lang_admin_settings['Board description label'] ?></span>
							</label><br />
							<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[board_desc]" size="50" maxlength="255" value="<?php echo forum_htmlencode($forum_config['o_board_desc']) ?>" /></span>
						</div>
					</div>
<?php ($hook = get_hook('aop_setup_pre_default_style')) ? eval($hook) : null; ?>
					<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
						<div class="sf-box select">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>">
								<span><?php echo $lang_admin_settings['Default style label'] ?></span>
							</label><br />
							<span class="fld-input"><select id="fld<?php echo $forum_page['fld_count'] ?>" name="form[default_style]">
<?php

	$styles = get_style_packs();
	foreach ($styles as $style)
	{
		if ($forum_config['o_default_style'] == $style)
			echo "\t\t\t\t\t\t\t\t".'<option value="'.$style.'" selected="selected">'.str_replace('_', ' ', $style).'</option>'."\n";
		else
			echo "\t\t\t\t\t\t\t\t".'<option value="'.$style.'">'.str_replace('_', ' ', $style).'</option>'."\n";
	}

?>
							</select></span>
						</div>
					</div>
<?php ($hook = get_hook('aop_setup_pre_personal_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php

	($hook = get_hook('aop_setup_personal_fieldset_end')) ? eval($hook) : null;

	// Reset counter
	$forum_page['group_count'] = $forum_page['item_count'] = 0;

?>
				<div class="content-head">
					<h2 class="hn"><span><?php echo $lang_admin_settings['Setup local'] ?></span></h2>
				</div>
<?php ($hook = get_hook('aop_setup_pre_local_fieldset')) ? eval($hook) : null; ?>
				<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
					<legend class="group-legend"><strong><?php echo $lang_admin_settings['Setup local legend'] ?></strong></legend>
<?php ($hook = get_hook('aop_setup_pre_default_language')) ? eval($hook) : null; ?>
					<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
						<div class="sf-box select">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Default language label'] ?></span><small><?php echo $lang_admin_settings['Default language help'] ?></small></label><br />
							<span class="fld-input"><select id="fld<?php echo $forum_page['fld_count'] ?>" name="form[default_lang]">
<?php

	$languages = get_language_packs();
	foreach ($languages as $lang)
	{
		if ($forum_config['o_default_lang'] == $lang)
			echo "\t\t\t\t\t\t\t\t".'<option value="'.$lang.'" selected="selected">'.$lang.'</option>'."\n";
		else
			echo "\t\t\t\t\t\t\t\t".'<option value="'.$lang.'">'.$lang.'</option>'."\n";
	}

?>
							</select></span>
						</div>
					</div>
<?php ($hook = get_hook('aop_setup_pre_default_timezone')) ? eval($hook) : null; ?>
					<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
						<div class="sf-box select">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Default timezone label'] ?></span></label><br />
							<span class="fld-input"><select id="fld<?php echo $forum_page['fld_count'] ?>" name="form[default_timezone]">
								<option value="-12"<?php if ($forum_config['o_default_timezone'] == -12) echo ' selected="selected"' ?>><?= __('UTC-12:00', 'profile') ?></option>
								<option value="-11"<?php if ($forum_config['o_default_timezone'] == -11) echo ' selected="selected"' ?>><?= __('UTC-11:00', 'profile') ?></option>
								<option value="-10"<?php if ($forum_config['o_default_timezone'] == -10) echo ' selected="selected"' ?>><?= __('UTC-10:00', 'profile') ?></option>
								<option value="-9.5"<?php if ($forum_config['o_default_timezone'] == -9.5) echo ' selected="selected"' ?>><?= __('UTC-09:30', 'profile') ?></option>
								<option value="-9"<?php if ($forum_config['o_default_timezone'] == -9) echo ' selected="selected"' ?>><?= __('UTC-09:00', 'profile') ?></option>
								<option value="-8"<?php if ($forum_config['o_default_timezone'] == -8) echo ' selected="selected"' ?>><?= __('UTC-08:00', 'profile') ?></option>
								<option value="-7"<?php if ($forum_config['o_default_timezone'] == -7) echo ' selected="selected"' ?>><?= __('UTC-07:00', 'profile') ?></option>
								<option value="-6"<?php if ($forum_config['o_default_timezone'] == -6) echo ' selected="selected"' ?>><?= __('UTC-06:00', 'profile') ?></option>
								<option value="-5"<?php if ($forum_config['o_default_timezone'] == -5) echo ' selected="selected"' ?>><?= __('UTC-05:00', 'profile') ?></option>
								<option value="-4"<?php if ($forum_config['o_default_timezone'] == -4) echo ' selected="selected"' ?>><?= __('UTC-04:00', 'profile') ?></option>
								<option value="-3.5"<?php if ($forum_config['o_default_timezone'] == -3.5) echo ' selected="selected"' ?>><?= __('UTC-03:30', 'profile') ?></option>
								<option value="-3"<?php if ($forum_config['o_default_timezone'] == -3) echo ' selected="selected"' ?>><?= __('UTC-03:00', 'profile') ?></option>
								<option value="-2"<?php if ($forum_config['o_default_timezone'] == -2) echo ' selected="selected"' ?>><?= __('UTC-02:00', 'profile') ?></option>
								<option value="-1"<?php if ($forum_config['o_default_timezone'] == -1) echo ' selected="selected"' ?>><?= __('UTC-01:00', 'profile') ?></option>
								<option value="0"<?php if ($forum_config['o_default_timezone'] == 0) echo ' selected="selected"' ?>><?= __('UTC', 'profile') ?></option>
								<option value="1"<?php if ($forum_config['o_default_timezone'] == 1) echo ' selected="selected"' ?>><?= __('UTC+01:00', 'profile') ?></option>
								<option value="2"<?php if ($forum_config['o_default_timezone'] == 2) echo ' selected="selected"' ?>><?= __('UTC+02:00', 'profile') ?></option>
								<option value="3"<?php if ($forum_config['o_default_timezone'] == 3) echo ' selected="selected"' ?>><?= __('UTC+03:00', 'profile') ?></option>
								<option value="3.5"<?php if ($forum_config['o_default_timezone'] == 3.5) echo ' selected="selected"' ?>><?= __('UTC+03:30', 'profile') ?></option>
								<option value="4"<?php if ($forum_config['o_default_timezone'] == 4) echo ' selected="selected"' ?>><?= __('UTC+04:00', 'profile') ?></option>
								<option value="4.5"<?php if ($forum_config['o_default_timezone'] == 4.5) echo ' selected="selected"' ?>><?= __('UTC+04:30', 'profile') ?></option>
								<option value="5"<?php if ($forum_config['o_default_timezone'] == 5) echo ' selected="selected"' ?>><?= __('UTC+05:00', 'profile') ?></option>
								<option value="5.5"<?php if ($forum_config['o_default_timezone'] == 5.5) echo ' selected="selected"' ?>><?=__('UTC+05:30', 'profile') ?></option>
								<option value="5.75"<?php if ($forum_config['o_default_timezone'] == 5.75) echo ' selected="selected"' ?>><?= __('UTC+05:45', 'profile') ?></option>
								<option value="6"<?php if ($forum_config['o_default_timezone'] == 6) echo ' selected="selected"' ?>><?= __('UTC+06:00', 'profile') ?></option>
								<option value="6.5"<?php if ($forum_config['o_default_timezone'] == 6.5) echo ' selected="selected"' ?>><?= __('UTC+06:30', 'profile') ?></option>
								<option value="7"<?php if ($forum_config['o_default_timezone'] == 7) echo ' selected="selected"' ?>><?= __('UTC+07:00', 'profile') ?></option>
								<option value="8"<?php if ($forum_config['o_default_timezone'] == 8) echo ' selected="selected"' ?>><?= __('UTC+08:00', 'profile') ?></option>
								<option value="8.75"<?php if ($forum_config['o_default_timezone'] == 8.75) echo ' selected="selected"' ?>><?= __('UTC+08:45', 'profile') ?></option>
								<option value="9"<?php if ($forum_config['o_default_timezone'] == 9) echo ' selected="selected"' ?>><?= __('UTC+09:00', 'profile') ?></option>
								<option value="9.5"<?php if ($forum_config['o_default_timezone'] == 9.5) echo ' selected="selected"' ?>><?= __('UTC+09:30', 'profile') ?></option>
								<option value="10"<?php if ($forum_config['o_default_timezone'] == 10) echo ' selected="selected"' ?>><?= __('UTC+10:00', 'profile') ?></option>
								<option value="10.5"<?php if ($forum_config['o_default_timezone'] == 10.5) echo ' selected="selected"' ?>><?= __('UTC+10:30', 'profile') ?></option>
								<option value="11"<?php if ($forum_config['o_default_timezone'] == 11) echo ' selected="selected"' ?>><?= __('UTC+11:00', 'profile') ?></option>
								<option value="11.5"<?php if ($forum_config['o_default_timezone'] == 11.5) echo ' selected="selected"' ?>><?= __('UTC+11:30', 'profile') ?></option>
								<option value="12"<?php if ($forum_config['o_default_timezone'] == 12) echo ' selected="selected"' ?>><?= __('UTC+12:00', 'profile') ?></option>
								<option value="12.75"<?php if ($forum_config['o_default_timezone'] == 12.75) echo ' selected="selected"' ?>><?= __('UTC+12:45', 'profile') ?></option>
								<option value="13"<?php if ($forum_config['o_default_timezone'] == 13) echo ' selected="selected"' ?>><?= __('UTC+13:00', 'profile') ?></option>
								<option value="14"<?php if ($forum_config['o_default_timezone'] == 14) echo ' selected="selected"' ?>><?= __('UTC+14:00', 'profile') ?></option>
							</select></span>
						</div>
					</div>
<?php ($hook = get_hook('aop_setup_pre_default_dst')) ? eval($hook) : null; ?>
					<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
						<div class="sf-box checkbox">
							<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[default_dst]" value="1"<?php if ($forum_config['o_default_dst'] == 1) echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo $lang_admin_settings['DST label'] ?></label>
						</div>
					</div>
<?php ($hook = get_hook('aop_setup_pre_time_format')) ? eval($hook) : null; ?>
					<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
						<div class="sf-box text">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Time format label'] ?></span><small><?php printf($lang_admin_settings['Current format'], format_time(time(), 2, null, $forum_config['o_time_format']), $lang_admin_settings['External format help']) ?></small></label><br />
							<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[time_format]" size="25" maxlength="25" value="<?php echo forum_htmlencode($forum_config['o_time_format']) ?>" /></span>
						</div>
					</div>
<?php ($hook = get_hook('aop_setup_pre_date_format')) ? eval($hook) : null; ?>
					<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
						<div class="sf-box text">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Date format label'] ?></span><small><?php printf($lang_admin_settings['Current format'], format_time(time(), 1, $forum_config['o_date_format'], null, true), $lang_admin_settings['External format help']) ?></small></label><br />
							<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[date_format]" size="25" maxlength="25" value="<?php echo forum_htmlencode($forum_config['o_date_format']) ?>" /></span>
						</div>
					</div>
<?php ($hook = get_hook('aop_setup_pre_local_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php

	($hook = get_hook('aop_setup_local_fieldset_end')) ? eval($hook) : null;

	// Reset counter
	$forum_page['group_count'] = $forum_page['item_count'] = 0;

?>
				<div class="content-head">
					<h2 class="hn"><span><?php echo $lang_admin_settings['Setup timeouts'] ?></span></h2>
				</div>
<?php ($hook = get_hook('aop_setup_pre_timeouts_fieldset')) ? eval($hook) : null; ?>
				<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
					<legend class="group-legend"><strong><?php echo $lang_admin_settings['Setup timeouts legend'] ?></strong></legend>
<?php ($hook = get_hook('aop_setup_pre_visit_timeout')) ? eval($hook) : null; ?>
					<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
						<div class="sf-box text">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Visit timeout label'] ?></span><small><?php echo $lang_admin_settings['Visit timeout help'] ?></small></label><br />
							<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[timeout_visit]" size="5" maxlength="5" value="<?php echo $forum_config['o_timeout_visit'] ?>" /></span>
						</div>
					</div>
<?php ($hook = get_hook('aop_setup_pre_online_timeout')) ? eval($hook) : null; ?>
					<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
						<div class="sf-box text">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Online timeout label'] ?></span><small><?php echo $lang_admin_settings['Online timeout help'] ?></small></label><br />
							<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[timeout_online]" size="5" maxlength="5" value="<?php echo $forum_config['o_timeout_online'] ?>" /></span>
						</div>
					</div>
<?php ($hook = get_hook('aop_setup_pre_redirect_time')) ? eval($hook) : null; ?>
					<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
						<div class="sf-box text">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Redirect time label'] ?></span><small><?php echo $lang_admin_settings['Redirect time help'] ?></small></label><br />
							<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[redirect_delay]" size="5" maxlength="5" value="<?php echo $forum_config['o_redirect_delay'] ?>" /></span>
						</div>
					</div>
<?php ($hook = get_hook('aop_setup_pre_timeouts_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php

	($hook = get_hook('aop_setup_timeouts_fieldset_end')) ? eval($hook) : null;

	// Reset counter
	$forum_page['group_count'] = $forum_page['item_count'] = 0;

?>
				<div class="content-head">
					<h2 class="hn"><span><?php echo $lang_admin_settings['Setup pagination'] ?></span></h2>
				</div>
<?php ($hook = get_hook('aop_setup_pre_pagination_fieldset')) ? eval($hook) : null; ?>
				<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
					<legend class="group-legend"><strong><?php echo $lang_admin_settings['Setup pagination legend'] ?></strong></legend>
<?php ($hook = get_hook('aop_setup_pre_topics_per_page')) ? eval($hook) : null; ?>
					<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
						<div class="sf-box text">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Topics per page label'] ?></span></label><br />
							<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[disp_topics_default]" size="5" maxlength="3" value="<?php echo $forum_config['o_disp_topics_default'] ?>" /></span>
						</div>
					</div>
<?php ($hook = get_hook('aop_setup_pre_posts_per_page')) ? eval($hook) : null; ?>
					<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
						<div class="sf-box text">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Posts per page label'] ?></span></label><br />
							<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[disp_posts_default]" size="5" maxlength="3" value="<?php echo $forum_config['o_disp_posts_default'] ?>" /></span>
						</div>
					</div>
<?php ($hook = get_hook('aop_setup_pre_topic_review')) ? eval($hook) : null; ?>
					<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
						<div class="sf-box frm-short text">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Topic review label'] ?></span><small><?php echo $lang_admin_settings['Topic review help'] ?></small></label><br />
							<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[topic_review]" size="5" maxlength="3" value="<?php echo $forum_config['o_topic_review'] ?>" /></span>
						</div>
					</div>
<?php ($hook = get_hook('aop_setup_pre_pagination_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php

	($hook = get_hook('aop_setup_pagination_fieldset_end')) ? eval($hook) : null;

	// Reset counter
	$forum_page['group_count'] = $forum_page['item_count'] = 0;

?>
				<div class="content-head">
					<h2 class="hn"><span><?php echo $lang_admin_settings['Setup reports'] ?></span></h2>
				</div>
<?php ($hook = get_hook('aop_setup_pre_reports_fieldset')) ? eval($hook) : null; ?>
				<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
					<legend class="group-legend"><strong><?php echo $lang_admin_settings['Setup reports legend'] ?></strong></legend>
					<fieldset class="mf-set set<?php echo ++$forum_page['item_count'] ?>">
						<legend><span><?php echo $lang_admin_settings['Reporting method'] ?></span></legend>
						<div class="mf-box">
							<div class="mf-item">
								<span class="fld-input"><input type="radio" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[report_method]" value="0"<?php if ($forum_config['o_report_method'] == '0') echo ' checked="checked"' ?> /></span>
								<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo $lang_admin_settings['Report internal label'] ?></label>
							</div>
							<div class="mf-item">
								<span class="fld-input"><input type="radio" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[report_method]" value="1"<?php if ($forum_config['o_report_method'] == '1') echo ' checked="checked"' ?> /></span>
								<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo $lang_admin_settings['Report email label'] ?></label>
							</div>
							<div class="mf-item">
								<span class="fld-input"><input type="radio" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[report_method]" value="2"<?php if ($forum_config['o_report_method'] == '2') echo ' checked="checked"' ?> /></span>
								<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo $lang_admin_settings['Report both label'] ?></label>
							</div>
<?php ($hook = get_hook('aop_setup_new_reporting_method')) ? eval($hook) : null; ?>
						</div>
					</fieldset>
<?php ($hook = get_hook('aop_setup_pre_reports_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php

	($hook = get_hook('aop_setup_reports_fieldset_end')) ? eval($hook) : null;

	// Reset counter
	$forum_page['group_count'] = $forum_page['item_count'] = 0;

?>
				<div class="content-head">
					<h2 class="hn"><span><?php echo $lang_admin_settings['Setup URL'] ?></span></h2>
				</div>
				<div class="ct-box">
					<p class="warn"><?php echo $lang_admin_settings['URL scheme info'] ?></p>
				</div>
<?php ($hook = get_hook('aop_setup_pre_url_scheme_fieldset')) ? eval($hook) : null; ?>
				<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
					<legend class="group-legend"><strong><?php echo $lang_admin_settings['Setup URL legend'] ?></strong></legend>
<?php ($hook = get_hook('aop_setup_pre_url_scheme')) ? eval($hook) : null; ?>
					<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
						<div class="sf-box select">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['URL scheme label'] ?></span><small><?php echo $lang_admin_settings['URL scheme help'] ?></small></label><br />
							<span class="fld-input"><select id="fld<?php echo $forum_page['fld_count'] ?>" name="form[sef]">
<?php

	$url_schemes = get_scheme_packs();
	foreach ($url_schemes as $schema)
	{
		if ($forum_config['o_sef'] == $schema)
			echo "\t\t\t\t\t\t\t\t".'<option value="'.$schema.'" selected="selected">'.str_replace('_', ' ', $schema).'</option>'."\n";
		else
			echo "\t\t\t\t\t\t\t\t".'<option value="'.$schema.'">'.str_replace('_', ' ', $schema).'</option>'."\n";
	}

?>
							</select></span>
						</div>
					</div>
<?php ($hook = get_hook('aop_setup_pre_url_scheme_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php

	($hook = get_hook('aop_setup_url_scheme_fieldset_end')) ? eval($hook) : null;

	// Reset counter
	$forum_page['group_count'] = $forum_page['item_count'] = 0;

?>
				<div class="content-head">
					<h2 class="hn"><span><?php echo $lang_admin_settings['Setup links'] ?></span></h2>
				</div>
				<div class="ct-box">
					<p class="warn"><?php echo $lang_admin_settings['Setup links info'] ?></p>
				</div>
<?php ($hook = get_hook('aop_setup_pre_links_fieldset')) ? eval($hook) : null; ?>
				<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
					<legend class="group-legend"><strong><?php echo $lang_admin_settings['Setup links legend'] ?></strong></legend>
<?php ($hook = get_hook('aop_setup_pre_additional_navlinks')) ? eval($hook) : null; ?>
					<div class="txt-set set<?php echo ++$forum_page['item_count'] ?>">
						<div class="txt-box textarea">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Enter links label'] ?></span></label>
							<div class="txt-input"><span class="fld-input"><textarea id="fld<?php echo $forum_page['fld_count'] ?>" name="form[additional_navlinks]" rows="3" cols="55"><?php echo forum_htmlencode($forum_config['o_additional_navlinks']) ?></textarea></span></div>
						</div>
					</div>
<?php ($hook = get_hook('aop_setup_pre_links_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php ($hook = get_hook('aop_setup_links_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="save" value="<?php echo $lang_admin_common['Save changes'] ?>" /></span>
			</div>
		</form>
	</div>
