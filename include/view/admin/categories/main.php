<?php
namespace punbb;

($hook = get_hook('acg_main_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?= __('Add category head', 'admin_categories') ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo $forum_page['form_action'] ?>">
			<div class="hidden">
				<?php echo implode("\n\t\t\t\t", $forum_page['hidden_fields'])."\n" ?>
			</div>
<?php ($hook = get_hook('acg_pre_add_cat_fieldset')) ? eval($hook) : null; ?>
			<div class="ct-box">
				<p><?php printf(__('Add category info', 'admin_categories'), '<a href="'.forum_link('admin_forums').'">'.
					__('Add category info link text', 'admin_categories') . '</a>') ?></p>
			</div>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><span><?= __('Add category legend', 'admin_categories') ?></span></legend>
<?php ($hook = get_hook('acg_pre_new_category_name')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('New category label', 'admin_categories') ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="new_cat_name" size="35" maxlength="80" required /></span>
					</div>
				</div>
<?php ($hook = get_hook('acg_pre_new_category_position')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Position label', 'admin_categories') ?></span></label><br />
						<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="position" size="3" maxlength="3" /></span>
					</div>
				</div>
<?php ($hook = get_hook('acg_pre_add_cat_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('acg_add_cat_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="add_cat" value="<?= __('Add category', 'admin_categories') ?>" /></span>
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
		<h2 class="hn"><span><?= __('Del category head', 'admin_categories') ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo $forum_page['form_action'] ?>">
			<div class="hidden">
				<?php echo implode("\n\t\t\t\t", $forum_page['hidden_fields'])."\n" ?>
			</div>
<?php ($hook = get_hook('acg_pre_del_cat_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?= __('Delete category', 'admin_categories') ?></strong></legend>
<?php ($hook = get_hook('acg_pre_del_category_select')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box select">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Select category label', 'admin_categories') ?></span> <small><?php echo __('Delete help', 'admin_common') ?></small></label><br />
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
				<span class="submit primary"><input type="submit" name="del_cat" value="<?= __('Delete category', 'admin_categories') ?>" /></span>
			</div>
		</form>
	</div>
<?php

($hook = get_hook('acg_post_del_cat_form')) ? eval($hook) : null;

// Reset counter
$forum_page['group_count'] = $forum_page['item_count'] = 0;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?= __('Edit categories head', 'admin_categories') ?></span></h2>
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
				<legend class="group-legend"><span><?= __('Edit category legend', 'admin_categories') ?></span></legend>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
<?php ($hook = get_hook('acg_pre_edit_cat_name')) ? eval($hook) : null; ?>
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Category name label', 'admin_categories') ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="cat_name[<?php echo $cur_category['id'] ?>]" value="<?php echo forum_htmlencode($cur_category['cat_name']) ?>" size="35" maxlength="80" required /></span>
					</div>
<?php ($hook = get_hook('acg_pre_edit_cat_position')) ? eval($hook) : null; ?>
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Position label', 'admin_categories') ?></span></label><br />
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
				<span class="submit primary"><input type="submit" name="update" value="<?= __('Update all categories', 'admin_categories') ?>" /></span>
			</div>
		</form>
	</div>
<?php

	($hook = get_hook('acg_post_edit_cat_form')) ? eval($hook) : null;
}

($hook = get_hook('acg_end')) ? eval($hook) : null;
