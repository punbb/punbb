<?php
namespace punbb;

($hook = get_hook('mr_confirm_delete_posts_output_start')) ? eval($hook) : null;

?>
	<div class="main-head">
		<h2 class="hn"><span><?= __('Confirm post delete', 'misc') ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?= $form_action ?>">
			<div class="hidden">
				<?= implode("\n", $hidden_fields) ?>
			</div>
<?php ($hook = get_hook('mr_confirm_delete_posts_pre_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?= __('Delete posts', 'misc') ?></strong></legend>
<?php ($hook = get_hook('mr_confirm_delete_posts_pre_confirm_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="req_confirm" value="1" checked="checked" /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?= __('Please confirm') ?></span> <?= __('Confirm post delete', 'misc') ?>.</label>
					</div>
				</div>
<?php ($hook = get_hook('mr_confirm_delete_posts_pre_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('mr_confirm_delete_posts_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary caution"><input type="submit" name="delete_posts_comply" value="<?= __('Delete') ?>" /></span>
				<span class="cancel"><input type="submit" name="cancel" value="<?= __('Cancel') ?>" formnovalidate /></span>
			</div>
		</form>
	</div>
<?php

		$forum_id = $fid;

($hook = get_hook('mr_confirm_delete_posts_end')) ? eval($hook) : null;
