<?php
namespace punbb;

($hook = get_hook('in_info_output_start')) ? eval($hook) : null;


if (file_exists(FORUM_CACHE_DIR.'cache_stats.php'))
	include FORUM_CACHE_DIR.'cache_stats.php';

// Regenerate cache only if the cache is more than 30 minutes old
if (!defined('FORUM_STATS_LOADED') || $forum_stats['cached'] < (time() - 1800)) {
	require FORUM_ROOT . 'include/cache.php';
	generate_stats_cache();
	require FORUM_CACHE_DIR . 'cache_stats.php';
}

$stats_list['no_of_users'] =
	'<li class="st-users"><span>'.sprintf(__('No of users', 'index'), '<strong>'.forum_number_format($forum_stats['total_users']).'</strong>').'</span></li>';
$stats_list['newest_user'] =
	'<li class="st-users"><span>'.sprintf(__('Newest user', 'index'), '<strong>'.(user()['g_view_users'] == '1' ? '<a href="'.forum_link($forum_url['user'], $forum_stats['last_user']['id']).'">'.forum_htmlencode($forum_stats['last_user']['username']).'</a>' : forum_htmlencode($forum_stats['last_user']['username'])).'</strong>').'</span></li>';
$stats_list['no_of_topics'] =
	'<li class="st-activity"><span>'.sprintf(__('No of topics', 'index'), '<strong>'.forum_number_format($forum_stats['total_topics']).'</strong>').'</span></li>';
$stats_list['no_of_posts'] =
	'<li class="st-activity"><span>'.sprintf(__('No of posts', 'index'), '<strong>'.forum_number_format($forum_stats['total_posts']).'</strong>').'</span></li>';

($hook = get_hook('in_stats_pre_info_output')) ? eval($hook) : null;

include view('index/stat');

($hook = get_hook('in_stats_end')) ? eval($hook) : null;
($hook = get_hook('in_users_online_start')) ? eval($hook) : null;

if (config()->o_users_online == '1')
{
	// Fetch users online info and generate strings for output
	$query = array(
		'SELECT'	=> 'o.user_id, o.ident',
		'FROM'		=> 'online AS o',
		'WHERE'		=> 'o.idle=0',
		'ORDER BY'	=> 'o.ident'
	);

	($hook = get_hook('in_users_online_qr_get_online_info')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);
	$forum_page['num_guests'] = $forum_page['num_users'] = 0;
	$users = array();

	while ($forum_user_online = db()->fetch_assoc($result))
	{
		($hook = get_hook('in_users_online_add_online_user_loop')) ? eval($hook) : null;

		if ($forum_user_online['user_id'] > 1)
		{
			$users[] = ($forum_user['g_view_users'] == '1') ? '<a href="'.forum_link($forum_url['user'], $forum_user_online['user_id']).'">'.forum_htmlencode($forum_user_online['ident']).'</a>' : forum_htmlencode($forum_user_online['ident']);
			++$forum_page['num_users'];
		}
		else
			++$forum_page['num_guests'];
	}

	$forum_page['online_info'] = array();
	$forum_page['online_info']['guests'] = ($forum_page['num_guests'] == 0) ?
		__('Guests none', 'index') : sprintf((($forum_page['num_guests'] == 1) ?
			__('Guests single', 'index') :
			__('Guests plural', 'index')), forum_number_format($forum_page['num_guests']));
	$forum_page['online_info']['users'] = ($forum_page['num_users'] == 0) ?
		__('Users none', 'index') : sprintf((($forum_page['num_users'] == 1) ?
			__('Users single', 'index') : __('Users plural', 'index')), forum_number_format($forum_page['num_users']));

	($hook = get_hook('in_users_online_pre_online_info_output')) ? eval($hook) : null;

	include view('index/online');

	($hook = get_hook('in_users_online_end')) ? eval($hook) : null;
}

($hook = get_hook('in_info_end')) ? eval($hook) : null;
