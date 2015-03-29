<?php

($hook = get_hook('se_main_output_start')) ? eval($hook) : null;

?>
	<div class="main-head">
<?php

	if (!empty($forum_page['main_head_options']))
		echo "\n\t\t".'<p class="options">'.implode(' ', $forum_page['main_head_options']).'</p>';

?>
		<h2 class="hn"><span><?php echo $lang_search['Search heading'] ?></span></h2>
	</div>
	<div class="main-content main-frm">
<?php if ($advanced_search): ?>
		<div class="ct-box info-box">
			<ul class="info-list">
				<?php echo implode("\n\t\t\t\t", $forum_page['frm-info'])."\n" ?>
			</ul>
		</div>
<?php endif; ?>
		<form id="afocus" class="frm-form" method="get" accept-charset="utf-8" action="<?php echo forum_link($forum_url['search']) ?>">
			<div class="hidden">
				<input type="hidden" name="action" value="search" />
			</div>
<?php ($hook = get_hook('se_pre_criteria_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo $lang_search['Search legend'] ?></strong></legend>
<?php ($hook = get_hook('se_pre_keywords')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_search['Keyword search'] ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="keywords" size="40" maxlength="100" <?php echo ($advanced_search) ? '' : 'required' ?> /></span>
					</div>
				</div>
<?php ($hook = get_hook('se_pre_author')) ? eval($hook) : null; ?>
<?php if ($advanced_search): ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_search['Author search'] ?></span></label><br />
						<span class="fld-input"><input id="fld<?php echo $forum_page['fld_count'] ?>" type="text" name="author" size="40" maxlength="25" /></span>
					</div>
				</div>
<?php ($hook = get_hook('se_pre_search_in')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box select">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_search['Search in'] ?></span></label><br />
						<span class="fld-input"><select id="fld<?php echo $forum_page['fld_count'] ?>" name="search_in">
							<option value="all"><?php echo $lang_search['Message and subject'] ?></option>
							<option value="message"><?php echo $lang_search['Message only'] ?></option>
							<option value="topic"><?php echo $lang_search['Topic only'] ?></option>
						</select></span>
					</div>
				</div>
<?php endif; if ((!$advanced_search && ($forum_config['o_search_all_forums'] == '0' && !$forum_user['is_admmod'])) || $advanced_search): ?>
<?php ($hook = get_hook('se_pre_forum_fieldset')) ? eval($hook) : null; ?>
				<fieldset class="mf-set set<?php echo ++$forum_page['item_count'] ?>">
					<legend><span><?php echo $lang_search['Forum search'] ?> <em><?php echo ($forum_config['o_search_all_forums'] == '1' || $forum_user['is_admmod']) ? $lang_search['Forum search default'] : $lang_search['Forum search require'] ?></em></span></legend>
<?php ($hook = get_hook('se_pre_forum_checklist')) ? eval($hook) : null; ?>
					<div class="mf-box">
						<div class="checklist">
<?php

// Get the list of categories and forums
$query = array(
	'SELECT'	=> 'c.id AS cid, c.cat_name, f.id AS fid, f.forum_name, f.redirect_url',
	'FROM'		=> 'categories AS c',
	'JOINS'		=> array(
		array(
			'INNER JOIN'	=> 'forums AS f',
			'ON'			=> 'c.id=f.cat_id'
		),
		array(
			'LEFT JOIN'		=> 'forum_perms AS fp',
			'ON'			=> '(fp.forum_id=f.id AND fp.group_id='.$forum_user['g_id'].')'
		)
	),
	'WHERE'		=> '(fp.read_forum IS NULL OR fp.read_forum=1) AND f.redirect_url IS NULL',
	'ORDER BY'	=> 'c.disp_position, c.id, f.disp_position'
);

($hook = get_hook('se_qr_get_cats_and_forums')) ? eval($hook) : null;
$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

$forums = array();
while ($cur_forum = $forum_db->fetch_assoc($result))
{
	$forums[] = $cur_forum;
}

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
<?php endif; ?>
<?php ($hook = get_hook('se_forum_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('se_criteria_fieldset_end')) ? eval($hook) : null; ?>
<?php $forum_page['item_count'] = 0; ?>
<?php ($hook = get_hook('se_pre_results_fieldset')) ? eval($hook) : null; ?>
<?php if ($advanced_search): ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo $lang_search['Results legend'] ?></strong></legend>
<?php ($hook = get_hook('se_pre_sort_by')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box select">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_search['Sort by'] ?></span></label><br />
						<span class="fld-input"><select id="fld<?php echo $forum_page['fld_count'] ?>" name="sort_by">
						<?php echo implode("\n\t\t\t\t\t\t", $forum_page['frm-sort'])."\n" ?>
						</select></span>
					</div>
				</div>
<?php ($hook = get_hook('se_pre_sort_order_fieldset')) ? eval($hook) : null; ?>
				<fieldset class="mf-set set<?php echo ++$forum_page['item_count'] ?>">
					<legend><span><?php echo $lang_search['Sort order'] ?></span></legend>
<?php ($hook = get_hook('se_pre_sort_order')) ? eval($hook) : null; ?>
					<div class="mf-box mf-yesno">
						<div class="mf-item">
							<span class="fld-input"><input type="radio" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="sort_dir" value="ASC" /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo $lang_search['Ascending'] ?></label>
						</div>
						<div class="mf-item">
							<span class="fld-input"><input type="radio" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="sort_dir" value="DESC" checked="checked" /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo $lang_search['Descending'] ?></label>
						</div>
					</div>
<?php ($hook = get_hook('se_pre_sort_order_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php ($hook = get_hook('se_pre_display_choices_fieldset')) ? eval($hook) : null; ?>
				<fieldset class="mf-set set<?php echo ++$forum_page['item_count'] ?>">
					<legend><span><?php echo $lang_search['Display results'] ?></span></legend>
<?php ($hook = get_hook('se_pre_display_choices')) ? eval($hook) : null; ?>
					<div class="mf-box mf-yesno">
						<div class="mf-item">
							<span class="fld-input"><input type="radio" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="show_as" value="topics" /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo $lang_search['Show as topics'] ?></label>
						</div>
						<div class="mf-item">
							<span class="fld-input"><input type="radio" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="show_as" value="posts" checked="checked" /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo $lang_search['Show as posts'] ?></label>
						</div>
<?php ($hook = get_hook('se_new_display_choices')) ? eval($hook) : null; ?>
					</div>
<?php ($hook = get_hook('se_pre_display_choices_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php ($hook = get_hook('se_pre_results_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php endif; ($hook = get_hook('se_results_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="search" value="<?php echo $lang_search['Submit search'] ?>" /></span>
			</div>
		</form>
	</div>
<?php

($hook = get_hook('se_end')) ? eval($hook) : null;
