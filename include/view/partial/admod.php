<?php
namespace punbb;

$admod_links = array();

// We only need to run this query for mods/admins if there will actually be reports to look at
if (user()->is_admmod && config()->o_report_method != 1) {
	$query = array(
		'SELECT'	=> 'COUNT(r.id)',
		'FROM'		=> 'reports AS r',
		'WHERE'		=> 'r.zapped IS NULL',
	);

	($hook = get_hook('hd_qr_get_unread_reports_count')) ? eval($hook) : null;
	$result_header = db()->query_build($query) or error(__FILE__, __LINE__);

	if (db()->result($result_header))
		$admod_links['reports'] = '<li id="reports"><a href="'.forum_link($forum_url['admin_reports']).'">'.
		__('New reports') . '</a></li>';
}

if (user()->g_id == FORUM_ADMIN) {
	$alert_items = array();

	// Warn the admin that maintenance mode is enabled
	if (config()->o_maintenance == '1')
		$alert_items['maintenance'] = '<p id="maint-alert" class="warn">'.
		__('Maintenance alert') . '</p>';

	if (config()->o_check_for_updates == '1')
	{
		if ($forum_updates['fail'])
			$alert_items['update_fail'] = '<p><strong>'.
				__('Updates') . '</strong> ' . __('Updates failed') . '</p>';
		else if (isset($forum_updates['version']) && isset($forum_updates['hotfix']))
			$alert_items['update_version_hotfix'] = '<p><strong>'.
			__('Updates') . '</strong> '.sprintf(__('Updates version n hf'), $forum_updates['version'], forum_link($forum_url['admin_extensions_hotfixes'])).'</p>';
		else if (isset($forum_updates['version']))
			$alert_items['update_version'] = '<p><strong>'.
			__('Updates') . '</strong> '.sprintf(__('Updates version'), $forum_updates['version']).'</p>';
		else if (isset($forum_updates['hotfix']))
			$alert_items['update_hotfix'] = '<p><strong>'.
			__('Updates') . '</strong> '.sprintf(__('Updates hf'), forum_link($forum_url['admin_extensions_hotfixes'])).'</p>';
	}

	// Warn the admin that their version of the database is newer than the version supported by the code
	// NOTE: Why is it done on any page, but shown in admin section only.
	if (config()->o_database_revision > FORUM_DB_REVISION)
		$alert_items['newer_database'] = '<p><strong>'.
		__('Database mismatch') . '</strong> ' . __('Database mismatch alert') . '</p>';

	if (!empty($alert_items))
		$admod_links['alert'] =
			'<li id="alert"><a href="'.forum_link($forum_url['admin_index']).'">'.
			__('New alerts').'</a></li>';

	($hook = get_hook('hd_alert')) ? eval($hook) : null;
}

echo (!empty($admod_links)) ?
	('<ul id="brd-admod">'.implode(' ', $admod_links).'</ul>') : '';
