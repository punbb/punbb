<?php
namespace punbb;

($hook = get_hook('ark_main_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo __('Rank head', 'admin_ranks') ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo link('admin_ranks') ?>?action=foo">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token(link('admin_ranks').'?action=foo') ?>" />
			</div>
			<div class="ct-box" id="info-ranks-intro">
				<p><?php printf(__('Add rank intro', 'admin_ranks'), '<a class="nowrap" href="'.link('admin_settings_features').'">'.
					__('Settings', 'admin_common').' &rarr; '.
					__('Features', 'admin_common').'</a>') ?></p>
			</div>
			<fieldset class="frm-group frm-hdgroup group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo __('Add rank legend', 'admin_ranks') ?></strong></legend>
<?php ($hook = get_hook('ark_pre_add_rank_fieldset')) ? eval($hook) : null; ?>
				<fieldset class="mf-set set<?php echo ++$forum_page['item_count'] ?><?php echo ($forum_page['item_count'] == 1) ? ' mf-head' : ' mf-extra' ?>">
					<legend><span><?php echo __('New rank', 'admin_ranks') ?></span></legend>
					<div class="mf-box">
<?php ($hook = get_hook('ark_pre_add_rank_title')) ? eval($hook) : null; ?>
						<div class="mf-field mf-field1 text">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span class="fld-label"><?php echo __('Rank title label', 'admin_ranks') ?></span></label><br />
							<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="new_rank" size="24" maxlength="50" required /></span>
						</div>
<?php ($hook = get_hook('ark_pre_add_rank_min_posts')) ? eval($hook) : null; ?>
						<div class="mf-field text">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span class="fld-label"><?php echo __('Min posts label', 'admin_ranks') ?></span></label><br />
							<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="new_min_posts" size="7" maxlength="7" required /></span>
						</div>
<?php ($hook = get_hook('ark_pre_add_rank_submit')) ? eval($hook) : null; ?>
						<div class="mf-field text">
							<span class="submit"><input type="submit" name="add_rank" value="<?php echo __('Add rank', 'admin_ranks') ?>" /></span>
						</div>
					</div>
<?php ($hook = get_hook('ark_pre_add_rank_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php ($hook = get_hook('ark_add_rank_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
		</form>
<?php

$cached_forum_ranks = cache()->get('cache_ranks');

if (!empty($cached_forum_ranks)) {
	// Reset fieldset counter
	$forum_page['group_count'] = $forum_page['item_count'] = 0;

?>
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo link('admin_ranks') ?>?action=foo">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token(link('admin_ranks').'?action=foo') ?>" />
			</div>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><span><?php echo __('Existing ranks legend', 'admin_ranks') ?></span></legend>
<?php

	foreach ($cached_forum_ranks as $rank_key => $cur_rank) { ?>
<?php ($hook = get_hook('ark_pre_edit_cur_rank_fieldset')) ? eval($hook) : null; ?>
				<fieldset class="mf-set mf-extra set<?php echo ++$forum_page['item_count'] ?>">
					<legend><span><?php echo __('Existing rank', 'admin_ranks') ?></span></legend>
					<div class="mf-box">
<?php ($hook = get_hook('ark_pre_edit_cur_rank_title')) ? eval($hook) : null; ?>
						<div class="mf-field text mf-field1">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Rank title label', 'admin_ranks') ?></span></label><br />
							<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="rank[<?php echo $cur_rank['id'] ?>]" value="<?php echo forum_htmlencode($cur_rank['rank']) ?>" size="24" maxlength="50" required /></span>
						</div>
<?php ($hook = get_hook('ark_pre_edit_cur_rank_min_posts')) ? eval($hook) : null; ?>
						<div class="mf-field text">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span class="fld-label"><?php echo __('Min posts label', 'admin_ranks') ?></span></label><br />
							<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="min_posts[<?php echo $cur_rank['id'] ?>]" value="<?php echo $cur_rank['min_posts'] ?>" size="7" maxlength="7" required /></span>
						</div>
<?php ($hook = get_hook('ark_pre_edit_cur_rank_submit')) ? eval($hook) : null; ?>
						<div class="mf-field text">
							<span class="submit"><input type="submit" name="update[<?php echo $cur_rank['id'] ?>]" value="<?php echo __('Update', 'admin_ranks') ?>" /> <input type="submit" name="remove[<?php echo $cur_rank['id'] ?>]" value="<?php echo __('Remove', 'admin_ranks') ?>" /></span>
						</div>
					</div>
<?php ($hook = get_hook('ark_pre_edit_cur_rank_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php

		($hook = get_hook('ark_edit_cur_rank_fieldset_end')) ? eval($hook) : null;

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
				<p><?php echo __('No ranks', 'admin_ranks') ?></p>
			</div>
		</div>
	</div>
<?php

}

($hook = get_hook('ark_end')) ? eval($hook) : null;
