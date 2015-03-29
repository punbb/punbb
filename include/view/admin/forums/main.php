<?php

($hook = get_hook('afo_main_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo $lang_admin_forums['Add forum head'] ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo forum_link($forum_url['admin_forums']) ?>?action=adddel">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token(forum_link($forum_url['admin_forums']).'?action=adddel') ?>" />
			</div>
<?php ($hook = get_hook('afo_pre_add_forum_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo $lang_admin_forums['Add forum legend'] ?></strong></legend>
<?php ($hook = get_hook('afo_pre_new_forum_name')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_forums['Forum name label'] ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="forum_name" size="35" maxlength="80" required /></span>
					</div>
				</div>
<?php ($hook = get_hook('afo_pre_new_forum_position')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_forums['Position label'] ?></span></label><br />
						<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="position" size="3" maxlength="3" /></span>
					</div>
				</div>
<?php ($hook = get_hook('afo_pre_new_forum_cat')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box select">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_forums['Add to category label'] ?></span></label><br />
						<span class="fld-input"><select id="fld<?php echo $forum_page['fld_count'] ?>" name="add_to_cat">
<?php

	$query = array(
		'SELECT'	=> 'c.id, c.cat_name',
		'FROM'		=> 'categories AS c',
		'ORDER BY'	=> 'c.disp_position'
	);

	($hook = get_hook('afo_qr_get_categories')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	while ($cur_cat = $forum_db->fetch_assoc($result))
		echo "\t\t\t\t\t\t\t".'<option value="'.$cur_cat['id'].'">'.forum_htmlencode($cur_cat['cat_name']).'</option>'."\n";

?>
						</select></span>
					</div>
				</div>
<?php ($hook = get_hook('afo_pre_add_forum_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('afo_add_forum_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="add_forum" value=" <?php echo $lang_admin_forums['Add forum'] ?> " /></span>
			</div>
		</form>
	</div>

<?php

// Display all the categories and forums
$query = array(
	'SELECT'	=> 'c.id AS cid, c.cat_name, f.id AS fid, f.forum_name, f.disp_position',
	'FROM'		=> 'categories AS c',
	'JOINS'		=> array(
		array(
			'INNER JOIN'	=> 'forums AS f',
			'ON'			=> 'c.id=f.cat_id'
		)
	),
	'ORDER BY'	=> 'c.disp_position, c.id, f.disp_position'
);

($hook = get_hook('afo_qr_get_cats_and_forums')) ? eval($hook) : null;
$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

$forums = array();
while ($cur_forum = $forum_db->fetch_assoc($result))
{
	$forums[] = $cur_forum;
}

if (!empty($forums))
{
	// Reset fieldset counter
	$forum_page['set_count'] = 0;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo $lang_admin_forums['Edit forums head'] ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo forum_link($forum_url['admin_forums']) ?>?action=edit">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token(forum_link($forum_url['admin_forums']).'?action=edit') ?>" />
			</div>

<?php

	$cur_category = 0;
	$i = 2;
	$forum_page['item_count'] = 0;

	foreach ($forums as $cur_forum)
	{
		if ($cur_forum['cid'] != $cur_category)	// A new category since last iteration?
		{
			if ($i > 2) echo "\t\t\t".'</div>'."\n";

			$forum_page['group_count'] = $forum_page['item_count'] = 0;
?>
			<div class="content-head">
				<h3 class="hn"><span><?php printf($lang_admin_forums['Forums in category'], forum_htmlencode($cur_forum['cat_name'])) ?></span></h3>
			</div>
			<div class="frm-group frm-hdgroup group<?php echo ++$forum_page['group_count'] ?>">

<?php

			$cur_category = $cur_forum['cid'];
		}

($hook = get_hook('afo_pre_edit_cur_forum_fieldset')) ? eval($hook) : null;

?>
				<fieldset id="forum<?php echo $cur_forum['fid'] ?>" class="mf-set set<?php echo ++$forum_page['item_count'] ?><?php echo ($forum_page['item_count'] == 1) ? ' mf-head' : ' mf-extra' ?>">
					<legend><span><?php printf($lang_admin_forums['Edit or delete'], '<a href="'.forum_link($forum_url['admin_forums']).'?edit_forum='.$cur_forum['fid'].'">'.$lang_admin_forums['Edit'].'</a>', '<a href="'.forum_link($forum_url['admin_forums']).'?del_forum='.$cur_forum['fid'].'">'.$lang_admin_forums['Delete'].'</a>') ?></span></legend>
					<div class="mf-box">
<?php ($hook = get_hook('afo_pre_edit_cur_forum_name')) ? eval($hook) : null; ?>
						<div class="mf-field mf-field1 forum-field">
							<span class="aslabel"><?php echo $lang_admin_forums['Forum name'] ?></span>
							<span class="fld-input"><?php echo forum_htmlencode($cur_forum['forum_name']) ?></span>
						</div>
<?php ($hook = get_hook('afo_pre_edit_cur_forum_position')) ? eval($hook) : null; ?>
						<div class="mf-field">
							<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_forums['Position label'] ?></span></label><br />
							<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="position[<?php echo $cur_forum['fid'] ?>]" size="3" maxlength="3" value="<?php echo $cur_forum['disp_position'] ?>" /></span>
						</div>
					</div>
<?php ($hook = get_hook('afo_pre_edit_cur_forum_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php

		($hook = get_hook('afo_edit_cur_forum_fieldset_end')) ? eval($hook) : null;

		++$i;
	}

?>
			</div>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="update_positions" value="<?php echo $lang_admin_forums['Update positions'] ?>" /></span>
			</div>
		</form>
	</div>
<?php

}

($hook = get_hook('afo_end')) ? eval($hook) : null;
