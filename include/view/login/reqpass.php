<?php
namespace punbb;

($hook = get_hook('li_forgot_pass_output_start')) ? eval($hook) : null;

?>
	<div class="main-head">
		<h2 class="hn"><span><?= __('New password request', 'login') ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<div class="ct-box info-box">
			<p class="important"><?= __('New password info', 'login') ?></p>
		</div>

		<?php helper('errors', ['errors_title' => __('New password errors', 'login')]); ?>

		<div id="req-msg" class="req-warn ct-box error-box">
			<p class="important"><?= __('Required warn') ?></p>
		</div>
		<form id="afocus" class="frm-form" method="post" accept-charset="utf-8" action="<?php echo $forum_page['form_action'] ?>">
			<div class="hidden">
				<input type="hidden" name="form_sent" value="1" />
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token($forum_page['form_action']) ?>" />
			</div>
<?php ($hook = get_hook('li_forgot_pass_pre_group')) ? eval($hook) : null; ?>
			<div class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
<?php ($hook = get_hook('li_forgot_pass_pre_email')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text required">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('E-mail address', 'login') ?></span>
						<small><?= __('E-mail address help', 'login') ?></small></label><br />
						<span class="fld-input"><input id="fld<?php echo $forum_page['fld_count'] ?>" type="email" name="req_email" value="<?php if (isset($_POST['req_email'])) echo forum_htmlencode($_POST['req_email']); ?>" size="35" maxlength="80" required spellcheck="false" /></span>
					</div>
				</div>
<?php ($hook = get_hook('li_forgot_pass_pre_group_end')) ? eval($hook) : null; ?>
			</div>
<?php ($hook = get_hook('li_forgot_pass_group_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="request_pass" value="<?= __('Submit password request', 'login') ?>" /></span>
				<span class="cancel"><input type="submit" name="cancel" value="<?= __('Cancel') ?>" formnovalidate /></span>
			</div>
		</form>
	</div>
<?php

($hook = get_hook('li_forgot_pass_end')) ? eval($hook) : null;
