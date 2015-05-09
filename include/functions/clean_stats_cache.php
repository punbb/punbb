<?php
namespace punbb;

//
// Clean stats cache PHP scripts
//
function clean_stats_cache() {
	$cache_file = FORUM_CACHE_DIR.'cache_stats.php';
	if (file_exists($cache_file)) {
		unlink($cache_file);
	}
}
