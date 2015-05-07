<?php
namespace punbb;

?>
<div id="brd-main" class="main">
	<div class="main-head">
		<h2 class="hn"><span><?= __('Confirm action head') ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<div class="ct-box info-box">
			<p><?= __('CSRF token mismatch') ?></p>
		</div>
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?=
				forum_htmlencode($form_action) ?>">
			<div class="hidden">
				<?= implode("\n", $hidden_fields) ?>
			</div>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" value="<?= __('Confirm') ?>" /></span>
				<span class="cancel"><input type="submit" name="confirm_cancel" value="<?= __('Cancel') ?>" /></span>
			</div>
		</form>
	</div>
</div>
<?php
