<?php

($hook = get_hook('ft_about_output_start')) ? eval($hook) : null;

// Display the "Jump to" drop list
if ($forum_user['g_read_board'] == '1' && $forum_config['o_quickjump'] == '1')
{
	($hook = get_hook('ft_about_pre_quickjump')) ? eval($hook) : null;

	// Load cached quickjump
	if (file_exists(FORUM_CACHE_DIR.'cache_quickjump_'.$forum_user['g_id'].'.php'))
		include FORUM_CACHE_DIR.'cache_quickjump_'.$forum_user['g_id'].'.php';

	if (!defined('FORUM_QJ_LOADED'))
	{
		if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
			require FORUM_ROOT.'include/cache.php';

		generate_quickjump_cache($forum_user['g_id']);
		require FORUM_CACHE_DIR.'cache_quickjump_'.$forum_user['g_id'].'.php';
	}
}

($hook = get_hook('ft_about_pre_copyright')) ? eval($hook) : null;

?>
	<p id="copyright"><?= sprintf(__('Powered by'),
		'<a href="http://punbb.informer.com/">PunBB</a>'.($forum_config['o_show_version'] == '1' ? ' '.$forum_config['o_cur_version'] : ''), '<a href="http://www.informer.com/">Informer Technologies, Inc</a>') ?></p>
<?php

($hook = get_hook('ft_about_end')) ? eval($hook) : null;
