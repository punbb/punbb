<?php

($hook = get_hook('pf_change_details_signature_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php printf(($forum_page['own_profile']) ? $lang_profile['Sig welcome'] : $lang_profile['Sig welcome user'], forum_htmlencode($user['username'])) ?></span></h2>
	</div>
	<div class="main-content main-frm">
	<?php
		if (!empty($forum_page['text_options']))
			echo "\t\t".'<p class="content-options options">'.sprintf($lang_common['You may use'], implode(' ', $forum_page['text_options'])).'</p>'."\n";
	?>

		<?= helper('errors', array(
			'errors_title' => $lang_profile['Profile update errors']
		)) ?>

		<form id="afocus" class="frm-form frm-ctrl-submit" method="post" accept-charset="utf-8" action="<?php echo $forum_page['form_action'] ?>">
			<div class="hidden">
				<?php echo implode("\n\t\t\t\t", $forum_page['hidden_fields'])."\n" ?>
			</div>
<?php ($hook = get_hook('pf_change_details_signature_pre_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo $lang_profile['Signature'] ?></strong></legend>
<?php ($hook = get_hook('pf_change_details_signature_pre_signature_demo')) ? eval($hook) : null; ?>
<?php if (isset($forum_page['sig_demo'])): ?>
				<div class="ct-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="ct-box">
						<h3 class="ct-legend hn"><?php echo $lang_profile['Current signature'] ?></h3>
						<div class="sig-demo"><?php echo $forum_page['sig_demo'] ?></div>
					</div>
				</div>
<?php endif; ($hook = get_hook('pf_change_details_signature_pre_signature_text')) ? eval($hook) : null; ?>
				<div class="txt-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="txt-box textarea">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_profile['Compose signature'] ?></span> <small><?php printf($lang_profile['Sig max size'], forum_number_format($forum_config['p_sig_length']), forum_number_format($forum_config['p_sig_lines'])) ?></small></label>
						<div class="txt-input"><span class="fld-input"><textarea id="fld<?php echo $forum_page['fld_count'] ?>" name="signature" rows="4" cols="65"><?php echo(isset($_POST['signature']) ? forum_htmlencode($_POST['signature']) : forum_htmlencode($user['signature'])) ?></textarea></span></div>
					</div>
				</div>
<?php ($hook = get_hook('pf_change_details_signature_pre_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('pf_change_details_signature_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="update" value="<?php echo $lang_profile['Update profile'] ?>" /></span>
			</div>
		</form>
	</div>
<?php

($hook = get_hook('pf_change_details_signature_end')) ? eval($hook) : null;
