<?php

($hook = get_hook('acs_main_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo $lang_admin_censoring['Censored word head'] ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo forum_link($forum_url['admin_censoring']) ?>?action=foo">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token(forum_link($forum_url['admin_censoring']).'?action=foo') ?>" />
			</div>
			<div class="ct-box" id="info-censored-intro">
				<p><?php echo $lang_admin_censoring['Add censored word intro']; if ($forum_user['g_id'] == FORUM_ADMIN) printf(' '.$lang_admin_censoring['Add censored word extra'], '<a class="nowrap" href="'.forum_link($forum_url['admin_settings_features']).'">'.$lang_admin_common['Settings'].' &rarr; '.$lang_admin_common['Features'].'</a>') ?></p>
			</div>
			<fieldset class="frm-group frm-hdgroup group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><span><?php echo $lang_admin_censoring['Add censored word legend'] ?></span></legend>
<?php ($hook = get_hook('acs_pre_add_word_fieldset')) ? eval($hook) : null; ?>
				<fieldset class="mf-set set<?php echo ++$forum_page['item_count'] ?><?php echo ($forum_page['item_count'] == 1) ? ' mf-head' : ' mf-extra' ?>">
					<legend><span><?php echo $lang_admin_censoring['Add new word legend'] ?></span></legend>
					<div class="mf-box">
<?php ($hook = get_hook('acs_pre_add_search_for')) ? eval($hook) : null; ?>
						<div class="mf-field mf-field1">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span class="fld-label"><?php echo $lang_admin_censoring['Censored word label'] ?></span></label><br />
							<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="new_search_for" size="24" maxlength="60" required /></span>
						</div>
<?php ($hook = get_hook('acs_pre_add_replace_with')) ? eval($hook) : null; ?>
						<div class="mf-field">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span class="fld-label"><?php echo $lang_admin_censoring['Replacement label'] ?></span></label><br />
							<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="new_replace_with" size="24" maxlength="60" required /></span>
						</div>
<?php ($hook = get_hook('acs_pre_add_submit')) ? eval($hook) : null; ?>
						<div class="mf-field">
							<span class="submit"><input type="submit" name="add_word" value=" <?php echo $lang_admin_censoring['Add word'] ?> " /></span>
						</div>
					</div>
<?php ($hook = get_hook('acs_pre_add_word_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php ($hook = get_hook('acs_add_word_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
		</form>
<?php

if (!empty($forum_censors))
{
	// Reset
	$forum_page['group_count'] = $forum_page['item_count'] = 0;

?>
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo forum_link($forum_url['admin_censoring']) ?>?action=foo">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token(forum_link($forum_url['admin_censoring']).'?action=foo') ?>" />
			</div>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><span><?php echo $lang_admin_censoring['Edit censored word legend'] ?></span></legend>
<?php

	foreach ($forum_censors as $censor_key => $cur_word)
	{

	?>
<?php ($hook = get_hook('acs_pre_edit_word_fieldset')) ? eval($hook) : null; ?>
				<fieldset class="mf-set mf-extra set<?php echo ++$forum_page['item_count'] ?><?php echo ($forum_page['item_count'] == 1) ? ' mf-head' : ' mf-extra' ?>">
					<legend><span><?php echo $lang_admin_censoring['Existing censored word legend'] ?></span></legend>
					<div class="mf-box">
<?php ($hook = get_hook('acs_pre_edit_search_for')) ? eval($hook) : null; ?>
						<div class="mf-field mf-field1">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_censoring['Censored word label'] ?></span></label><br />
							<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="search_for[<?php echo $cur_word['id'] ?>]" value="<?php echo forum_htmlencode($cur_word['search_for']) ?>" size="24" maxlength="60" required /></span>
						</div>
<?php ($hook = get_hook('acs_pre_edit_replace_with')) ? eval($hook) : null; ?>
						<div class="mf-field">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_censoring['Replacement label'] ?></span></label><br />
							<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="replace_with[<?php echo $cur_word['id'] ?>]" value="<?php echo forum_htmlencode($cur_word['replace_with']) ?>" size="24" maxlength="60" required /></span>
						</div>
<?php ($hook = get_hook('acs_pre_edit_submit')) ? eval($hook) : null; ?>
						<div class="mf-field">
							<span class="submit"><input type="submit" name="update[<?php echo $cur_word['id'] ?>]" value="<?php echo $lang_admin_common['Update'] ?>" /> <input type="submit" name="remove[<?php echo $cur_word['id'] ?>]" value="<?php echo $lang_admin_common['Remove'] ?>" formnovalidate /></span>
						</div>
					</div>
<?php ($hook = get_hook('acs_pre_edit_word_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php ($hook = get_hook('acs_edit_word_fieldset_end')) ? eval($hook) : null; ?>
<?php

	}

?>
			</fieldset>
		</form>
	</div>
<?php

}
else
{

?>
		<div class="frm-form">
			<div class="ct-box">
				<p><?php echo $lang_admin_censoring['No censored words'] ?></p>
			</div>
		</div>
	</div>
<?php

}

($hook = get_hook('acs_end')) ? eval($hook) : null;
