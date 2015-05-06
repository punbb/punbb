<?php
namespace punbb;

global $errors;

($hook = get_hook('pf_change_details_signature_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php printf(($forum_page['own_profile']) ?
			__('Sig welcome', 'profile') : __('Sig welcome user', 'profile'), forum_htmlencode($user['username'])) ?></span></h2>
	</div>
	<div class="main-content main-frm">
	<?php
		if (!empty($forum_page['text_options']))
			echo "\t\t".'<p class="content-options options">'.
			sprintf(__('You may use'), implode(' ', $forum_page['text_options'])).'</p>'."\n";
	?>

		<?php template()->helper('errors', [
			'errors_title' => __('Profile update errors', 'profile'),
			'errors' => $errors
		]) ?>

		<form id="afocus" class="frm-form frm-ctrl-submit" method="post" accept-charset="utf-8" action="<?= $form_action ?>">
			<div class="hidden">
				<?php echo implode("\n\t\t\t\t", $forum_page['hidden_fields'])."\n" ?>
			</div>
<?php ($hook = get_hook('pf_change_details_signature_pre_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?= __('Signature', 'profile') ?></strong></legend>
<?php ($hook = get_hook('pf_change_details_signature_pre_signature_demo')) ? eval($hook) : null; ?>
<?php if (isset($forum_page['sig_demo'])): ?>
				<div class="ct-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="ct-box">
						<h3 class="ct-legend hn"><?= __('Current signature', 'profile') ?></h3>
						<div class="sig-demo"><?php echo $forum_page['sig_demo'] ?></div>
					</div>
				</div>
<?php endif; ($hook = get_hook('pf_change_details_signature_pre_signature_text')) ? eval($hook) : null; ?>
				<div class="txt-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="txt-box textarea">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Compose signature', 'profile') ?></span>
						<small><?php printf(__('Sig max size', 'profile'), forum_number_format(config()->p_sig_length), forum_number_format(config()->p_sig_lines)) ?></small></label>
						<div class="txt-input"><span class="fld-input"><textarea id="fld<?php echo $forum_page['fld_count'] ?>" name="signature" rows="4" cols="65"><?php echo(isset($_POST['signature']) ? forum_htmlencode($_POST['signature']) : forum_htmlencode($user['signature'])) ?></textarea></span></div>
					</div>
				</div>
<?php ($hook = get_hook('pf_change_details_signature_pre_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('pf_change_details_signature_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="update" value="<?= __('Update profile', 'profile') ?>" /></span>
			</div>
		</form>
	</div>
<?php

($hook = get_hook('pf_change_details_signature_end')) ? eval($hook) : null;
