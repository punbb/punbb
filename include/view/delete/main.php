<?php
namespace punbb;

($hook = get_hook('dl_main_output_start')) ? eval($hook) : null;

?>
	<div class="main-content main-frm">
		<div class="ct-box info-box">
			<ul class="info-list">
				<?php echo implode("\n\t\t\t\t", $forum_page['frm_info'])."\n" ?>
			</ul>
		</div>
<?php ($hook = get_hook('dl_pre_post_display')) ? eval($hook) : null; ?>
		<div class="post singlepost">
			<div class="posthead">
				<h3 class="hn post-ident"><?php echo implode(' ', $forum_page['post_ident']) ?></h3>
<?php ($hook = get_hook('dl_new_post_head_option')) ? eval($hook) : null; ?>
			</div>
			<div class="postbody">
				<div class="post-entry">
					<h4 class="entry-title hn"><?php echo $forum_page['item_subject'] ?></h4>
					<div class="entry-content">
						<?php echo $cur_post['message']."\n" ?>
					</div>
<?php ($hook = get_hook('dl_new_post_entry_data')) ? eval($hook) : null; ?>
				</div>
			</div>
		</div>
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?= $form_action ?>">
			<div class="hidden">
				<?php echo implode("\n\t\t\t\t", $forum_page['hidden_fields'])."\n" ?>
			</div>
<?php ($hook = get_hook('dl_pre_confirm_delete_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo ($cur_post['is_topic']) ?
					__('Delete topic', 'delete') :
					__('Delete post', 'delete') ?></strong></legend>
<?php ($hook = get_hook('dl_pre_confirm_delete_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="req_confirm" value="1" checked="checked" /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?= __('Please confirm', 'delete') ?></span> <?php printf(((($cur_post['is_topic'])) ?
							__('Delete topic label', 'delete') :
							__('Delete post label', 'delete')), forum_htmlencode($cur_post['poster']), format_time($cur_post['posted'])) ?></label>
					</div>
				</div>
<?php ($hook = get_hook('dl_pre_confirm_delete_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('dl_confirm_delete_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary caution"><input type="submit" name="delete" value="<?= ($cur_post['is_topic']) ?
					__('Delete topic', 'delete') : __('Delete post', 'delete') ?>" /></span>
				<span class="cancel"><input type="submit" name="cancel" value="<?= __('Cancel') ?>" formnovalidate /></span>
			</div>
		</form>
	</div>
<?php

$forum_id = $cur_post['fid'];

($hook = get_hook('dl_end')) ? eval($hook) : null;
