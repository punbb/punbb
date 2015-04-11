<?php
namespace punbb;

($hook = get_hook('vt_qpost_output_start')) ? eval($hook) : null;

// Setup form
$forum_page['form_action'] = forum_link($forum_url['new_reply'], $id);
$forum_page['form_attributes'] = array();

$forum_page['hidden_fields'] = array(
	'form_sent'		=> '<input type="hidden" name="form_sent" value="1" />',
	'form_user'		=> '<input type="hidden" name="form_user" value="'.((!user()['is_guest']) ? forum_htmlencode(user()['username']) : 'Guest').'" />',
	'csrf_token'	=> '<input type="hidden" name="csrf_token" value="'.generate_form_token($forum_page['form_action']).'" />'
);

if (!user()['is_guest'] && config()->o_subscriptions == '1' && (user()['auto_notify'] == '1' || $cur_topic['is_subscribed']))
	$forum_page['hidden_fields']['subscribe'] = '<input type="hidden" name="subscribe" value="1" />';

// Setup help
$forum_page['main_head_options'] = array();
if (config()->p_message_bbcode == '1')
	$forum_page['text_options']['bbcode'] = '<span'.(empty($forum_page['text_options']) ? ' class="first-item"' : '').'><a class="exthelp" href="'.forum_link($forum_url['help'], 'bbcode').'" title="'.sprintf(__('Help page'), __('BBCode')).'">'.__('BBCode').'</a></span>';
if (config()->p_message_img_tag == '1')
	$forum_page['text_options']['img'] = '<span'.(empty($forum_page['text_options']) ? ' class="first-item"' : '').'><a class="exthelp" href="'.forum_link($forum_url['help'], 'img').'" title="'.sprintf(__('Help page'), __('Images')).'">'.__('Images').'</a></span>';
if (config()->o_smilies == '1')
	$forum_page['text_options']['smilies'] = '<span'.(empty($forum_page['text_options']) ? ' class="first-item"' : '').'><a class="exthelp" href="'.forum_link($forum_url['help'], 'smilies').'" title="'.sprintf(__('Help page'), __('Smilies')).'">'.__('Smilies').'</a></span>';

($hook = get_hook('vt_quickpost_pre_display')) ? eval($hook) : null;

?>
<div class="main-subhead">
	<h2 class="hn"><span><?= __('Quick post', 'topic') ?></span></h2>
</div>
<div id="brd-qpost" class="main-content main-frm">
<?php if (!empty($forum_page['text_options'])) echo "\t".
	'<p class="content-options options">'.sprintf(__('You may use'), implode(' ', $forum_page['text_options'])).'</p>'."\n" ?>
	<div id="req-msg" class="req-warn ct-box error-box">
		<p class="important"><?= __('Required warn', 'topic') ?></p>
	</div>
	<form class="frm-form frm-ctrl-submit" method="post" accept-charset="utf-8" action="<?php echo $forum_page['form_action'] ?>"<?php if (!empty($forum_page['form_attributes'])) echo ' '.implode(' ', $forum_page['form_attributes']) ?>>
		<div class="hidden">
			<?php echo implode("\n\t\t\t\t", $forum_page['hidden_fields'])."\n" ?>
		</div>
<?php ($hook = get_hook('vt_quickpost_pre_fieldset')) ? eval($hook) : null; ?>
		<fieldset class="frm-group group1">
			<legend class="group-legend"><strong><?= __('Write message legend') ?></strong></legend>
<?php ($hook = get_hook('vt_quickpost_pre_message_box')) ? eval($hook) : null; ?>
			<div class="txt-set set1">
				<div class="txt-box textarea required">
					<label for="fld1"><span><?= __('Write message', 'post') ?></span></label>
					<div class="txt-input"><span class="fld-input"><textarea id="fld1" name="req_message" rows="7" cols="95" required spellcheck="true" ></textarea></span></div>
				</div>
			</div>
<?php ($hook = get_hook('vt_quickpost_pre_fieldset_end')) ? eval($hook) : null; ?>
		</fieldset>
<?php ($hook = get_hook('vt_quickpost_fieldset_end')) ? eval($hook) : null; ?>
		<div class="frm-buttons">
			<span class="submit primary"><input type="submit" name="submit_button" value="<?= __('Submit') ?>" /></span>
			<span class="submit"><input type="submit" name="preview" value="<?= __('Preview') ?>" /></span>
		</div>
	</form>
</div>
<?php

($hook = get_hook('vt_quickpost_end')) ? eval($hook) : null;
