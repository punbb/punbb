<?php
namespace punbb;

$config = config();
$user = user();

// Display the "Jump to" drop list
if ($user->g_read_board == '1' && $config->o_quickjump == '1') {
	// Load cached quickjump
	if (file_exists(FORUM_CACHE_DIR . 'cache_quickjump_' . $user->g_id . '.php')) {
		include FORUM_CACHE_DIR . 'cache_quickjump_' . $user->g_id . '.php';
	}

	if (!defined('FORUM_QJ_LOADED')) {
		require FORUM_ROOT . 'include/cache.php';
		generate_quickjump_cache($user->g_id);
		require FORUM_CACHE_DIR . 'cache_quickjump_' . $user->g_id . '.php';
	}
}

?>
	<p id="copyright"><?= sprintf(__('Powered by'),
		'<a href="http://punbb.informer.com/">PunBB</a>' .
		($config->o_show_version == '1'? (' ' . $config->o_cur_version) : ''),
		'<a href="http://www.informer.com/">Informer Technologies, Inc</a>') ?>
	</p>
<?php
