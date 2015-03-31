<?php

$admod_links = array();

// We only need to run this query for mods/admins if there will actually be reports to look at
if ($forum_user['is_admmod'] && $forum_config['o_report_method'] != 1)
{
	$query = array(
		'SELECT'	=> 'COUNT(r.id)',
		'FROM'		=> 'reports AS r',
		'WHERE'		=> 'r.zapped IS NULL',
	);

	($hook = get_hook('hd_qr_get_unread_reports_count')) ? eval($hook) : null;
	$result_header = $forum_db->query_build($query) or error(__FILE__, __LINE__);

	if ($forum_db->result($result_header))
		$admod_links['reports'] = '<li id="reports"><a href="'.forum_link($forum_url['admin_reports']).'">'.$lang_common['New reports'].'</a></li>';
}

if ($forum_user['g_id'] == FORUM_ADMIN)
{
	$alert_items = array();

	// Warn the admin that maintenance mode is enabled
	if ($forum_config['o_maintenance'] == '1')
		$alert_items['maintenance'] = '<p id="maint-alert" class="warn">'.$lang_common['Maintenance alert'].'</p>';

	if ($forum_config['o_check_for_updates'] == '1')
	{
		if ($forum_updates['fail'])
			$alert_items['update_fail'] = '<p><strong>'.$lang_common['Updates'].'</strong> '.$lang_common['Updates failed'].'</p>';
		else if (isset($forum_updates['version']) && isset($forum_updates['hotfix']))
			$alert_items['update_version_hotfix'] = '<p><strong>'.$lang_common['Updates'].'</strong> '.sprintf($lang_common['Updates version n hf'], $forum_updates['version'], forum_link($forum_url['admin_extensions_hotfixes'])).'</p>';
		else if (isset($forum_updates['version']))
			$alert_items['update_version'] = '<p><strong>'.$lang_common['Updates'].'</strong> '.sprintf($lang_common['Updates version'], $forum_updates['version']).'</p>';
		else if (isset($forum_updates['hotfix']))
			$alert_items['update_hotfix'] = '<p><strong>'.$lang_common['Updates'].'</strong> '.sprintf($lang_common['Updates hf'], forum_link($forum_url['admin_extensions_hotfixes'])).'</p>';
	}

	// Warn the admin that their version of the database is newer than the version supported by the code
	// NOTE: Why is it done on any page, but shown in admin section only.
	if ($forum_config['o_database_revision'] > FORUM_DB_REVISION)
		$alert_items['newer_database'] = '<p><strong>'.$lang_common['Database mismatch'].'</strong> '.$lang_common['Database mismatch alert'].'</p>';

	if (!empty($alert_items))
		$admod_links['alert'] = '<li id="alert"><a href="'.forum_link($forum_url['admin_index']).'">'.$lang_common['New alerts'].'</a></li>';

	($hook = get_hook('hd_alert')) ? eval($hook) : null;
}

echo (!empty($admod_links)) ?
	('<ul id="brd-admod">'.implode(' ', $admod_links).'</ul>') : '';
