<?php

($hook = get_hook('afo_edit_forum_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php printf($lang_admin_forums['Edit forum head'], forum_htmlencode($cur_forum['forum_name'])) ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<form method="post" class="frm-form" accept-charset="utf-8" action="<?php echo forum_link($forum_url['admin_forums']) ?>?edit_forum=<?php echo $forum_id ?>">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token(forum_link($forum_url['admin_forums']).'?edit_forum='.$forum_id) ?>" />
			</div>
			<div class="content-head">
				<h3 class="hn"><span><?php echo $lang_admin_forums['Edit forum details head'] ?></span></h3>
			</div>
<?php ($hook = get_hook('afo_edit_forum_pre_details_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo $lang_admin_forums['Edit forum details legend'] ?></strong></legend>
<?php ($hook = get_hook('afo_edit_forum_pre_forum_name')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_forums['Forum name'] ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="forum_name" size="35" maxlength="80" value="<?php echo forum_htmlencode($cur_forum['forum_name']) ?>" required /></span>
					</div>
				</div>
<?php ($hook = get_hook('afo_edit_forum_pre_forum_descrip')) ? eval($hook) : null; ?>
				<div class="txt-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="txt-box textarea">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_forums['Forum description'] ?></span> <small><?php echo $lang_admin_forums['Forum description help'] ?></small></label><br />
						<div class="txt-input"><span class="fld-input"><textarea id="fld<?php echo $forum_page['fld_count'] ?>" name="forum_desc" rows="3" cols="50"><?php echo forum_htmlencode($cur_forum['forum_desc']) ?></textarea></span></div>
					</div>
				</div>
<?php ($hook = get_hook('afo_edit_forum_pre_forum_cat')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box select">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_forums['Category assignment'] ?></span></label><br />
						<span class="fld-input"><select id="fld<?php echo $forum_page['fld_count'] ?>" name="cat_id">
<?php

	$query = array(
		'SELECT'	=> 'c.id, c.cat_name',
		'FROM'		=> 'categories AS c',
		'ORDER BY'	=> 'c.disp_position'
	);

	($hook = get_hook('afo_edit_forum_qr_get_categories')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	while ($cur_cat = $forum_db->fetch_assoc($result))
	{
		$selected = ($cur_cat['id'] == $cur_forum['cat_id']) ? ' selected="selected"' : '';
		echo "\t\t\t\t\t\t\t\t".'<option value="'.$cur_cat['id'].'"'.$selected.'>'.forum_htmlencode($cur_cat['cat_name']).'</option>'."\n";
	}

?>
						</select></span>
					</div>
				</div>
<?php ($hook = get_hook('afo_edit_forum_pre_forum_sort_by')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box select">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_forums['Sort topics by'] ?></span></label><br />
						<span class="fld-input"><select id="fld<?php echo $forum_page['fld_count'] ?>" name="sort_by">
							<option value="0"<?php if ($cur_forum['sort_by'] == '0') echo ' selected="selected"' ?>><?php echo $lang_admin_forums['Sort last post'] ?></option>
							<option value="1"<?php if ($cur_forum['sort_by'] == '1') echo ' selected="selected"' ?>><?php echo $lang_admin_forums['Sort topic start'] ?></option>
<?php ($hook = get_hook('afo_edit_forum_modify_sort_by')) ? eval($hook) : null; ?>						</select></span>
					</div>
				</div>
<?php ($hook = get_hook('afo_edit_forum_pre_forum_redirect_url')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_forums['Redirect URL'] ?></span></label><br />
						<span class="fld-input"><?php echo ($cur_forum['num_topics']) ? '<input type="url" id="fld'.$forum_page['fld_count'].'" name="redirect_url" size="45" maxlength="100" value="'.$lang_admin_forums['Only for empty forums'].'" disabled="disabled" />' : '<input type="text" id="fld'.$forum_page['fld_count'].'" name="redirect_url" size="35" maxlength="100" value="'.forum_htmlencode($cur_forum['redirect_url']).'" />' ?></span>
					</div>
				</div>
<?php ($hook = get_hook('afo_edit_forum_pre_details_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php

($hook = get_hook('afo_edit_forum_details_fieldset_end')) ? eval($hook) : null;

// Reset fieldset counter
$forum_page['group_count'] = $forum_page['item_count'] = 0;

($hook = get_hook('afo_edit_forum_pre_permissions_part')) ? eval($hook) : null;

?>
			<div class="content-head">
				<h3 class="hn"><span><?php echo $lang_admin_forums['Edit forum perms head'] ?></span></h3>
			</div>
			<div class="ct-box">
				<ul>
					<?php echo implode("\n\t\t\t\t\t", $forum_page['form_info'])."\n" ?>
				</ul>
			</div>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo $lang_admin_forums['Edit forum perms legend'] ?></strong></legend>
<?php

	$i = 2;

	$query = array(
		'SELECT'	=> 'g.g_id, g.g_title, g.g_read_board, g.g_post_replies, g.g_post_topics, fp.read_forum, fp.post_replies, fp.post_topics',
		'FROM'		=> 'groups AS g',
		'JOINS'		=> array(
			array(
				'LEFT JOIN'		=> 'forum_perms AS fp',
				'ON'			=> 'g.g_id=fp.group_id AND fp.forum_id='.$forum_id
			)
		),
		'WHERE'		=> 'g.g_id!='.FORUM_ADMIN,
		'ORDER BY'	=> 'g.g_id'
	);

	($hook = get_hook('afo_qr_get_forum_perms')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	while ($cur_perm = $forum_db->fetch_assoc($result))
	{
		$read_forum = ($cur_perm['read_forum'] != '0') ? true : false;
		$post_replies = (($cur_perm['g_post_replies'] == '0' && $cur_perm['post_replies'] == '1') || ($cur_perm['g_post_replies'] == '1' && $cur_perm['post_replies'] != '0')) ? true : false;
		$post_topics = (($cur_perm['g_post_topics'] == '0' && $cur_perm['post_topics'] == '1') || ($cur_perm['g_post_topics'] == '1' && $cur_perm['post_topics'] != '0')) ? true : false;

		// Determine if the current sittings differ from the default or not
		$read_forum_def = ($cur_perm['read_forum'] == '0') ? false : true;
		$post_replies_def = (($post_replies && $cur_perm['g_post_replies'] == '0') || (!$post_replies && ($cur_perm['g_post_replies'] == '' || $cur_perm['g_post_replies'] == '1'))) ? false : true;
		$post_topics_def = (($post_topics && $cur_perm['g_post_topics'] == '0') || (!$post_topics && ($cur_perm['g_post_topics'] == '' || $cur_perm['g_post_topics'] == '1'))) ? false : true;

($hook = get_hook('afo_edit_forum_pre_cur_group_permissions_fieldset')) ? eval($hook) : null;

?>
				<fieldset class="mf-set set<?php echo ++$forum_page['item_count'] ?>">
					<legend><span><?php echo forum_htmlencode($cur_perm['g_title']) ?></span></legend>
					<div class="mf-box mf-yesno">
<?php ($hook = get_hook('afo_edit_forum_pre_cur_group_read_forum_permission')) ? eval($hook) : null; ?>
						<div class="mf-item">
							<input type="hidden" name="read_forum_old[<?php echo $cur_perm['g_id'] ?>]" value="<?php echo ($read_forum) ? '1' : '0' ?>" />
							<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="read_forum_new[<?php echo $cur_perm['g_id'] ?>]" value="1"<?php if ($read_forum) echo ' checked="checked"'; echo ($cur_perm['g_read_board'] == '0') ? ' disabled="disabled"' : ''; ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"<?php if (!$read_forum_def) echo ' class="warn"' ?>><?php echo $lang_admin_forums['Read forum'] ?> <?php if (!$read_forum_def) echo $lang_admin_forums['Not default'] ?></label>
						</div>
<?php ($hook = get_hook('afo_edit_forum_pre_cur_group_post_replies_permission')) ? eval($hook) : null; ?>
						<div class="mf-item">
							<input type="hidden" name="post_replies_old[<?php echo $cur_perm['g_id'] ?>]" value="<?php echo ($post_replies) ? '1' : '0' ?>" />
							<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="post_replies_new[<?php echo $cur_perm['g_id'] ?>]" value="1"<?php if ($post_replies) echo ' checked="checked"'; echo ($cur_forum['redirect_url'] != '') ? ' disabled="disabled"' : ''; ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"<?php if (!$post_replies_def) echo ' class="warn"'; ?>><?php echo $lang_admin_forums['Post replies'] ?> <?php if (!$post_replies_def) echo $lang_admin_forums['Not default'] ?></label>
						</div>
<?php ($hook = get_hook('afo_edit_forum_pre_cur_group_post_topics_permission')) ? eval($hook) : null; ?>
						<div class="mf-item">
							<input type="hidden" name="post_topics_old[<?php echo $cur_perm['g_id'] ?>]" value="<?php echo ($post_topics) ? '1' : '0' ?>" />
							<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="post_topics_new[<?php echo $cur_perm['g_id'] ?>]" value="1"<?php if ($post_topics) echo ' checked="checked"'; echo ($cur_forum['redirect_url'] != '') ? ' disabled="disabled"' : ''; ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"<?php if (!$post_topics_def) echo ' class="warn"'; ?>><?php echo $lang_admin_forums['Post topics'] ?> <?php if (!$post_topics_def) echo $lang_admin_forums['Not default'] ?></label>
						</div>
<?php ($hook = get_hook('afo_edit_forum_post_cur_group_post_topics_permission')) ? eval($hook) : null; ?>
					</div>
<?php ($hook = get_hook('afo_edit_forum_pre_cur_group_permissions_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php

		($hook = get_hook('afo_edit_forum_cur_group_permissions_fieldset_end')) ? eval($hook) : null;

		++$i;
	}

?>
			</fieldset>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="save" value="<?php echo $lang_admin_common['Save changes'] ?>" /></span>
				<span class="submit"><input type="submit" name="revert_perms" value="<?php echo $lang_admin_forums['Restore defaults'] ?>" formnovalidate /></span>
			</div>
		</form>
	</div>
<?php

($hook = get_hook('afo_edit_forum_end')) ? eval($hook) : null;
