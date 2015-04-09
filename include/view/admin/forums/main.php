<?php

($hook = get_hook('afo_main_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo __('Add forum head', 'admin_forums') ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo forum_link($forum_url['admin_forums']) ?>?action=adddel">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token(forum_link($forum_url['admin_forums']).'?action=adddel') ?>" />
			</div>
<?php ($hook = get_hook('afo_pre_add_forum_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo __('Add forum legend', 'admin_forums') ?></strong></legend>
<?php ($hook = get_hook('afo_pre_new_forum_name')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Forum name label', 'admin_forums') ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="forum_name" size="35" maxlength="80" required /></span>
					</div>
				</div>
<?php ($hook = get_hook('afo_pre_new_forum_position')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Position label', 'admin_forums') ?></span></label><br />
						<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="position" size="3" maxlength="3" /></span>
					</div>
				</div>
<?php ($hook = get_hook('afo_pre_new_forum_cat')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box select">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Add to category label', 'admin_forums') ?></span></label><br />
						<span class="fld-input"><select id="fld<?php echo $forum_page['fld_count'] ?>" name="add_to_cat">
<?php
	while ($cur_cat = db()->fetch_assoc($result_categories))
		echo "\t\t\t\t\t\t\t".'<option value="'.$cur_cat['id'].'">'.forum_htmlencode($cur_cat['cat_name']).'</option>'."\n";
?>
						</select></span>
					</div>
				</div>
<?php ($hook = get_hook('afo_pre_add_forum_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('afo_add_forum_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="add_forum" value=" <?php echo __('Add forum', 'admin_forums') ?> " /></span>
			</div>
		</form>
	</div>

<?php

if (!empty($forums))
{
	// Reset fieldset counter
	$forum_page['set_count'] = 0;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo __('Edit forums head', 'admin_forums') ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo forum_link($forum_url['admin_forums']) ?>?action=edit">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token(forum_link($forum_url['admin_forums']).'?action=edit') ?>" />
			</div>

<?php

	$cur_category = 0;
	$i = 2;
	$forum_page['item_count'] = 0;

	foreach ($forums as $cur_forum)
	{
		if ($cur_forum['cid'] != $cur_category)	// A new category since last iteration?
		{
			if ($i > 2) echo "\t\t\t".'</div>'."\n";

			$forum_page['group_count'] = $forum_page['item_count'] = 0;
?>
			<div class="content-head">
				<h3 class="hn"><span><?php printf(__('Forums in category', 'admin_forums'), forum_htmlencode($cur_forum['cat_name'])) ?></span></h3>
			</div>
			<div class="frm-group frm-hdgroup group<?php echo ++$forum_page['group_count'] ?>">

<?php

			$cur_category = $cur_forum['cid'];
		}

($hook = get_hook('afo_pre_edit_cur_forum_fieldset')) ? eval($hook) : null;

?>
				<fieldset id="forum<?php echo $cur_forum['fid'] ?>" class="mf-set set<?php echo ++$forum_page['item_count'] ?><?php echo ($forum_page['item_count'] == 1) ? ' mf-head' : ' mf-extra' ?>">
					<legend><span><?php printf(__('Edit or delete', 'admin_forums'), '<a href="'.forum_link($forum_url['admin_forums']).'?edit_forum='.$cur_forum['fid'].'">'.__('Edit', 'admin_forums').'</a>', '<a href="'.forum_link($forum_url['admin_forums']).'?del_forum='.$cur_forum['fid'].'">'.
						__('Delete', 'admin_forums').'</a>') ?></span></legend>
					<div class="mf-box">
<?php ($hook = get_hook('afo_pre_edit_cur_forum_name')) ? eval($hook) : null; ?>
						<div class="mf-field mf-field1 forum-field">
							<span class="aslabel"><?php echo __('Forum name', 'admin_forums') ?></span>
							<span class="fld-input"><?php echo forum_htmlencode($cur_forum['forum_name']) ?></span>
						</div>
<?php ($hook = get_hook('afo_pre_edit_cur_forum_position')) ? eval($hook) : null; ?>
						<div class="mf-field">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Position label', 'admin_forums') ?></span></label><br />
							<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="position[<?php echo $cur_forum['fid'] ?>]" size="3" maxlength="3" value="<?php echo $cur_forum['disp_position'] ?>" /></span>
						</div>
					</div>
<?php ($hook = get_hook('afo_pre_edit_cur_forum_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php

		($hook = get_hook('afo_edit_cur_forum_fieldset_end')) ? eval($hook) : null;

		++$i;
	}

?>
			</div>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="update_positions" value="<?php echo __('Update positions', 'admin_forums') ?>" /></span>
			</div>
		</form>
	</div>
<?php

}

($hook = get_hook('afo_end')) ? eval($hook) : null;
