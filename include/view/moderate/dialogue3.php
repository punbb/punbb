<?php
namespace punbb;

($hook = get_hook('mr_move_topics_output_start')) ? eval($hook) : null;

?>
	<div class="main-head">
		<h2 class="hn"><span><?php echo end($forum_page['crumbs']).' '.
			__('To new forum', 'misc') ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo $forum_page['form_action'] ?>">
			<div class="hidden">
				<?php echo implode("\n\t\t\t\t", $forum_page['hidden_fields'])."\n" ?>
			</div>
<?php ($hook = get_hook('mr_move_topics_pre_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?= __('Move topic', 'misc') ?></strong></legend>
<?php ($hook = get_hook('mr_move_topics_pre_move_to_forum')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box select">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Move to', 'misc') ?></span></label><br />
						<span class="fld-input"><select id="fld<?php echo $forum_page['fld_count'] ?>" name="move_to_forum">
<?php

	$forum_page['cur_category'] = 0;
	foreach ($forum_list as $cur_forum)
	{
		($hook = get_hook('mr_move_topics_forum_loop_start')) ? eval($hook) : null;

		if ($cur_forum['cid'] != $forum_page['cur_category'])	// A new category since last iteration?
		{
			if ($forum_page['cur_category'])
				echo "\t\t\t\t".'</optgroup>'."\n";

			echo "\t\t\t\t".'<optgroup label="'.forum_htmlencode($cur_forum['cat_name']).'">'."\n";
			$forum_page['cur_category'] = $cur_forum['cid'];
		}

		if ($cur_forum['fid'] != $fid)
			echo "\t\t\t\t".'<option value="'.$cur_forum['fid'].'">'.forum_htmlencode($cur_forum['forum_name']).'</option>'."\n";

		($hook = get_hook('mr_move_topics_forum_loop_end')) ? eval($hook) : null;
	}

?>
						</optgroup>
						</select></span>
					</div>
				</div>
<?php ($hook = get_hook('mr_move_topics_pre_redirect_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo (++$forum_page['fld_count']) ?>" name="with_redirect" value="1"<?php if ($action == 'single') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo ($action == 'single') ?
							__('Leave redirect', 'misc') : __('Leave redirects', 'misc') ?></label>
					</div>
				</div>
<?php ($hook = get_hook('mr_move_topics_pre_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('mr_move_topics_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="move_topics_to" value="<?= __('Move', 'misc') ?>" /></span>
				<span class="cancel"><input type="submit" name="cancel" value="<?= __('Cancel') ?>" formnovalidate /></span>
			</div>
		</form>
	</div>
<?php

	$forum_id = $fid;

($hook = get_hook('mr_move_topics_end')) ? eval($hook) : null;
