<?php
namespace punbb;

($hook = get_hook('afo_edit_forum_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php printf(__('Edit forum head', 'admin_forums'), forum_htmlencode($cur_forum['forum_name'])) ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<form method="post" class="frm-form" accept-charset="utf-8" action="<?php echo forum_link('admin_forums') ?>?edit_forum=<?php echo $forum_id ?>">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token(forum_link('admin_forums').'?edit_forum='.$forum_id) ?>" />
			</div>
			<div class="content-head">
				<h3 class="hn"><span><?php echo __('Edit forum details head', 'admin_forums') ?></span></h3>
			</div>
<?php ($hook = get_hook('afo_edit_forum_pre_details_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo __('Edit forum details legend', 'admin_forums') ?></strong></legend>
<?php ($hook = get_hook('afo_edit_forum_pre_forum_name')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Forum name', 'admin_forums') ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="forum_name" size="35" maxlength="80" value="<?php echo forum_htmlencode($cur_forum['forum_name']) ?>" required /></span>
					</div>
				</div>
<?php ($hook = get_hook('afo_edit_forum_pre_forum_descrip')) ? eval($hook) : null; ?>
				<div class="txt-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="txt-box textarea">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Forum description', 'admin_forums') ?></span> <small><?php echo __('Forum description help', 'admin_forums') ?></small></label><br />
						<div class="txt-input"><span class="fld-input"><textarea id="fld<?php echo $forum_page['fld_count'] ?>" name="forum_desc" rows="3" cols="50"><?php echo forum_htmlencode($cur_forum['forum_desc']) ?></textarea></span></div>
					</div>
				</div>
<?php ($hook = get_hook('afo_edit_forum_pre_forum_cat')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box select">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Category assignment', 'admin_forums') ?></span></label><br />
						<span class="fld-input"><select id="fld<?php echo $forum_page['fld_count'] ?>" name="cat_id">
<?php

	while ($cur_cat = db()->fetch_assoc($result_categories))
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
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Sort topics by', 'admin_forums') ?></span></label><br />
						<span class="fld-input"><select id="fld<?php echo $forum_page['fld_count'] ?>" name="sort_by">
							<option value="0"<?php if ($cur_forum['sort_by'] == '0') echo ' selected="selected"' ?>><?php echo __('Sort last post', 'admin_forums') ?></option>
							<option value="1"<?php if ($cur_forum['sort_by'] == '1') echo ' selected="selected"' ?>><?php echo __('Sort topic start', 'admin_forums') ?></option>
<?php ($hook = get_hook('afo_edit_forum_modify_sort_by')) ? eval($hook) : null; ?>						</select></span>
					</div>
				</div>
<?php ($hook = get_hook('afo_edit_forum_pre_forum_redirect_url')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Redirect URL', 'admin_forums') ?></span></label><br />
						<span class="fld-input"><?php echo ($cur_forum['num_topics']) ? '<input type="url" id="fld'.$forum_page['fld_count'].'" name="redirect_url" size="45" maxlength="100" value="'.
							__('Only for empty forums', 'admin_forums') . '" disabled="disabled" />' : '<input type="text" id="fld'.$forum_page['fld_count'].'" name="redirect_url" size="35" maxlength="100" value="'.forum_htmlencode($cur_forum['redirect_url']).'" />' ?></span>
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
				<h3 class="hn"><span><?php echo __('Edit forum perms head', 'admin_forums') ?></span></h3>
			</div>
			<div class="ct-box">
				<ul>
					<?php echo implode("\n\t\t\t\t\t", $forum_page['form_info'])."\n" ?>
				</ul>
			</div>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo __('Edit forum perms legend', 'admin_forums') ?></strong></legend>
<?php

	$i = 2;

	while ($cur_perm = db()->fetch_assoc($result))
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
							<label for="fld<?php echo $forum_page['fld_count'] ?>"<?php if (!$read_forum_def) echo ' class="warn"' ?>><?php echo __('Read forum', 'admin_forums') ?> <?php if (!$read_forum_def) echo __('Not default', 'admin_forums') ?></label>
						</div>
<?php ($hook = get_hook('afo_edit_forum_pre_cur_group_post_replies_permission')) ? eval($hook) : null; ?>
						<div class="mf-item">
							<input type="hidden" name="post_replies_old[<?php echo $cur_perm['g_id'] ?>]" value="<?php echo ($post_replies) ? '1' : '0' ?>" />
							<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="post_replies_new[<?php echo $cur_perm['g_id'] ?>]" value="1"<?php if ($post_replies) echo ' checked="checked"'; echo ($cur_forum['redirect_url'] != '') ? ' disabled="disabled"' : ''; ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"<?php if (!$post_replies_def) echo ' class="warn"'; ?>><?php echo __('Post replies', 'admin_forums') ?> <?php if (!$post_replies_def) echo __('Not default', 'admin_forums') ?></label>
						</div>
<?php ($hook = get_hook('afo_edit_forum_pre_cur_group_post_topics_permission')) ? eval($hook) : null; ?>
						<div class="mf-item">
							<input type="hidden" name="post_topics_old[<?php echo $cur_perm['g_id'] ?>]" value="<?php echo ($post_topics) ? '1' : '0' ?>" />
							<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="post_topics_new[<?php echo $cur_perm['g_id'] ?>]" value="1"<?php if ($post_topics) echo ' checked="checked"'; echo ($cur_forum['redirect_url'] != '') ? ' disabled="disabled"' : ''; ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"<?php if (!$post_topics_def) echo ' class="warn"'; ?>><?php echo __('Post topics', 'admin_forums') ?> <?php if (!$post_topics_def) echo __('Not default', 'admin_forums') ?></label>
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
				<span class="submit primary"><input type="submit" name="save" value="<?php echo __('Save changes', 'admin_common') ?>" /></span>
				<span class="submit"><input type="submit" name="revert_perms" value="<?php echo __('Restore defaults', 'admin_forums') ?>" formnovalidate /></span>
			</div>
		</form>
	</div>
<?php

($hook = get_hook('afo_edit_forum_end')) ? eval($hook) : null;
