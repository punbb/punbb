<?php

($hook = get_hook('aop_announcements_output_start')) ? eval($hook) : null;

?>
	<div class="main-content main-frm">
		<div class="content-head">
			<h2 class="hn"><span><?php echo $lang_admin_settings['Announcements head'] ?></span></h2>
		</div>
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo forum_link($forum_url['admin_settings_announcements']) ?>">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token(forum_link($forum_url['admin_settings_announcements'])) ?>" />
				<input type="hidden" name="form_sent" value="1" />
			</div>
<?php ($hook = get_hook('aop_announcements_pre_announcement_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo $lang_admin_settings['Announcements legend'] ?></strong></legend>
<?php ($hook = get_hook('aop_announcements_pre_enable_announcement_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[announcement]" value="1"<?php if ($forum_config['o_announcement'] == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Enable announcement'] ?></span> <?php echo $lang_admin_settings['Enable announcement label'] ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_announcements_pre_announcement_heading')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Announcement heading label'] ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[announcement_heading]" size="50" maxlength="255" value="<?php echo forum_htmlencode($forum_config['o_announcement_heading']) ?>" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aop_announcements_pre_announcement_message')) ? eval($hook) : null; ?>
				<div class="txt-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="txt-box textarea">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_settings['Announcement message label'] ?></span><small><?php echo $lang_admin_settings['Announcement message help'] ?></small></label>
						<div class="txt-input"><span class="fld-input"><textarea id="fld<?php echo $forum_page['fld_count'] ?>" name="form[announcement_message]" rows="5" cols="55"><?php echo forum_htmlencode($forum_config['o_announcement_message']) ?></textarea></span></div>
					</div>
				</div>
<?php ($hook = get_hook('aop_announcements_pre_announcement_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('aop_announcements_announcement_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="save" value="<?php echo $lang_admin_common['Save changes'] ?>" /></span>
			</div>
		</form>
	</div>
