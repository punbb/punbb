<?php
/**
 * Provides a list of forum users that can be sorted based on various criteria.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */
namespace punbb;

require __DIR__ . '/vendor/autoload.php';

($hook = get_hook('ul_start')) ? eval($hook) : null;

if (user()->g_read_board == '0') {
	message(__('No view'));
}
else if (user()->g_view_users == '0') {
	message(__('No permission'));
}

// Miscellaneous setup
$forum_page['show_post_count'] = (config()->o_show_post_count == '1' || user()->is_admmod) ? true : false;

$forum_page['username'] = '';
if (isset($_GET['username']) && is_string($_GET['username'])) {
	if ($_GET['username'] != '-' && user()->g_search_users == '1') {
		$forum_page['username'] = $_GET['username'];
	}
}

$forum_page['show_group'] = (!isset($_GET['show_group']) || intval($_GET['show_group']) < -1 && intval($_GET['show_group']) > 2) ? -1 : intval($_GET['show_group']);
$forum_page['sort_by'] = (!isset($_GET['sort_by']) || $_GET['sort_by'] != 'username' && $_GET['sort_by'] != 'registered' && ($_GET['sort_by'] != 'num_posts' || !$forum_page['show_post_count'])) ? 'username' : $_GET['sort_by'];
$forum_page['sort_dir'] = (!isset($_GET['sort_dir']) || strtoupper($_GET['sort_dir']) != 'ASC' && strtoupper($_GET['sort_dir']) != 'DESC') ? 'ASC' : strtoupper($_GET['sort_dir']);


// Create any SQL for the WHERE clause
$where_sql = array();
$like_command = ($db_type == 'pgsql') ? 'ILIKE' : 'LIKE';

if (user()->g_search_users == '1' && $forum_page['username'] != '')
	$where_sql[] = 'u.username '.$like_command.' \''.db()->escape(str_replace('*', '%', $forum_page['username'])).'\'';
if ($forum_page['show_group'] > -1)
	$where_sql[] = 'u.group_id='.$forum_page['show_group'];


// Fetch user count
$query = array(
	'SELECT'	=> 'COUNT(u.id)',
	'FROM'		=> 'users AS u',
	'WHERE'		=> 'u.id > 1 AND u.group_id != '.FORUM_UNVERIFIED
);

if (!empty($where_sql)) {
	$query['WHERE'] .= ' AND '.implode(' AND ', $where_sql);
}

($hook = get_hook('ul_qr_get_user_count')) ? eval($hook) : null;
$result = db()->query_build($query) or error(__FILE__, __LINE__);
$forum_page['num_users'] = db()->result($result);

// Determine the user offset (based on $_GET['p'])
$forum_page['num_pages'] = ceil($forum_page['num_users'] / 50);
$page = (!isset($_GET['p']) || !is_numeric($_GET['p']) || $_GET['p'] <= 1 || $_GET['p'] > $forum_page['num_pages']) ? 1 : intval($_GET['p']);
$forum_page['start_from'] = 50 * ($page - 1);
$forum_page['finish_at'] = min(($forum_page['start_from'] + 50), ($forum_page['num_users']));

$forum_page['users_searched'] = ((user()->g_search_users == '1' && $forum_page['username'] != '') || $forum_page['show_group'] > -1);

if ($forum_page['num_users'] > 0)
	$forum_page['items_info'] = generate_items_info((($forum_page['users_searched']) ?
		__('Users found', 'userlist') : __('Users', 'userlist')), ($forum_page['start_from'] + 1), $forum_page['num_users']);
else
	$forum_page['items_info'] = __('Users', 'userlist');

// Generate paging links
$page_post['paging'] = '<p class="paging"><span class="pages">'.
	__('Pages').'</span> '.paginate($forum_page['num_pages'], $page, $forum_url['users_browse'],
		__('Paging separator'), array($forum_page['show_group'], $forum_page['sort_by'], $forum_page['sort_dir'], ($forum_page['username'] != '') ? urlencode($forum_page['username']) : '-')).'</p>';

// Navigation links for header and page numbering for title/meta description
if ($page < $forum_page['num_pages']) {
	$nav['last'] = '<link rel="last" href="'.forum_sublink('users_browse', $forum_url['page'], $forum_page['num_pages'], array($forum_page['show_group'], $forum_page['sort_by'], $forum_page['sort_dir'], ($forum_page['username'] != '') ? urlencode($forum_page['username']) : '-')).'" title="'.
		__('Page').' '.$forum_page['num_pages'].'" />';
	$nav['next'] = '<link rel="next" href="'.forum_sublink('users_browse', $forum_url['page'], ($page + 1), array($forum_page['show_group'], $forum_page['sort_by'], $forum_page['sort_dir'], ($forum_page['username'] != '') ? urlencode($forum_page['username']) : '-')).'" title="'.
		__('Page').' '.($page + 1).'" />';
}
if ($page > 1) {
	$nav['prev'] = '<link rel="prev" href="'.forum_sublink('users_browse', $forum_url['page'], ($page - 1), array($forum_page['show_group'], $forum_page['sort_by'], $forum_page['sort_dir'], ($forum_page['username'] != '') ? urlencode($forum_page['username']) : '-')).'" title="'.
		__('Page').' '.($page - 1).'" />';
	$nav['first'] = '<link rel="first" href="'.link('users_browse', array($forum_page['show_group'], $forum_page['sort_by'], $forum_page['sort_dir'], ($forum_page['username'] != '') ? urlencode($forum_page['username']) : '-')).'" title="'.
		__('Page').' 1" />';
}

// Setup main options
if (empty($_GET))
	$forum_page['main_head_options'] = array();
else
	$forum_page['main_head_options'] = array(
		'new_search'	=> '<span'.(empty($forum_page['main_foot_options']) ? ' class="first-item"' : '').'><a href="'.link('users').'">'.
			__('Perform new search', 'userlist') . '</a></span>'
	);

// Setup form
$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;
$form_action = app()->base_url . '/userlist.php';

// Setup breadcrumbs
$crumbs = array(
	array(config()->o_board_title, link('index')),
	__('User list')
);

if ($forum_page['num_pages'] > 1) {
	$main_head_pages = sprintf(__('Page info'), $page, $forum_page['num_pages']);
}
else {
	$main_head_pages = '';
}

($hook = get_hook('ul_pre_header_load')) ? eval($hook) : null;

define('FORUM_ALLOW_INDEX', 1);

define('FORUM_PAGE', 'userlist');

// Get the list of user groups (excluding the guest group)
$query = array(
	'SELECT'	=> 'g.g_id, g.g_title',
	'FROM'		=> 'groups AS g',
	'WHERE'		=> 'g.g_id!='.FORUM_GUEST,
	'ORDER BY'	=> 'g.g_id'
);

($hook = get_hook('ul_qr_get_groups')) ? eval($hook) : null;
$result_group = db()->query_build($query) or error(__FILE__, __LINE__);

// Grab the users
$query = array(
	'SELECT'	=> 'u.id, u.username, u.title, u.num_posts, u.registered, g.g_id, g.g_user_title',
	'FROM'		=> 'users AS u',
	'JOINS'		=> array(
		array(
			'LEFT JOIN'		=> 'groups AS g',
			'ON'			=> 'g.g_id=u.group_id'
		)
	),
	'WHERE'		=> 'u.id > 1 AND u.group_id != '.FORUM_UNVERIFIED,
	'ORDER BY'	=> $forum_page['sort_by'].' '.$forum_page['sort_dir'].', u.id ASC',
	'LIMIT'		=> $forum_page['start_from'].', 50'
);

if (!empty($where_sql))
	$query['WHERE'] .= ' AND '.implode(' AND ', $where_sql);

($hook = get_hook('ul_qr_get_users')) ? eval($hook) : null;
$result = db()->query_build($query) or error(__FILE__, __LINE__);

template()->render([
	'main_view' => 'userlist/main',
	'main_head_pages' => $main_head_pages,
	'crumbs' => $crumbs,
	'page_post' => $page_post,
	'page' => $page,
	'form_action' => $form_action,
	'nav' => $nav
]);
