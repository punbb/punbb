<?php

($hook = get_hook('mr_merge_topics_output_start')) ? eval($hook) : null;

?>
	<div class="main-head">
		<h2 class="hn"><span><?= __('Confirm topic merge', 'misc') ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo $forum_page['form_action'] ?>">
			<div class="hidden">
				<?php echo implode("\n\t\t\t\t", $forum_page['hidden_fields'])."\n" ?>
			</div>
<?php ($hook = get_hook('mr_merge_topics_pre_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?= __('Merge topics', 'misc') ?></strong></legend>
<?php ($hook = get_hook('mr_merge_topics_pre_redirect_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo (++$forum_page['fld_count']) ?>" name="with_redirect" value="1" /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?= __('Redirect topic', 'misc') ?></span> <?= __('Leave merge redirects', 'misc') ?></label>
					</div>
				</div>
<?php ($hook = get_hook('mr_merge_topics_pre_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('mr_merge_topics_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="merge_topics_comply" value="<?= __('Merge', 'misc') ?>" /></span>
				<span class="cancel"><input type="submit" name="cancel" value="<?= __('Cancel') ?>" formnovalidate /></span>
			</div>
		</form>
	</div>
<?php

	$forum_id = $fid;

($hook = get_hook('mr_merge_topics_end')) ? eval($hook) : null;
