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


if (!defined('FORUM_ROOT'))
	define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';
require FORUM_ROOT.'include/common_admin.php';

($hook = get_hook('arp_start')) ? eval($hook) : null;

if (!$forum_user['is_admmod'])
	message($lang_common['No permission']);

// Load the admin.php language file
require FORUM_ROOT.'lang/'.$forum_user['language'].'/admin_common.php';
require FORUM_ROOT.'lang/'.$forum_user['language'].'/admin_reports.php';


// Mark reports as read
if (isset($_POST['mark_as_read']))
{
	if (empty($_POST['reports']))
		message($lang_admin_reports['No reports selected']);

	($hook = get_hook('arp_mark_as_read_form_submitted')) ? eval($hook) : null;

	$reports_to_mark = array_map('intval', array_keys($_POST['reports']));

	$query = array(
		'UPDATE'	=> 'reports',
		'SET'		=> 'zapped='.time().', zapped_by='.$forum_user['id'],
		'WHERE'		=> 'id IN('.implode(',', $reports_to_mark).') AND zapped IS NULL'
	);

	($hook = get_hook('arp_mark_as_read_qr_mark_reports_as_read')) ? eval($hook) : null;
	$forum_db->query_build($query) or error(__FILE__, __LINE__);

	// Add flash message
	$forum_flash->add_info($lang_admin_reports['Reports marked read']);

	($hook = get_hook('arp_mark_as_read_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['admin_reports']), $lang_admin_reports['Reports marked read']);
}

$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;

// Setup breadcrumbs
$forum_page['crumbs'] = array(
	array($forum_config['o_board_title'], forum_link($forum_url['index'])),
	array($lang_admin_common['Forum administration'], forum_link($forum_url['admin_index']))
);
if ($forum_user['g_id'] == FORUM_ADMIN)
	$forum_page['crumbs'][] = array($lang_admin_common['Management'], forum_link($forum_url['admin_reports']));
$forum_page['crumbs'][] = array($lang_admin_common['Reports'], forum_link($forum_url['admin_reports']));

($hook = get_hook('arp_pre_header_load')) ? eval($hook) : null;

define('FORUM_PAGE_SECTION', 'management');
define('FORUM_PAGE', 'admin-reports');
require FORUM_ROOT.'header.php';

// START SUBST - <!-- forum_main -->
ob_start();
include FORUM_ROOT . 'include/view/admin/reports/main.php';
$view_forum_main = forum_trim(ob_get_contents());
$tpl_main = str_replace('<!-- forum_main -->', $view_forum_main, $tpl_main);
ob_end_clean();
// END SUBST - <!-- forum_main -->

require FORUM_ROOT.'footer.php';
