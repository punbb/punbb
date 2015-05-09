<?php
namespace punbb;

	// Set up table headers
	$forum_page['table_header'] = array();
	$forum_page['table_header']['ip'] = '<th class="tc'.count($forum_page['table_header']).'" scope="col">'.
		__('IP address', 'admin_users').'</th>';
	$forum_page['table_header']['lastused'] = '<th class="tc'.count($forum_page['table_header']).'" scope="col">'.
		__('Last used', 'admin_users').'</th>';
	$forum_page['table_header']['timesfound'] = '<th class="tc'.count($forum_page['table_header']).'" scope="col">'.
		__('Times found', 'admin_users').'</th>';
	$forum_page['table_header']['actions'] = '<th class="tc'.count($forum_page['table_header']).'" scope="col">'.
		__('Actions', 'admin_users').'</th>';

	($hook = get_hook('aus_ip_stats_output_start')) ? eval($hook) : null;

?>
	<div class="main-head">
<?php

	if (!empty($main_head_options))
		echo "\n\t\t".'<p class="options">'.implode(' ', $main_head_options).'</p>';

?>
		<h2 class="hn"><span><?php printf(__('IP addresses found', 'admin_users'), $forum_page['num_users']) ?></span></h2>
	</div>
	<div class="main-content main-forum">
		<table>
			<thead>
				<tr>
					<?php echo implode("\n\t\t\t\t", $forum_page['table_header'])."\n" ?>
				</tr>
			</thead>
			<tbody>
<?php

	if ($forum_page['num_users'])
	{
		$forum_page['item_count'] = 0;

		foreach ($founded_ips as $cur_ip)
		{
			++$forum_page['item_count'];

			$forum_page['item_style'] = (($forum_page['item_count'] % 2 != 0) ? 'odd' : 'even');
			if ($forum_page['item_count'] == 1)
				$forum_page['item_style'] .= ' row1';

			($hook = get_hook('aus_ip_stats_pre_row_generation')) ? eval($hook) : null;

			$forum_page['table_row'] = array();
			$forum_page['table_row']['ip'] = '<td class="tc'.count($forum_page['table_row']).'"><a href="'.link('get_host', $cur_ip['poster_ip']).'">'.$cur_ip['poster_ip'].'</a></td>';
			$forum_page['table_row']['lastused'] = '<td class="tc'.count($forum_page['table_row']).'">'.format_time($cur_ip['last_used']).'</td>';
			$forum_page['table_row']['timesfound'] = '<td class="tc'.count($forum_page['table_row']).'">'.$cur_ip['used_times'].'</td>';
			$forum_page['table_row']['actions'] = '<td class="tc'.count($forum_page['table_row']).'"><a href="'.link('admin_users').'?show_users='.$cur_ip['poster_ip'].'">'.
				__('Find more users', 'admin_users').'</a></td>';

			($hook = get_hook('aus_ip_stats_pre_row_output')) ? eval($hook) : null;

?>
				<tr class="<?php echo $forum_page['item_style'] ?>">
					<?php echo implode("\n\t\t\t\t", $forum_page['table_row'])."\n" ?>
				</tr>
<?php

		}
	}
	else
	{
		($hook = get_hook('aus_ip_stats_pre_no_results_row_generation')) ? eval($hook) : null;

		$forum_page['table_row'] = array();
		$forum_page['table_row']['ip'] = '<td class="tc'.count($forum_page['table_row']).'">'.
			__('No posts by user', 'admin_users').'</td>';
		$forum_page['table_row']['lastused'] = '<td class="tc'.count($forum_page['table_row']).'"> - </td>';
		$forum_page['table_row']['timesfound'] = '<td class="tc'.count($forum_page['table_row']).'"> - </td>';
		$forum_page['table_row']['actions'] = '<td class="tc'.count($forum_page['table_row']).'"> - </td>';

		($hook = get_hook('aus_ip_stats_pre_no_results_row_output')) ? eval($hook) : null;

?>
				<tr class="odd row1">
					<?php echo implode("\n\t\t\t\t", $forum_page['table_row'])."\n" ?>
				</tr>
<?php

	}


?>
			</tbody>
		</table>
	</div>
	<div class="main-foot">
<?php

	if (!empty($forum_page['main_foot_options']))
		echo "\n\t\t".'<p class="options">'.implode(' ', $forum_page['main_foot_options']).'</p>';

?>
		<h2 class="hn"><span><?php printf(__('IP addresses found', 'admin_users'), $forum_page['num_users']) ?></span></h2>
	</div>
<?php

($hook = get_hook('aus_ip_stats_end')) ? eval($hook) : null;
