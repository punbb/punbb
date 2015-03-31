<?php

if (defined('FORUM_DEBUG') || defined('FORUM_SHOW_QUERIES'))
{

($hook = get_hook('ft_debug_output_start')) ? eval($hook) : null;

// Display debug info (if enabled/defined)
if (defined('FORUM_DEBUG'))
{
	// Calculate script generation time
	$time_diff = forum_microtime() - $forum_start;
	$query_time_total = $time_percent_db = 0.0;

	$saved_queries = $forum_db->get_saved_queries();
	if (count($saved_queries) > 0)
	{
		foreach ($saved_queries as $cur_query)
		{
			$query_time_total += $cur_query[1];
		}

		if ($query_time_total > 0 && $time_diff > 0)
		{
			$time_percent_db = ($query_time_total / $time_diff) * 100;
		}
	}

	echo '<p id="querytime" class="quiet">'.sprintf($lang_common['Querytime'],
		forum_number_format($time_diff, 3),
		forum_number_format(100 - $time_percent_db, 0),
		forum_number_format($time_percent_db, 0),
		forum_number_format($forum_db->get_num_queries())).'</p>'."\n";
}

if (defined('FORUM_SHOW_QUERIES'))
	echo get_saved_queries();

($hook = get_hook('ft_debug_end')) ? eval($hook) : null;

}