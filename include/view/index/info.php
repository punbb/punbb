<?php

($hook = get_hook('in_info_output_start')) ? eval($hook) : null;


if (file_exists(FORUM_CACHE_DIR.'cache_stats.php'))
	include FORUM_CACHE_DIR.'cache_stats.php';

// Regenerate cache only if the cache is more than 30 minutes old
if (!defined('FORUM_STATS_LOADED') || $forum_stats['cached'] < (time() - 1800))
{
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_stats_cache();
	require FORUM_CACHE_DIR.'cache_stats.php';
}

$stats_list['no_of_users'] = '<li class="st-users"><span>'.sprintf($lang_index['No of users'], '<strong>'.forum_number_format($forum_stats['total_users']).'</strong>').'</span></li>';
$stats_list['newest_user'] = '<li class="st-users"><span>'.sprintf($lang_index['Newest user'], '<strong>'.($forum_user['g_view_users'] == '1' ? '<a href="'.forum_link($forum_url['user'], $forum_stats['last_user']['id']).'">'.forum_htmlencode($forum_stats['last_user']['username']).'</a>' : forum_htmlencode($forum_stats['last_user']['username'])).'</strong>').'</span></li>';
$stats_list['no_of_topics'] = '<li class="st-activity"><span>'.sprintf($lang_index['No of topics'], '<strong>'.forum_number_format($forum_stats['total_topics']).'</strong>').'</span></li>';
$stats_list['no_of_posts'] = '<li class="st-activity"><span>'.sprintf($lang_index['No of posts'], '<strong>'.forum_number_format($forum_stats['total_posts']).'</strong>').'</span></li>';

($hook = get_hook('in_stats_pre_info_output')) ? eval($hook) : null;

?>
<div id="brd-stats" class="gen-content">
	<h2 class="hn"><span><?php echo $lang_index['Statistics'] ?></span></h2>
	<ul>
		<?php echo implode("\n\t\t", $stats_list)."\n" ?>
	</ul>
</div>
<?php

($hook = get_hook('in_stats_end')) ? eval($hook) : null;
($hook = get_hook('in_users_online_start')) ? eval($hook) : null;

if ($forum_config['o_users_online'] == '1')
{
	// Fetch users online info and generate strings for output
	$query = array(
		'SELECT'	=> 'o.user_id, o.ident',
		'FROM'		=> 'online AS o',
		'WHERE'		=> 'o.idle=0',
		'ORDER BY'	=> 'o.ident'
	);

	($hook = get_hook('in_users_online_qr_get_online_info')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	$forum_page['num_guests'] = $forum_page['num_users'] = 0;
	$users = array();

	while ($forum_user_online = $forum_db->fetch_assoc($result))
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
	$forum_page['online_info']['guests'] = ($forum_page['num_guests'] == 0) ? $lang_index['Guests none'] : sprintf((($forum_page['num_guests'] == 1) ? $lang_index['Guests single'] : $lang_index['Guests plural']), forum_number_format($forum_page['num_guests']));
	$forum_page['online_info']['users'] = ($forum_page['num_users'] == 0) ? $lang_index['Users none'] : sprintf((($forum_page['num_users'] == 1) ? $lang_index['Users single'] : $lang_index['Users plural']), forum_number_format($forum_page['num_users']));

	($hook = get_hook('in_users_online_pre_online_info_output')) ? eval($hook) : null;
?>
<div id="brd-online" class="gen-content">
	<h3 class="hn"><span><?php printf($lang_index['Currently online'], implode($lang_index['Online stats separator'], $forum_page['online_info'])) ?></span></h3>
<?php if (!empty($users)): ?>
	<p><?php echo implode($lang_index['Online list separator'], $users) ?></p>
<?php endif; ($hook = get_hook('in_new_online_data')) ? eval($hook) : null; ?>
</div>
<?php

	($hook = get_hook('in_users_online_end')) ? eval($hook) : null;
}

($hook = get_hook('in_info_end')) ? eval($hook) : null;
