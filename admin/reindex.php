<?php
/**
 * Search index rebuilding script.
 *
 * Allows administrators to rebuild the index used to search the posts and topics.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */
namespace punbb;

// Tell common.php that we don't want output buffering
define('FORUM_DISABLE_BUFFERING', 1);

require __DIR__ . '/../vendor/pautoload.php';

($hook = get_hook('ari_start')) ? eval($hook) : null;

if (user()->g_id != FORUM_ADMIN) {
	message(__('No permission'));
}

if (isset($_GET['i_per_page']) && isset($_GET['i_start_at'])) {
	$per_page = intval($_GET['i_per_page']);
	$start_at = intval($_GET['i_start_at']);
	if ($per_page < 1 || $start_at < 1)
		message(__('Bad request'));

	// We validate the CSRF token. If it's set in POST and we're at this point, the token is valid.
	// If it's in GET, we need to make sure it's valid.
	if (!isset($_POST['csrf_token']) && (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== generate_form_token('reindex'.user()->id)))
		csrf_confirm_form();

	($hook = get_hook('ari_cycle_start')) ? eval($hook) : null;

	@set_time_limit(0);

	// If this is the first cycle of posts we empty the search index before we proceed
	if (isset($_GET['i_empty_index']))
	{
		$query = array(
			'DELETE'	=> 'search_matches'
		);

		($hook = get_hook('ari_cycle_qr_empty_search_matches')) ? eval($hook) : null;
		db()->query_build($query) or error(__FILE__, __LINE__);

		$query = array(
			'DELETE'	=> 'search_words'
		);

		($hook = get_hook('ari_cycle_qr_empty_search_words')) ? eval($hook) : null;
		db()->query_build($query) or error(__FILE__, __LINE__);

		// Reset the sequence for the search words (not needed for SQLite)
		switch ($db_type)
		{
			case 'mysql':
			case 'mysql_innodb':
			case 'mysqli':
			case 'mysqli_innodb':
				$result = db()->query('ALTER TABLE '.db()->prefix.'search_words auto_increment=1') or error(__FILE__, __LINE__);
				break;

			case 'pgsql';
				$result = db()->query('SELECT setval(\''.db()->prefix.'search_words_id_seq\', 1, false)') or error(__FILE__, __LINE__);
		}
	}

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array(config()->o_board_title, forum_link($forum_url['index'])),
		array(__('Forum administration', 'admin_common'), forum_link($forum_url['admin_index'])),
		array(__('Management', 'admin_common'), forum_link($forum_url['admin_reports'])),
		__('Rebuilding index title', 'admin_reindex')
	);

?>
<!DOCTYPE html>
<html lang="<?= __('lang_identifier') ?>" dir="<?= __('lang_direction') ?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo generate_crumbs(true) ?></title>
<style type="text/css">
body {
	font: 68.75% Verdana, Arial, Helvetica, sans-serif;
	color: #333333;
	background-color: #FFFFFF
}
</style>
</head>
<body>
<p><?php echo __('Rebuilding index', 'admin_reindex') ?></p>

<?php

	if (!defined('FORUM_SEARCH_IDX_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/search_idx.php';

	// Fetch posts to process
	$query = array(
		'SELECT'	=> 'p.id, p.message, t.id, t.subject, t.first_post_id',
		'FROM'		=> 'posts AS p',
		'JOINS'		=> array(
			array(
				'INNER JOIN'	=> 'topics AS t',
				'ON'			=> 't.id=p.topic_id'
			)
		),
		'WHERE'		=> 'p.id >= '.$start_at,
		'ORDER BY'	=> 'p.id',
		'LIMIT'		=> $per_page
	);

	($hook = get_hook('ari_cycle_qr_fetch_posts')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);

	$post_id = 0;
	echo '<p>';
	while ($cur_post = db()->fetch_row($result))
	{
		echo sprintf(__('Processing post', 'admin_reindex'), $cur_post[0], $cur_post[2]).'<br />'."\n";

		if ($cur_post[0] == $cur_post[4])	// This is the "topic post" so we have to index the subject as well
			update_search_index('post', $cur_post[0], $cur_post[1], $cur_post[3]);
		else
			update_search_index('post', $cur_post[0], $cur_post[1]);

		$post_id = $cur_post[0];
	}
	echo '</p>';

	// Check if there is more work to do
	$query = array(
		'SELECT'	=> 'p.id',
		'FROM'		=> 'posts AS p',
		'WHERE'		=> 'p.id > '.$post_id,
		'ORDER BY'	=> 'p.id',
		'LIMIT'		=> '1'
	);

	($hook = get_hook('ari_cycle_qr_find_next_post')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);
	$next_posts_to_proced = db()->result($result);

	$query_str = '';
	if (!is_null($next_posts_to_proced) && $next_posts_to_proced !== false)
	{
		$query_str = '?i_per_page='.$per_page.'&i_start_at='.$next_posts_to_proced.'&csrf_token='.generate_form_token('reindex'.user()->id);
	}

	($hook = get_hook('ari_cycle_end')) ? eval($hook) : null;

	db()->end_transaction();
	db()->close();

	exit('<script type="text/javascript">window.location="'.forum_link($forum_url['admin_reindex']).$query_str.'"</script><br />'.
		__('Javascript redirect', 'admin_reindex') . ' <a href="'.forum_link($forum_url['admin_reindex']).$query_str.'">'.
		__('Click to continue', 'admin_reindex') . '</a>.');
}


// Get the first post ID from the db
$query = array(
	'SELECT'	=> 'p.id',
	'FROM'		=> 'posts AS p',
	'ORDER BY'	=> 'p.id',
	'LIMIT'		=> '1'
);

($hook = get_hook('ari_qr_find_lowest_post_id')) ? eval($hook) : null;
$result = db()->query_build($query) or error(__FILE__, __LINE__);
$first_id = db()->result($result);

if (is_null($first_id) || $first_id === false)
{
	unset($first_id);
}

// Setup form
$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;

// Setup breadcrumbs
$forum_page['crumbs'] = array(
	array(config()->o_board_title, forum_link($forum_url['index'])),
	array(__('Forum administration', 'admin_common'), forum_link($forum_url['admin_index'])),
	array(__('Management', 'admin_common'), forum_link($forum_url['admin_reports'])),
	array(__('Rebuild index', 'admin_common'), forum_link($forum_url['admin_reindex']))
);

($hook = get_hook('ari_pre_header_load')) ? eval($hook) : null;

define('FORUM_PAGE_SECTION', 'management');
define('FORUM_PAGE', 'admin-reindex');

$forum_main_view = 'admin/reindex/main';
include FORUM_ROOT . 'include/render.php';
