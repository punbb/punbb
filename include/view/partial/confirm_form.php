<?php

($hook = get_hook('fn_csrf_confirm_form_pre_header_load')) ? eval($hook) : null;

?>
<div id="brd-main" class="main">
	<div class="main-head">
		<h2 class="hn"><span><?php echo $lang_common['Confirm action head'] ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<div class="ct-box info-box">
			<p><?php echo $lang_common['CSRF token mismatch'] ?></p>
		</div>
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo forum_htmlencode($forum_page['form_action']) ?>">
			<div class="hidden">
				<?php echo implode("\n\t\t\t\t", $forum_page['hidden_fields'])."\n" ?>
			</div>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" value="<?php echo $lang_common['Confirm'] ?>" /></span>
				<span class="cancel"><input type="submit" name="confirm_cancel" value="<?php echo $lang_common['Cancel'] ?>" /></span>
			</div>
		</form>
	</div>
</div>
<?php

($hook = get_hook('fn_csrf_confirm_form_end')) ? eval($hook) : null;
