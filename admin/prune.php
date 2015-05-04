<?php
/**
 * Topic pruning page.
 *
 * Allows administrators to delete older topics from the site.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */
namespace punbb;

require __DIR__ . '/../vendor/autoload.php';

($hook = get_hook('apr_start')) ? eval($hook) : null;

if (user()->g_id != FORUM_ADMIN) {
	message(__('No permission'));
}

if (isset($_GET['action']) || isset($_POST['prune']) || isset($_POST['prune_comply']))
{
	if (isset($_POST['prune_comply']))
	{
		$prune_from = $_POST['prune_from'];
		$prune_days = intval($_POST['prune_days']);
		$prune_date = ($prune_days) ? time() - ($prune_days*86400) : -1;

		($hook = get_hook('apr_prune_comply_form_submitted')) ? eval($hook) : null;

		@set_time_limit(0);

		if ($prune_from == 'all')
		{
			$query = array(
				'SELECT'	=> 'f.id',
				'FROM'		=> 'forums AS f'
			);

			($hook = get_hook('apr_prune_comply_qr_get_all_forums')) ? eval($hook) : null;
			$result = db()->query_build($query) or error(__FILE__, __LINE__);

			while ($cur_forum = db()->fetch_assoc($result)) {
				prune($cur_forum['id'], $_POST['prune_sticky'], $prune_date);
				sync_forum($cur_forum['id']);
			}
		}
		else
		{
			$prune_from = intval($prune_from);
			prune($prune_from, $_POST['prune_sticky'], $prune_date);
			sync_forum($prune_from);
		}

		delete_orphans();

		flash()->add_info(__('Prune done', 'admin_prune'));

		($hook = get_hook('apr_prune_pre_redirect')) ? eval($hook) : null;

		redirect(link('admin_prune'), __('Prune done', 'admin_prune'));
	}


	$prune_days = intval($_POST['req_prune_days']);
	if ($prune_days < 0)
		message(__('Days to prune message', 'admin_prune'));

	$prune_date = time() - ($prune_days * 86400);
	$prune_from = $_POST['prune_from'];

	if ($prune_from != 'all')
	{
		$prune_from = intval($prune_from);

		// Fetch the forum name (just for cosmetic reasons)
		$query = array(
			'SELECT'	=> 'f.forum_name',
			'FROM'		=> 'forums AS f',
			'WHERE'		=> 'f.id='.$prune_from
		);

		($hook = get_hook('apr_prune_comply_qr_get_forum_name')) ? eval($hook) : null;
		$result = db()->query_build($query) or error(__FILE__, __LINE__);
		$forum = forum_htmlencode(db()->result($result));
	}
	else
		$forum = 'all forums';

	// Count the number of topics to prune
	$query = array(
		'SELECT'	=> 'COUNT(t.id)',
		'FROM'		=> 'topics AS t',
		'WHERE'		=> 't.last_post<'.$prune_date.' AND t.moved_to IS NULL'
	);

	if ($prune_from != 'all')
		$query['WHERE'] .= ' AND t.forum_id='.$prune_from;
	if (!isset($_POST['prune_sticky']))
		$query['WHERE'] .= ' AND t.sticky=0';

	($hook = get_hook('apr_prune_comply_qr_get_topic_count')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);
	$num_topics = db()->result($result);

	if (!$num_topics)
		message(__('No days old message', 'admin_prune'));


	// Setup breadcrumbs
	$crumbs = array(
		array(config()->o_board_title, link('index')),
		array(__('Forum administration', 'admin_common'), link('admin_index')),
		array(__('Management', 'admin_common'), link('admin_reports')),
		array(__('Prune topics', 'admin_prune'), link('admin_prune')),
		__('Confirm prune heading', 'admin_prune')
	);

	($hook = get_hook('apr_prune_comply_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE_SECTION', 'management');
	define('FORUM_PAGE', 'admin-prune');

	template()->render([
		'main_view' => 'admin/prune/comply',
		'crumbs' => $crumbs
	]);
}
else {
	// Setup form
	$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;

	// Setup breadcrumbs
	$crumbs = array(
		array(config()->o_board_title, link('index')),
		array(__('Forum administration', 'admin_common'), link('admin_index')),
		array(__('Management', 'admin_common'), link('admin_reports')),
		array(__('Prune topics', 'admin_common'), link('admin_prune'))
	);

	($hook = get_hook('apr_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE_SECTION', 'management');
	define('FORUM_PAGE', 'admin-prune');

	$query = array(
		'SELECT'	=> 'c.id AS cid, c.cat_name, f.id AS fid, f.forum_name',
		'FROM'		=> 'categories AS c',
		'JOINS'		=> array(
			array(
				'INNER JOIN'	=> 'forums AS f',
				'ON'			=> 'c.id=f.cat_id'
			)
		),
		'WHERE'		=> 'f.redirect_url IS NULL',
		'ORDER BY'	=> 'c.disp_position, c.id, f.disp_position'
	);
	($hook = get_hook('apr_qr_get_forum_list')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);

	template()->render([
		'main_view' => 'admin/prune/main',
		'crumbs' => $crumbs
	]);
}
