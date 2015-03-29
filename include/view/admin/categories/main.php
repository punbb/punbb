<?php

($hook = get_hook('acg_main_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo $lang_admin_categories['Add category head'] ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo $forum_page['form_action'] ?>">
			<div class="hidden">
				<?php echo implode("\n\t\t\t\t", $forum_page['hidden_fields'])."\n" ?>
			</div>
<?php ($hook = get_hook('acg_pre_add_cat_fieldset')) ? eval($hook) : null; ?>
			<div class="ct-box">
				<p><?php printf($lang_admin_categories['Add category info'], '<a href="'.forum_link($forum_url['admin_forums']).'">'.$lang_admin_categories['Add category info link text'].'</a>') ?></p>
			</div>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><span><?php echo $lang_admin_categories['Add category legend'] ?></span></legend>
<?php ($hook = get_hook('acg_pre_new_category_name')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_categories['New category label'] ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="new_cat_name" size="35" maxlength="80" required /></span>
					</div>
				</div>
<?php ($hook = get_hook('acg_pre_new_category_position')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_categories['Position label'] ?></span></label><br />
						<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="position" size="3" maxlength="3" /></span>
					</div>
				</div>
<?php ($hook = get_hook('acg_pre_add_cat_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('acg_add_cat_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="add_cat" value="<?php echo $lang_admin_categories['Add category'] ?>" /></span>
			</div>
		</form>
	</div>
<?php

($hook = get_hook('acg_post_add_cat_form')) ? eval($hook) : null;

// Reset counter
$forum_page['group_count'] = $forum_page['item_count'] = 0;

if (!empty($cat_list))
{

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo $lang_admin_categories['Del category head'] ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo $forum_page['form_action'] ?>">
			<div class="hidden">
				<?php echo implode("\n\t\t\t\t", $forum_page['hidden_fields'])."\n" ?>
			</div>
<?php ($hook = get_hook('acg_pre_del_cat_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo $lang_admin_categories['Delete category'] ?></strong></legend>
<?php ($hook = get_hook('acg_pre_del_category_select')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box select">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_categories['Select category label'] ?></span> <small><?php echo $lang_admin_common['Delete help'] ?></small></label><br />
						<span class="fld-input"><select id="fld<?php echo $forum_page['fld_count'] ?>" name="cat_to_delete">
<?php

	foreach ($cat_list as $cur_category)
	{
		echo "\t\t\t\t\t\t\t".'<option value="'.$cur_category['id'].'">'.forum_htmlencode($cur_category['cat_name']).'</option>'."\n";
	}

?>
						</select></span>
					</div>
				</div>
<?php ($hook = get_hook('acg_pre_del_cat_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('acg_del_cat_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="del_cat" value="<?php echo $lang_admin_categories['Delete category'] ?>" /></span>
			</div>
		</form>
	</div>
<?php

($hook = get_hook('acg_post_del_cat_form')) ? eval($hook) : null;

// Reset counter
$forum_page['group_count'] = $forum_page['item_count'] = 0;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo $lang_admin_categories['Edit categories head'] ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo $forum_page['form_action'] ?>">
			<div class="hidden">
				<?php echo implode("\n\t\t\t\t", $forum_page['hidden_fields'])."\n" ?>
			</div>
<?php

	($hook = get_hook('acg_edit_cat_fieldsets_start')) ? eval($hook) : null;
	foreach ($cat_list as $cur_category)
	{
		$forum_page['item_count'] = 0;
		($hook = get_hook('acg_pre_edit_cur_cat_fieldset')) ? eval($hook) : null;

?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><span><?php echo $lang_admin_categories['Edit category legend'] ?></span></legend>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
<?php ($hook = get_hook('acg_pre_edit_cat_name')) ? eval($hook) : null; ?>
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_categories['Category name label'] ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="cat_name[<?php echo $cur_category['id'] ?>]" value="<?php echo forum_htmlencode($cur_category['cat_name']) ?>" size="35" maxlength="80" required /></span>
					</div>
<?php ($hook = get_hook('acg_pre_edit_cat_position')) ? eval($hook) : null; ?>
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_categories['Position label'] ?></span></label><br />
						<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="cat_order[<?php echo $cur_category['id'] ?>]" value="<?php echo $cur_category['disp_position'] ?>" size="3" maxlength="3" /></span>
					</div>
				</div>
<?php ($hook = get_hook('acg_pre_edit_cur_cat_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php

		($hook = get_hook('acg_edit_cur_cat_fieldset_end')) ? eval($hook) : null;
	}

	($hook = get_hook('acg_edit_cat_fieldsets_end')) ? eval($hook) : null;

?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="update" value="<?php echo $lang_admin_categories['Update all categories'] ?>" /></span>
			</div>
		</form>
	</div>
<?php

	($hook = get_hook('acg_post_edit_cat_form')) ? eval($hook) : null;
}

($hook = get_hook('acg_end')) ? eval($hook) : null;
