<?php
namespace punbb;

($hook = get_hook('mi_email_output_start')) ? eval($hook) : null;

?>
	<div class="main-head">
		<h2 class="hn"><span><?php echo $forum_page['main_head'] ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<div class="ct-box warn-box">
			<p class="important"><?= __('E-mail disclosure note', 'misc') ?></p>
		</div>

	<?php helper('errors', ['errors_title' => __('Form e-mail errors', 'misc')]) ?>

	<div id="req-msg" class="req-warn ct-box error-box">
			<p class="important"><?= __('Required warn') ?></p>
		</div>
		<form id="afocus" class="frm-form" method="post" accept-charset="utf-8" action="<?php echo $forum_page['form_action'] ?>">
			<div class="hidden">
				<?php echo implode("\n\t\t\t\t", $forum_page['hidden_fields'])."\n" ?>
			</div>
<?php ($hook = get_hook('mi_email_pre_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?= __('Write e-mail', 'misc') ?></strong></legend>
<?php ($hook = get_hook('mi_email_pre_subject')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text required longtext">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('E-mail subject', 'misc') ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="req_subject" value="<?php echo(isset($_POST['req_subject']) ? forum_htmlencode($_POST['req_subject']) : '') ?>" size="<?php echo FORUM_SUBJECT_MAXIMUM_LENGTH ?>" maxlength="<?php echo FORUM_SUBJECT_MAXIMUM_LENGTH ?>" required /></span>
					</div>
				</div>
<?php ($hook = get_hook('mi_email_pre_message_contents')) ? eval($hook) : null; ?>
				<div class="txt-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="txt-box textarea required">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('E-mail message', 'misc') ?></span></label>
						<div class="txt-input"><span class="fld-input"><textarea id="fld<?php echo $forum_page['fld_count'] ?>" name="req_message" rows="10" cols="95" required><?php echo(isset($_POST['req_message']) ? forum_htmlencode($_POST['req_message']) : '') ?></textarea></span></div>
					</div>
				</div>
<?php ($hook = get_hook('mi_email_pre_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('mi_email_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="submit" value="<?= __('Submit') ?>" /></span>
				<span class="cancel"><input type="submit" name="cancel" value="<?= __('Cancel') ?>" formnovalidate /></span>
			</div>
		</form>
	</div>
<?php

($hook = get_hook('mi_email_end')) ? eval($hook) : null;
