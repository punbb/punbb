<?php
namespace punbb;

($hook = get_hook('se_main_output_start')) ? eval($hook) : null;

?>
	<div class="main-head">
<?php

	if (!empty($forum_page['main_head_options']))
		echo "\n\t\t".'<p class="options">'.implode(' ', $forum_page['main_head_options']).'</p>';

?>
		<h2 class="hn"><span><?= __('Search heading', 'search') ?></span></h2>
	</div>
	<div class="main-content main-frm">
<?php if ($advanced_search): ?>
		<div class="ct-box info-box">
			<ul class="info-list">
				<?php echo implode("\n\t\t\t\t", $forum_page['frm-info'])."\n" ?>
			</ul>
		</div>
<?php endif; ?>
		<form id="afocus" class="frm-form" method="get" accept-charset="utf-8" action="<?php echo forum_link('search') ?>">
			<div class="hidden">
				<input type="hidden" name="action" value="search" />
			</div>
<?php ($hook = get_hook('se_pre_criteria_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?= __('Search legend', 'search') ?></strong></legend>
<?php ($hook = get_hook('se_pre_keywords')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Keyword search', 'search') ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="keywords" size="40" maxlength="100" <?php echo ($advanced_search) ? '' : 'required' ?> /></span>
					</div>
				</div>
<?php ($hook = get_hook('se_pre_author')) ? eval($hook) : null; ?>
<?php if ($advanced_search): ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Author search', 'search') ?></span></label><br />
						<span class="fld-input"><input id="fld<?php echo $forum_page['fld_count'] ?>" type="text" name="author" size="40" maxlength="25" /></span>
					</div>
				</div>
<?php ($hook = get_hook('se_pre_search_in')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box select">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Search in', 'search') ?></span></label><br />
						<span class="fld-input"><select id="fld<?php echo $forum_page['fld_count'] ?>" name="search_in">
							<option value="all"><?= __('Message and subject', 'search') ?></option>
							<option value="message"><?= __('Message only', 'search') ?></option>
							<option value="topic"><?= __('Topic only', 'search') ?></option>
						</select></span>
					</div>
				</div>
<?php endif;

if ((!$advanced_search && (config()->o_search_all_forums == '0' &&
	!user()->is_admmod)) || $advanced_search) {

?>
<?php ($hook = get_hook('se_pre_forum_fieldset')) ? eval($hook) : null; ?>
				<fieldset class="mf-set set<?php echo ++$forum_page['item_count'] ?>">
					<legend><span><?= __('Forum search', 'search') ?> <em><?php echo (config()->o_search_all_forums == '1' ||
						user()->is_admmod) ?
						__('Forum search default', 'search') :
						__('Forum search require', 'search') ?></em></span></legend>
<?php ($hook = get_hook('se_pre_forum_checklist')) ? eval($hook) : null; ?>
					<div class="mf-box">
						<div class="checklist">

<?php

if (!empty($forums))
{
	$cur_category = 0;
	foreach ($forums as $cur_forum)
	{
		($hook = get_hook('se_forum_loop_start')) ? eval($hook) : null;

		if ($cur_forum['cid'] != $cur_category)	// A new category since last iteration?
		{
			if ($cur_category)
				echo "\t\t\t\t\t\t\t".'</fieldset>'."\n";

			echo "\t\t\t\t\t\t\t".'<fieldset>'."\n\t\t\t\t\t\t\t\t".'<legend><span>'.forum_htmlencode($cur_forum['cat_name']).':</span></legend>'."\n";
			$cur_category = $cur_forum['cid'];
		}

		echo "\t\t\t\t\t\t\t\t".'<div class="checklist-item"><span class="fld-input"><input type="checkbox" id="fld'.(++$forum_page['fld_count']).'" name="forum[]" value="'.$cur_forum['fid'].'" /></span> <label for="fld'.$forum_page['fld_count'].'">'.forum_htmlencode($cur_forum['forum_name']).'</label></div>'."\n";

		($hook = get_hook('se_forum_loop_end')) ? eval($hook) : null;
	}

	echo "\t\t\t\t\t\t\t".'</fieldset>'."\n";
}

?>
						</div>
					</div>
<?php ($hook = get_hook('se_pre_forum_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php } ?>

<?php ($hook = get_hook('se_forum_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('se_criteria_fieldset_end')) ? eval($hook) : null; ?>
<?php $forum_page['item_count'] = 0; ?>
<?php ($hook = get_hook('se_pre_results_fieldset')) ? eval($hook) : null; ?>
<?php if ($advanced_search): ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?= __('Results legend', 'search') ?></strong></legend>
<?php ($hook = get_hook('se_pre_sort_by')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box select">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Sort by', 'search') ?></span></label><br />
						<span class="fld-input"><select id="fld<?php echo $forum_page['fld_count'] ?>" name="sort_by">
						<?php echo implode("\n\t\t\t\t\t\t", $forum_page['frm-sort'])."\n" ?>
						</select></span>
					</div>
				</div>
<?php ($hook = get_hook('se_pre_sort_order_fieldset')) ? eval($hook) : null; ?>
				<fieldset class="mf-set set<?php echo ++$forum_page['item_count'] ?>">
					<legend><span><?= __('Sort order', 'search') ?></span></legend>
<?php ($hook = get_hook('se_pre_sort_order')) ? eval($hook) : null; ?>
					<div class="mf-box mf-yesno">
						<div class="mf-item">
							<span class="fld-input"><input type="radio" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="sort_dir" value="ASC" /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?= __('Ascending', 'search') ?></label>
						</div>
						<div class="mf-item">
							<span class="fld-input"><input type="radio" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="sort_dir" value="DESC" checked="checked" /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?= __('Descending', 'search') ?></label>
						</div>
					</div>
<?php ($hook = get_hook('se_pre_sort_order_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php ($hook = get_hook('se_pre_display_choices_fieldset')) ? eval($hook) : null; ?>
				<fieldset class="mf-set set<?php echo ++$forum_page['item_count'] ?>">
					<legend><span><?= __('Display results', 'search') ?></span></legend>
<?php ($hook = get_hook('se_pre_display_choices')) ? eval($hook) : null; ?>
					<div class="mf-box mf-yesno">
						<div class="mf-item">
							<span class="fld-input"><input type="radio" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="show_as" value="topics" /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?= __('Show as topics', 'search') ?></label>
						</div>
						<div class="mf-item">
							<span class="fld-input"><input type="radio" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="show_as" value="posts" checked="checked" /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?= __('Show as posts', 'search') ?></label>
						</div>
<?php ($hook = get_hook('se_new_display_choices')) ? eval($hook) : null; ?>
					</div>
<?php ($hook = get_hook('se_pre_display_choices_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php ($hook = get_hook('se_pre_results_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php endif; ($hook = get_hook('se_results_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="search" value="<?= __('Submit search', 'search') ?>" /></span>
			</div>
		</form>
	</div>
<?php

($hook = get_hook('se_end')) ? eval($hook) : null;
