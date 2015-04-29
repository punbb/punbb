<?php
namespace punbb;

($hook = get_hook('apr_main_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo __('Prune settings head', 'admin_prune') ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<div class="ct-box">
			<p><?php echo __('Prune intro', 'admin_prune') ?></p>
			<p class="important"><?php echo __('Prune caution', 'admin_prune') ?></p>
		</div>
		<div id="req-msg" class="req-warn ct-box error-box">
			<p class="important"><?php echo __('Required warn', 'admin_common') ?></p>
		</div>
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo forum_link('admin_prune') ?>?action=foo">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token(forum_link('admin_prune').'?action=foo') ?>" />
				<input type="hidden" name="form_sent" value="1" />
			</div>
<?php ($hook = get_hook('apr_pre_prune_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><span><?php echo __('Prune legend', 'admin_prune') ?></span></legend>
<?php ($hook = get_hook('apr_pre_prune_from')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box select">
					<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span class="fld-label"><?php echo __('Prune from', 'admin_prune') ?></span></label><br />
						<span class="fld-input"><select id="fld<?php echo $forum_page['fld_count'] ?>" name="prune_from">
							<option value="all"><?php echo __('All forums', 'admin_prune') ?></option>
<?php

	$cur_category = 0;
	while ($forum = db()->fetch_assoc($result))
	{
		($hook = get_hook('apr_pre_prune_forum_loop_start')) ? eval($hook) : null;

		if ($forum['cid'] != $cur_category)	// Are we still in the same category?
		{
			if ($cur_category)
				echo "\t\t\t\t\t\t\t\t".'</optgroup>'."\n";

			echo "\t\t\t\t\t\t\t\t".'<optgroup label="'.forum_htmlencode($forum['cat_name']).'">'."\n";
			$cur_category = $forum['cid'];
		}

		echo "\t\t\t\t\t\t\t\t\t".'<option value="'.$forum['fid'].'">'.forum_htmlencode($forum['forum_name']).'</option>'."\n";

		($hook = get_hook('apr_pre_prune_forum_loop_end')) ? eval($hook) : null;
	}

?>
						</optgroup>
						</select></span>
					</div>
				</div>
<?php ($hook = get_hook('apr_pre_prune_days')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text required">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Days old', 'admin_prune') ?></span></label><br />
						<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="req_prune_days" size="4" maxlength="4" required /></span>
					</div>
				</div>
<?php ($hook = get_hook('apr_pre_prune_sticky')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="prune_sticky" value="1" checked="checked" /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo __('Prune sticky enable', 'admin_prune') ?></label>
					</div>
				</div>
<?php ($hook = get_hook('apr_pre_prune_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('apr_prune_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="prune" value="<?php echo __('Prune topics', 'admin_prune') ?>" /></span>
			</div>
		</form>
	</div>
<?php

($hook = get_hook('apr_end')) ? eval($hook) : null;
