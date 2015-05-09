<?php
/**
 * Allows users to search the forum based on various criteria.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */
namespace punbb;

require __DIR__ . '/vendor/autoload.php';

($hook = get_hook('se_start')) ? eval($hook) : null;

// Load the necessary search functions
require FORUM_ROOT.'include/search_functions.php';


if (user()->g_read_board == '0')
	message(__('No view'));
else if (user()->g_search == '0')
	message(__('No search permission', 'search'));


// If a search_id was supplied
if (isset($_GET['search_id']))
{
	$search_id = intval($_GET['search_id']);
	if ($search_id < 1)
		message(__('Bad request'));

	// Generate the query to grab the cached results
	$query = generate_cached_search_query($search_id, $show_as);

	$url_type = $forum_url['search_results'];
}
// We aren't just grabbing a cached search
else if (isset($_GET['action']))
{
	$action = $_GET['action'];

	// Validate action
	if (!validate_search_action($action))
		message(__('Bad request'));

	// If it's a regular search (keywords and/or author)
	if ($action == 'search')
	{
		$keywords = (isset($_GET['keywords']) && is_string($_GET['keywords'])) ? forum_trim($_GET['keywords']) : null;
		$author = (isset($_GET['author']) && is_string($_GET['author'])) ? forum_trim($_GET['author']) : null;
		$sort_dir = (isset($_GET['sort_dir'])) ? (($_GET['sort_dir'] == 'DESC') ? 'DESC' : 'ASC') : 'DESC';
		$show_as = (isset($_GET['show_as'])) ? $_GET['show_as'] : 'posts';
		$sort_by = (isset($_GET['sort_by'])) ? intval($_GET['sort_by']) : null;
		$search_in = (!isset($_GET['search_in']) || $_GET['search_in'] == 'all') ? 0 : (($_GET['search_in'] == 'message') ? 1 : -1);
		$forum = (isset($_GET['forum']) && is_array($_GET['forum'])) ? array_map('intval', $_GET['forum']) : array(-1);

		if (preg_match('#^[\*%]+$#', $keywords))
			$keywords = '';

		if (preg_match('#^[\*%]+$#', $author))
			$author = '';

		if (!$keywords && !$author)
			message(__('No terms', 'search'));

		// Create a cache of the results and redirect the user to the results
		create_search_cache($keywords, $author, $search_in, $forum, $show_as, $sort_by, $sort_dir);
	}
	// Its not a regular search, so its a quicksearch
	else
	{
		$value = null;
		// Get any additional variables for quicksearches
		if ($action == 'show_user_posts' || $action == 'show_user_topics' || $action == 'show_subscriptions' || $action == 'show_forum_subscriptions')
		{
			$value = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
			if ($value < 2)
				message(__('Bad request'));
		}
		else if ($action == 'show_recent')
			$value = (isset($_GET['value'])) ? intval($_GET['value']) : 86400;
		else if ($action == 'show_new')
			$value = (isset($_GET['forum'])) ? intval($_GET['forum']) : -1;

		($hook = get_hook('se_additional_quicksearch_variables')) ? eval($hook) : null;

		$search_id = '';

		// Show as
		if ($action == 'show_forum_subscriptions')
			$show_as = 'forums';
		else
			$show_as = 'topics';

		// Generate the query for the search
		$query = generate_action_search_query($action, $value, $search_id, $url_type, $show_as);
	}
}

($hook = get_hook('se_pre_search_query')) ? eval($hook) : null;

// We have the query to get the results, lets get them!
if (isset($query))
{
	// No results?
	if (!$query)
		no_search_results();

	// Work out the settings for pagination
	if ($show_as == 'posts')
		$forum_page['per_page'] = user()->disp_posts;
	else if ($show_as == 'topics')
		$forum_page['per_page'] = user()->disp_topics;
	else if ($show_as == 'forums')
		$forum_page['per_page'] = 0;	// Show all

	// We now have a query that will give us our results in $query, lets get the data!
	$num_hits = get_search_results($query, $search_set);

	($hook = get_hook('se_post_results_fetched')) ? eval($hook) : null;

	// No search results?
	if ($num_hits == 0)
		no_search_results($action);

	//
	// Output the search results
	//

	// Setup breadcrumbs and results header and footer
	$crumbs[] = array(config()->o_board_title, link('index'));
	$action = (isset($action)) ? $action : null;
	generate_search_crumbs($action);

	// Generate paging links
	if ($show_as == 'posts' || $show_as == 'topics') {
		$page_post['paging'] = '<p class="paging"><span class="pages">'.
		__('Pages') . '</span> '.paginate($forum_page['num_pages'], $page, $url_type,
			__('Paging separator'), $search_id).'</p>';
	}

	// Get topic/forum tracking data
	if (!user()->is_guest) {
		$tracked_topics = get_tracked_topics();
	}

	// Navigation links for header and page numbering for title/meta description
	if ($show_as == 'posts' || $show_as == 'topics')
	{
		if ($page < $forum_page['num_pages']) {
			$nav['last'] = '<link rel="last" href="'.forum_sublink($url_type, $forum_url['page'], $forum_page['num_pages'], $search_id).'" title="'.
				__('Page').' '.$forum_page['num_pages'].'" />';
			$nav['next'] = '<link rel="next" href="'.forum_sublink($url_type, $forum_url['page'], ($page + 1), $search_id).'" title="'.
				__('Page').' '.($page + 1).'" />';
		}
		if ($page > 1)
		{
			$nav['prev'] = '<link rel="prev" href="'.forum_sublink($url_type, $forum_url['page'], ($page - 1), $search_id).'" title="'.
				__('Page').' '.($page - 1).'" />';
			$nav['first'] = '<link rel="first" href="'.link($url_type, $search_id).'" title="'.
				__('Page').' 1" />';
		}

		// Setup main heading
		if ($forum_page['num_pages'] > 1) {
			$main_head_pages = sprintf(__('Page info'), $page, $forum_page['num_pages']);
		}
		else {
			$main_head_pages = '';
		}
	}

	($hook = get_hook('se_results_pre_header_load')) ? eval($hook) : null;

	// Define page type
	if ($show_as == 'posts') {
		define('FORUM_PAGE', 'searchposts');
	}
	else if ($show_as == 'topics') {
		define('FORUM_PAGE', 'searchtopics');
	}
	else {
		define('FORUM_PAGE', 'searchforums');
	}

	template()->render([
		'main_view' => 'search/search_forums',
		'main_title' => __('Search options', 'search'),
		'main_head_pages' => $main_head_pages,
		'crumbs' => $crumbs,
		'page_post' => $page_post,
		'page' => $page,
		'nav' => $nav
	]);
}

//
// Display the search form
//

// Setup form information
$forum_page['frm-info'] = array(
	'keywords'	=> '<li><span>' . __('Keywords info', 'search') . '</span></li>',
	'refine'	=> '<li><span>' . __('Refine info', 'search') . '</span></li>',
	'wildcard'	=> '<li><span>' . __('Wildcard info', 'search') . '</span></li>'
);

if (config()->o_search_all_forums == '1' || user()->is_admmod)
	$forum_page['frm-info']['forums'] = '<li><span>' . __('Forum default info', 'search') . '</span></li>';
else
	$forum_page['frm-info']['forums'] = '<li><span>' . __('Forum require info', 'search') . '</span></li>';

// Setup sort by options
$forum_page['frm-sort'] = array(
	'post_time'		=> '<option value="0">' . __('Sort by post time', 'search') . '</option>',
	'author'		=> '<option value="1">' . __('Sort by author', 'search') . '</option>',
	'subject'		=> '<option value="2">' . __('Sort by subject', 'search') . '</option>',
	'forum_name'	=> '<option value="3">' . __('Sort by forum', 'search') . '</option>'
);

// Setup breadcrumbs
$crumbs = array(
	array(config()->o_board_title, link('index')),
	__('Search')
);

$advanced_search = isset($_GET['advanced']) ? true : false;

// Show link for advanced form
if (!$advanced_search)
{
	$main_head_options['advanced_search'] = '<span'.(empty($main_head_options)? ' class="first-item"' : '').'><a href="'.link('search_advanced').'">'.
		__('Advanced search', 'search') . '</a></span>';
}

// Setup form
$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;

($hook = get_hook('se_pre_header_load')) ? eval($hook) : null;

define('FORUM_PAGE', 'search');

if ((!$advanced_search && (config()->o_search_all_forums == '0' &&
	!user()->is_admmod)) || $advanced_search) {

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
				'ON'			=> '(fp.forum_id=f.id AND fp.group_id='.user()->g_id.')'
			)
		),
		'WHERE'		=> '(fp.read_forum IS NULL OR fp.read_forum=1) AND f.redirect_url IS NULL',
		'ORDER BY'	=> 'c.disp_position, c.id, f.disp_position'
	);

	($hook = get_hook('se_qr_get_cats_and_forums')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);

	$forums = array();
	while ($cur_forum = db()->fetch_assoc($result))
	{
		$forums[] = $cur_forum;
	}
}

template()->render([
	'main_view' => 'search/main',
	'crumbs' => $crumbs,
	'page' => $page,
	'main_head_options' => $main_head_options
]);
