<?php
namespace punbb;

//
// Generate quickjump cache PHP scripts
//
function generate_quickjump_cache($group_id = false) {
	global $forum_url, $base_url;

	$return = ($hook = get_hook('ch_fn_generate_quickjump_cache_start')) ? eval($hook) : null;
	if ($return != null) {
		return;
	}

	$groups = array();

	// If a group_id was supplied, we generate the quickjump cache for that group only
	if ($group_id !== false)
		$groups[0] = $group_id;
	else
	{
		// A group_id was not supplied, so we generate the quickjump cache for all groups
		$query = array(
			'SELECT'	=> 'g.g_id',
			'FROM'		=> 'groups AS g'
		);

		($hook = get_hook('ch_fn_generate_quickjump_cache_qr_get_groups')) ? eval($hook) : null;
		$result = db()->query_build($query) or error(__FILE__, __LINE__);

		while ($cur_group = db()->fetch_assoc($result))
		{
			$groups[] = $cur_group['g_id'];
		}
	}

	// Loop through the groups in $groups and output the cache for each of them
	foreach ($groups as $group_id)
	{
		$output = '<?php' . "
		namespace punbb;
		" .
		'if (!defined(\'FORUM\')) exit;' .
		"\n" .
		"\n" .
		'$forum_id = isset($forum_id) ? $forum_id : 0;' .
		"\n\n" .
		'?>';
		$output .= '<form id="qjump" method="get" accept-charset="utf-8" action="' .
			$base_url . '/viewforum.php">' .
		"\n\t" .
		'<div class="frm-fld frm-select">' .
		"\n\t\t".
		'<label for="qjump-select"><span><?= __(\'Jump to\') ?>' .
		'</span></label><br />' .
		"\n\t\t" .
		'<span class="frm-input"><select id="qjump-select" name="id">' .
		"\n";

		// Get the list of categories and forums from the DB
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
					'ON'			=> '(fp.forum_id=f.id AND fp.group_id='.$group_id.')'
				)
			),
			'WHERE'		=> 'fp.read_forum IS NULL OR fp.read_forum=1',
			'ORDER BY'	=> 'c.disp_position, c.id, f.disp_position'
		);

		($hook = get_hook('ch_fn_generate_quickjump_cache_qr_get_cats_and_forums')) ? eval($hook) : null;
		$result = db()->query_build($query) or error(__FILE__, __LINE__);

		$forums = array();
		while ($cur_forum = db()->fetch_assoc($result)) {
			$forums[] = $cur_forum;
		}

		$cur_category = 0;
		$forum_count = 0;
		$sef_friendly_names = array();
		foreach ($forums as $cur_forum) {
			($hook = get_hook('ch_fn_generate_quickjump_cache_forum_loop_start')) ? eval($hook) : null;

			if ($cur_forum['cid'] != $cur_category)	{
				// A new category since last iteration?
				if ($cur_category) {
					$output .= "\t\t\t".'</optgroup>'."\n";
				}
				$output .= "\t\t\t".'<optgroup label="'.forum_htmlencode($cur_forum['cat_name']).'">'."\n";
				$cur_category = $cur_forum['cid'];
			}

			$sef_friendly_names[$cur_forum['fid']] = sef_friendly($cur_forum['forum_name']);
			$redirect_tag = ($cur_forum['redirect_url'] != '') ? ' &gt;&gt;&gt;' : '';
			$output .= "\t\t\t\t".'<option value="'.$cur_forum['fid'].'"<?php echo ($forum_id == '.$cur_forum['fid'].') ? \' selected="selected"\' : \'\' ?>>'.forum_htmlencode($cur_forum['forum_name']).$redirect_tag.'</option>'."\n";
			$forum_count++;
		}

		$output .= "\t\t\t".'</optgroup>'."\n\t\t".'</select>'."\n\t\t".
		'<input type="submit" id="qjump-submit" value="<?= __(\'Go\') ?>" /></span>'."\n\t".'</div>'."\n".'</form>'."\n";
		$output_js = "\n".'(function () {'."\n\t".'var forum_quickjump_url = "'.link('forum').'";'."\n\t".'var sef_friendly_url_array = new Array('.count($forums).');';

		foreach ($sef_friendly_names as $forum_id => $forum_name) {
			$output_js .= "\n\t".'sef_friendly_url_array['.$forum_id.'] = "'.forum_htmlencode($forum_name).'";';
		}

		// Add Load Event
		$output_js .= "\n\n\t".'PUNBB.common.addDOMReadyEvent(function () { PUNBB.common.attachQuickjumpRedirect(forum_quickjump_url, sef_friendly_url_array); });'."\n".'}());';

		if ($forum_count < 2) {
			$output = '<?php' .
				"\n\n" .
				'if (!defined(\'FORUM\')) exit;' .
				"\n";
		}
		else {
			$output .= '<?php' .
				"\n\n" .
				'$forum_javascript_quickjump_code = <<<EOL' .
				$output_js .
				"\nEOL;\n\n" .
				'assets()->add_js($forum_javascript_quickjump_code, array(\'type\' => \'inline\', \'weight\' => 60, \'group\' => FORUM_JS_GROUP_SYSTEM));' .
				"\n"."\n";
		}

		$output .= '
			return 1;
		';

		// Output quickjump as PHP code
		if (!cache()->set('cache_quickjump_' . $group_id, $output)) {
			error('Unable to write quickjump cache file to cache directory.<br />
				Please make sure PHP has write access to the directory \'cache\'.',
				__FILE__, __LINE__);
		}
	}
}
