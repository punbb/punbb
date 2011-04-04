<?php
/**
 * Outputs the footer used by most forum pages.
 *
 * @copyright (C) 2008-2009 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */


// Make sure no one attempts to run this script "directly"
if (!defined('FORUM'))
	exit;

// START SUBST - <!-- forum_about -->
ob_start();
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
	<p id="copyright"><?php echo sprintf($lang_common['Powered by'], '<a href="http://punbb.informer.com/">PunBB</a>'.($forum_config['o_show_version'] == '1' ? ' '.$forum_config['o_cur_version'] : ''), '<a href="http://www.informer.com/">Informer Technologies, Inc</a>') ?></p>
<?php

($hook = get_hook('ft_about_end')) ? eval($hook) : null;

$tpl_temp = forum_trim(ob_get_contents());
$tpl_main = str_replace('<!-- forum_about -->', $tpl_temp, $tpl_main);
ob_end_clean();
// END SUBST - <!-- forum_about -->


// START SUBST - <!-- forum_debug -->
if (defined('FORUM_DEBUG') || defined('FORUM_SHOW_QUERIES'))
{
	ob_start();

	($hook = get_hook('ft_debug_output_start')) ? eval($hook) : null;

	// Display debug info (if enabled/defined)
	if (defined('FORUM_DEBUG'))
	{
		// Calculate script generation time
		list($usec, $sec) = explode(' ', microtime());
		$time_diff = forum_number_format(((float)$usec + (float)$sec) - $forum_start, 3);
		echo '<p id="querytime" class="quiet">'.sprintf($lang_common['Querytime'], $time_diff, forum_number_format($forum_db->get_num_queries())).'</p>'."\n";
	}

	if (defined('FORUM_SHOW_QUERIES'))
		echo get_saved_queries();

	($hook = get_hook('ft_debug_end')) ? eval($hook) : null;

	$tpl_temp = forum_trim(ob_get_contents());
	$tpl_main = str_replace('<!-- forum_debug -->', $tpl_temp, $tpl_main);
	ob_end_clean();
}
// END SUBST - <!-- forum_debug -->


// START SUBST - <!-- forum_javascript -->
$forum_javascript_commonjs_urls = '
	if (typeof FORUM === "undefined" || !FORUM) {
		var FORUM = {};
	}

	FORUM.env = {
		base_url: "'.forum_htmlencode($base_url).'/",
		base_js_url: "'.forum_htmlencode($base_url).'/include/js/",
		user_lang: "'.forum_htmlencode($forum_user['language']).'",
		user_style: "'.forum_htmlencode($forum_user['style']).'",
		user_is_guest: "'.forum_htmlencode(($forum_user['is_guest'] == 1) ? "1" : "0").'",
		page: "'.forum_htmlencode((defined("FORUM_PAGE")) ? FORUM_PAGE : "unknown" ).'",
	};';


forum_add_js($forum_javascript_commonjs_urls, array('type' => 'inline', 'weight' => 50, 'group' => FORUM_JS_GROUP_SYSTEM));
forum_add_js(FORUM_ROOT.'include/js/min/common.min.js', array('type' => 'file', 'weight' => 55, 'async' => false, 'group' => FORUM_JS_GROUP_SYSTEM));

($hook = get_hook('ft_js_include')) ? eval($hook) : null;

$tpl_main = str_replace('<!-- forum_javascript -->', forum_output_lib_js(), $tpl_main);
// END SUBST - <!-- forum_javascript -->

// Last call!
($hook = get_hook('ft_end')) ? eval($hook) : null;

// End the transaction
$forum_db->end_transaction();

// Close the db connection (and free up any result data)
$forum_db->close();

// Spit out the page
exit($tpl_main);
