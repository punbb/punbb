<?php
/**
 * Report management page.
 *
 * Allows administrators and moderators to handle reported posts.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */
namespace punbb;

require __DIR__ . '/../vendor/pautoload.php';

($hook = get_hook('arp_start')) ? eval($hook) : null;

if (!user()->is_admmod) {
	message(__('No permission'));
}

// Mark reports as read
if (isset($_POST['mark_as_read']))
{
	if (empty($_POST['reports']))
		message(__('No reports selected', 'admin_reports'));

	($hook = get_hook('arp_mark_as_read_form_submitted')) ? eval($hook) : null;

	$reports_to_mark = array_map('intval', array_keys($_POST['reports']));

	$query = array(
		'UPDATE'	=> 'reports',
		'SET'		=> 'zapped='.time().', zapped_by='.user()->id,
		'WHERE'		=> 'id IN('.implode(',', $reports_to_mark).') AND zapped IS NULL'
	);

	($hook = get_hook('arp_mark_as_read_qr_mark_reports_as_read')) ? eval($hook) : null;
	db()->query_build($query) or error(__FILE__, __LINE__);

	// Add flash message
	flash()->add_info(__('Reports marked read', 'admin_reports'));

	($hook = get_hook('arp_mark_as_read_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['admin_reports']), __('Reports marked read', 'admin_reports'));
}

$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;

// Setup breadcrumbs
$forum_page['crumbs'] = array(
	array(config()->o_board_title, forum_link($forum_url['index'])),
	array(__('Forum administration', 'admin_common'), forum_link($forum_url['admin_index']))
);
if (user()->g_id == FORUM_ADMIN) {
	$forum_page['crumbs'][] = array(__('Management', 'admin_common'), forum_link($forum_url['admin_reports']));
}
$forum_page['crumbs'][] = array(__('Reports', 'admin_common'), forum_link($forum_url['admin_reports']));

($hook = get_hook('arp_pre_header_load')) ? eval($hook) : null;

define('FORUM_PAGE_SECTION', 'management');
define('FORUM_PAGE', 'admin-reports');

// Fetch any unread reports
$query = array(
	'SELECT'	=> 'r.id, r.topic_id, r.forum_id, r.reported_by, r.created, r.message, p.id AS pid, t.subject, f.forum_name, u.username AS reporter',
	'FROM'		=> 'reports AS r',
	'JOINS'		=> array(
		array(
			'LEFT JOIN'		=> 'posts AS p',
			'ON'			=> 'r.post_id=p.id'
		),
		array(
			'LEFT JOIN'		=> 'topics AS t',
			'ON'			=> 'r.topic_id=t.id'
		),
		array(
			'LEFT JOIN'		=> 'forums AS f',
			'ON'			=> 'r.forum_id=f.id'
		),
		array(
			'LEFT JOIN'		=> 'users AS u',
			'ON'			=> 'r.reported_by=u.id'
		)
	),
	'WHERE'		=> 'r.zapped IS NULL',
	'ORDER BY'	=> 'r.created DESC'
);
($hook = get_hook('arp_qr_get_new_reports')) ? eval($hook) : null;

$forum_page['new_reports'] = false;
$result = db()->query_build($query) or error(__FILE__, __LINE__);
$unread_reports = array();
while ($cur_report = db()->fetch_assoc($result)) {
	$unread_reports[] = $cur_report;
}

// Fetch the last 10 reports marked as read
$query = array(
	'SELECT'	=> 'r.id, r.topic_id, r.forum_id, r.reported_by, r.created, r.message, r.zapped, r.zapped_by AS zapped_by_id, p.id AS pid, t.subject, f.forum_name, u.username AS reporter, u2.username AS zapped_by',
	'FROM'		=> 'reports AS r',
	'JOINS'		=> array(
		array(
			'LEFT JOIN'		=> 'posts AS p',
			'ON'			=> 'r.post_id=p.id'
		),
		array(
			'LEFT JOIN'		=> 'topics AS t',
			'ON'			=> 'r.topic_id=t.id'
		),
		array(
			'LEFT JOIN'		=> 'forums AS f',
			'ON'			=> 'r.forum_id=f.id'
		),
		array(
			'LEFT JOIN'		=> 'users AS u',
			'ON'			=> 'r.reported_by=u.id'
		),
		array(
			'LEFT JOIN'		=> 'users AS u2',
			'ON'			=> 'r.zapped_by=u2.id'
		)
	),
	'WHERE'		=> 'r.zapped IS NOT NULL',
	'ORDER BY'	=> 'r.zapped DESC',
	'LIMIT'		=> '10'
);
($hook = get_hook('arp_qr_get_last_zapped_reports')) ? eval($hook) : null;

$forum_page['old_reports'] = false;
$result = db()->query_build($query) or error(__FILE__, __LINE__);
$zapped_reports = array();
while ($cur_report = db()->fetch_assoc($result)) {
	$zapped_reports[] = $cur_report;
}

template()->render([
	'main_view' => 'admin/reports/main'
]);
