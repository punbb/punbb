<?php

($hook = get_hook('vt_qpost_output_start')) ? eval($hook) : null;

// Setup form
$forum_page['form_action'] = forum_link($forum_url['new_reply'], $id);
$forum_page['form_attributes'] = array();

$forum_page['hidden_fields'] = array(
	'form_sent'		=> '<input type="hidden" name="form_sent" value="1" />',
	'form_user'		=> '<input type="hidden" name="form_user" value="'.((!$forum_user['is_guest']) ? forum_htmlencode($forum_user['username']) : 'Guest').'" />',
	'csrf_token'	=> '<input type="hidden" name="csrf_token" value="'.generate_form_token($forum_page['form_action']).'" />'
);

if (!$forum_user['is_guest'] && $forum_config['o_subscriptions'] == '1' && ($forum_user['auto_notify'] == '1' || $cur_topic['is_subscribed']))
	$forum_page['hidden_fields']['subscribe'] = '<input type="hidden" name="subscribe" value="1" />';

// Setup help
$forum_page['main_head_options'] = array();
if ($forum_config['p_message_bbcode'] == '1')
	$forum_page['text_options']['bbcode'] = '<span'.(empty($forum_page['text_options']) ? ' class="first-item"' : '').'><a class="exthelp" href="'.forum_link($forum_url['help'], 'bbcode').'" title="'.sprintf($lang_common['Help page'], $lang_common['BBCode']).'">'.$lang_common['BBCode'].'</a></span>';
if ($forum_config['p_message_img_tag'] == '1')
	$forum_page['text_options']['img'] = '<span'.(empty($forum_page['text_options']) ? ' class="first-item"' : '').'><a class="exthelp" href="'.forum_link($forum_url['help'], 'img').'" title="'.sprintf($lang_common['Help page'], $lang_common['Images']).'">'.$lang_common['Images'].'</a></span>';
if ($forum_config['o_smilies'] == '1')
	$forum_page['text_options']['smilies'] = '<span'.(empty($forum_page['text_options']) ? ' class="first-item"' : '').'><a class="exthelp" href="'.forum_link($forum_url['help'], 'smilies').'" title="'.sprintf($lang_common['Help page'], $lang_common['Smilies']).'">'.$lang_common['Smilies'].'</a></span>';

($hook = get_hook('vt_quickpost_pre_display')) ? eval($hook) : null;

?>
<div class="main-subhead">
	<h2 class="hn"><span><?php echo $lang_topic['Quick post'] ?></span></h2>
</div>
<div id="brd-qpost" class="main-content main-frm">
<?php if (!empty($forum_page['text_options'])) echo "\t".'<p class="content-options options">'.sprintf($lang_common['You may use'], implode(' ', $forum_page['text_options'])).'</p>'."\n" ?>
	<div id="req-msg" class="req-warn ct-box error-box">
		<p class="important"><?php echo $lang_topic['Required warn'] ?></p>
	</div>
	<form class="frm-form frm-ctrl-submit" method="post" accept-charset="utf-8" action="<?php echo $forum_page['form_action'] ?>"<?php if (!empty($forum_page['form_attributes'])) echo ' '.implode(' ', $forum_page['form_attributes']) ?>>
		<div class="hidden">
			<?php echo implode("\n\t\t\t\t", $forum_page['hidden_fields'])."\n" ?>
		</div>
<?php ($hook = get_hook('vt_quickpost_pre_fieldset')) ? eval($hook) : null; ?>
		<fieldset class="frm-group group1">
			<legend class="group-legend"><strong><?php echo $lang_common['Write message legend'] ?></strong></legend>
<?php ($hook = get_hook('vt_quickpost_pre_message_box')) ? eval($hook) : null; ?>
			<div class="txt-set set1">
				<div class="txt-box textarea required">
					<label for="fld1"><span><?php echo $lang_common['Write message'] ?></span></label>
					<div class="txt-input"><span class="fld-input"><textarea id="fld1" name="req_message" rows="7" cols="95" required spellcheck="true" ></textarea></span></div>
				</div>
			</div>
<?php ($hook = get_hook('vt_quickpost_pre_fieldset_end')) ? eval($hook) : null; ?>
		</fieldset>
<?php ($hook = get_hook('vt_quickpost_fieldset_end')) ? eval($hook) : null; ?>
		<div class="frm-buttons">
			<span class="submit primary"><input type="submit" name="submit_button" value="<?php echo $lang_common['Submit'] ?>" /></span>
			<span class="submit"><input type="submit" name="preview" value="<?php echo $lang_common['Preview'] ?>" /></span>
		</div>
	</form>
</div>
<?php

($hook = get_hook('vt_quickpost_end')) ? eval($hook) : null;
