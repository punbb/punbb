<?php
namespace punbb;

$config = config();
$user = user();

// Display the "Jump to" drop list
if ($user->g_read_board == '1' && $config->o_quickjump == '1') {
	$cached = cache()->get('cache_quickjump_' . $user->g_id);
	if (!$cached) {
		cache()->generate('quickjump_cache', $user->g_id);
		$cached = cache()->get('cache_quickjump_' . $user->g_id);
	}
}

?>
	<p id="copyright"><?= sprintf(__('Powered by'),
		'<a href="http://punbb.informer.com/">PunBB</a>' .
		($config->o_show_version == '1'? (' ' . $config->o_cur_version) : ''),
		'<a href="http://www.informer.com/">Informer Technologies, Inc</a>') ?>
	</p>
<?php
