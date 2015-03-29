<?php

($hook = get_hook('ark_main_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo $lang_admin_ranks['Rank head'] ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo forum_link($forum_url['admin_ranks']) ?>?action=foo">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token(forum_link($forum_url['admin_ranks']).'?action=foo') ?>" />
			</div>
			<div class="ct-box" id="info-ranks-intro">
				<p><?php printf($lang_admin_ranks['Add rank intro'], '<a class="nowrap" href="'.forum_link($forum_url['admin_settings_features']).'">'.$lang_admin_common['Settings'].' &rarr; '.$lang_admin_common['Features'].'</a>') ?></p>
			</div>
			<fieldset class="frm-group frm-hdgroup group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo $lang_admin_ranks['Add rank legend'] ?></strong></legend>
<?php ($hook = get_hook('ark_pre_add_rank_fieldset')) ? eval($hook) : null; ?>
				<fieldset class="mf-set set<?php echo ++$forum_page['item_count'] ?><?php echo ($forum_page['item_count'] == 1) ? ' mf-head' : ' mf-extra' ?>">
					<legend><span><?php echo $lang_admin_ranks['New rank'] ?></span></legend>
					<div class="mf-box">
<?php ($hook = get_hook('ark_pre_add_rank_title')) ? eval($hook) : null; ?>
						<div class="mf-field mf-field1 text">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span class="fld-label"><?php echo $lang_admin_ranks['Rank title label'] ?></span></label><br />
							<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="new_rank" size="24" maxlength="50" required /></span>
						</div>
<?php ($hook = get_hook('ark_pre_add_rank_min_posts')) ? eval($hook) : null; ?>
						<div class="mf-field text">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span class="fld-label"><?php echo $lang_admin_ranks['Min posts label'] ?></span></label><br />
							<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="new_min_posts" size="7" maxlength="7" required /></span>
						</div>
<?php ($hook = get_hook('ark_pre_add_rank_submit')) ? eval($hook) : null; ?>
						<div class="mf-field text">
							<span class="submit"><input type="submit" name="add_rank" value="<?php echo $lang_admin_ranks['Add rank'] ?>" /></span>
						</div>
					</div>
<?php ($hook = get_hook('ark_pre_add_rank_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php ($hook = get_hook('ark_add_rank_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
		</form>
<?php

if (!empty($forum_ranks))
{
	// Reset fieldset counter
	$forum_page['group_count'] = $forum_page['item_count'] = 0;

?>
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo forum_link($forum_url['admin_ranks']) ?>?action=foo">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token(forum_link($forum_url['admin_ranks']).'?action=foo') ?>" />
			</div>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><span><?php echo $lang_admin_ranks['Existing ranks legend'] ?></span></legend>
<?php

	foreach ($forum_ranks as $rank_key => $cur_rank)
	{

	?>
<?php ($hook = get_hook('ark_pre_edit_cur_rank_fieldset')) ? eval($hook) : null; ?>
				<fieldset class="mf-set mf-extra set<?php echo ++$forum_page['item_count'] ?>">
					<legend><span><?php echo $lang_admin_ranks['Existing rank'] ?></span></legend>
					<div class="mf-box">
<?php ($hook = get_hook('ark_pre_edit_cur_rank_title')) ? eval($hook) : null; ?>
						<div class="mf-field text mf-field1">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_ranks['Rank title label'] ?></span></label><br />
							<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="rank[<?php echo $cur_rank['id'] ?>]" value="<?php echo forum_htmlencode($cur_rank['rank']) ?>" size="24" maxlength="50" required /></span>
						</div>
<?php ($hook = get_hook('ark_pre_edit_cur_rank_min_posts')) ? eval($hook) : null; ?>
						<div class="mf-field text">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span class="fld-label"><?php echo $lang_admin_ranks['Min posts label'] ?></span></label><br />
							<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="min_posts[<?php echo $cur_rank['id'] ?>]" value="<?php echo $cur_rank['min_posts'] ?>" size="7" maxlength="7" required /></span>
						</div>
<?php ($hook = get_hook('ark_pre_edit_cur_rank_submit')) ? eval($hook) : null; ?>
						<div class="mf-field text">
							<span class="submit"><input type="submit" name="update[<?php echo $cur_rank['id'] ?>]" value="<?php echo $lang_admin_ranks['Update'] ?>" /> <input type="submit" name="remove[<?php echo $cur_rank['id'] ?>]" value="<?php echo $lang_admin_ranks['Remove'] ?>" /></span>
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
				<p><?php echo $lang_admin_ranks['No ranks'] ?></p>
			</div>
		</div>
	</div>
<?php

}

($hook = get_hook('ark_end')) ? eval($hook) : null;
