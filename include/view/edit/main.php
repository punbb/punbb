<?php
namespace punbb;

($hook = get_hook('ed_main_output_start')) ? eval($hook) : null;

?>
	<div class="main-head">
		<h2 class="hn"><span><?php echo ($id == $cur_post['first_post_id']) ?
			__('Edit topic', 'post') : __('Edit reply', 'post') ?></span></h2>
	</div>

	<?php include view('edit/preview') ?>

	<div class="main-subhead">
		<h2 class="hn"><span><?php echo ($id != $cur_post['first_post_id']) ?
			__('Compose edited reply', 'post') : __('Compose edited topic', 'post') ?></span></h2>
	</div>
	<div id="post-form" class="main-content main-frm">
<?php

	if (!empty($forum_page['text_options']))
		echo "\t\t".'<p class="ct-options options">'.sprintf(__('You may use'), implode(' ', $forum_page['text_options'])).'</p>'."\n";

	helper('errors', ['errors_title' => __('Post errors', 'post')]);
?>
		<div id="req-msg" class="req-warn ct-box error-box">
			<p class="important"><?= __('Required warn') ?></p>
		</div>
		<form id="afocus" class="frm-form frm-ctrl-submit" method="post" accept-charset="utf-8" action="<?php echo $forum_page['form_action'] ?>"<?php if (!empty($forum_page['form_attributes'])) echo ' '.implode(' ', $forum_page['form_attributes']) ?>>
			<div class="hidden">
				<?php echo implode("\n\t\t\t\t", $forum_page['hidden_fields'])."\n" ?>
			</div>
<?php ($hook = get_hook('ed_pre_main_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?= __('Edit post legend', 'post') ?></strong></legend>
<?php ($hook = get_hook('ed_pre_subject')) ? eval($hook) : null; ?>
<?php if ($can_edit_subject): ?>				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text required">
						<label for="fld<?php echo ++ $forum_page['fld_count'] ?>"><span><?= __('Topic subject', 'post') ?></span></label><br />
						<span class="fld-input"><input id="fld<?php echo $forum_page['fld_count'] ?>" type="text" name="req_subject" size="<?php echo FORUM_SUBJECT_MAXIMUM_LENGTH ?>" maxlength="<?php echo FORUM_SUBJECT_MAXIMUM_LENGTH ?>" value="<?php echo forum_htmlencode(isset($_POST['req_subject']) ? $_POST['req_subject'] : $cur_post['subject']) ?>" required /></span>
					</div>
				</div>
<?php endif; ($hook = get_hook('ed_pre_message_box')) ? eval($hook) : null; ?>				<div class="txt-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="txt-box textarea required">
						<label for="fld<?php echo ++ $forum_page['fld_count'] ?>"><span><?= __('Write message', 'post') ?></span></label>
						<div class="txt-input"><span class="fld-input"><textarea id="fld<?php echo $forum_page['fld_count'] ?>" name="req_message" rows="15" cols="95" required spellcheck="true"><?php echo forum_htmlencode(isset($_POST['req_message']) ? $message : $cur_post['message']) ?></textarea></span></div>
					</div>
				</div>
<?php

$forum_page['checkboxes'] = array();
if ($forum_config['o_smilies'] == '1')
{
	if (isset($_POST['hide_smilies']) || $cur_post['hide_smilies'] == '1')
		$forum_page['checkboxes']['hide_smilies'] = '<div class="mf-item"><span class="fld-input"><input type="checkbox" id="fld'.(++$forum_page['fld_count']).'" name="hide_smilies" value="1" checked="checked" /></span> <label for="fld'.$forum_page['fld_count'].'">'.
		__('Hide smilies', 'post') . '</label></div>';
	else
		$forum_page['checkboxes']['hide_smilies'] = '<div class="mf-item"><span class="fld-input"><input type="checkbox" id="fld'.(++$forum_page['fld_count']).'" name="hide_smilies" value="1" /></span> <label for="fld'.$forum_page['fld_count'].'">'.
		__('Hide smilies', 'post') . '</label></div>';
}

if ($forum_page['is_admmod'])
{
	if ((isset($_POST['form_sent']) && isset($_POST['silent'])) || !isset($_POST['form_sent']))
		$forum_page['checkboxes']['silent'] = '<div class="mf-item"><span class="fld-input"><input type="checkbox" id="fld'.(++$forum_page['fld_count']).'" name="silent" value="1" checked="checked" /></span> <label for="fld'.$forum_page['fld_count'].'">'.
		__('Silent edit', 'post') . '</label></div>';
	else
		$forum_page['checkboxes']['silent'] = '<div class="mf-item"><span class="fld-input"><input type="checkbox" id="fld'.(++$forum_page['fld_count']).'" name="silent" value="1" /></span> <label for="fld'.$forum_page['fld_count'].'">'.
		__('Silent edit', 'post') . '</label></div>';
}

($hook = get_hook('ed_pre_checkbox_display')) ? eval($hook) : null;

if (!empty($forum_page['checkboxes']))
{

?>
				<fieldset class="mf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="mf-box checkbox">
						<?php echo implode("\n\t\t\t\t\t", $forum_page['checkboxes'])."\n" ?>
					</div>
<?php ($hook = get_hook('ed_pre_checkbox_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php

}

($hook = get_hook('ed_pre_main_fieldset_end')) ? eval($hook) : null;

?>
			</fieldset>
<?php

($hook = get_hook('ed_main_fieldset_end')) ? eval($hook) : null;

?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="submit_button" value="<?php echo ($id != $cur_post['first_post_id']) ?
					__('Submit reply', 'post') : __('Submit topic', 'post') ?>" /></span>
				<span class="submit"><input type="submit" name="preview" value="<?php echo ($id != $cur_post['first_post_id']) ?
					__('Preview reply', 'post') : __('Preview topic', 'post') ?>" /></span>
			</div>
		</form>
	</div>
<?php

$forum_id = $cur_post['fid'];

($hook = get_hook('ed_end')) ? eval($hook) : null;
