<?php
namespace punbb;

if (!defined('FORUM_DEBUG') && !defined('FORUM_SHOW_QUERIES')) {
	return;
}

// Display debug info (if enabled/defined)
if (defined('FORUM_DEBUG')) {
	// Calculate script generation time
	$time_diff = forum_microtime() - app()->forum_start;
	$query_time_total = $time_percent_db = 0.0;

	$saved_queries = db()->get_saved_queries();
	if (count($saved_queries) > 0) {
		foreach ($saved_queries as $cur_query) {
			$query_time_total += $cur_query[1];
		}

		if ($query_time_total > 0 && $time_diff > 0) {
			$time_percent_db = ($query_time_total / $time_diff) * 100;
		}
	}

	echo '<p id="querytime" class="quiet">'.sprintf(__('Querytime'),
		forum_number_format($time_diff, 3),
		forum_number_format(100 - $time_percent_db, 0),
		forum_number_format($time_percent_db, 0),
		forum_number_format(db()->get_num_queries())).'</p>'."\n";
}

if (defined('FORUM_SHOW_QUERIES')) {
	echo get_saved_queries();
}
