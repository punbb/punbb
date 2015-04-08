<?php
/**
 * Loads common functions used in the administration panel.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */

// Make sure no one attempts to run this script "directly"
if (!defined('FORUM'))
	exit;


//
// Display the admin navigation menu
//
function generate_admin_menu($submenu)
{
	global $forum_config, $forum_url, $forum_user, $db_type;

	$return = ($hook = get_hook('ca_fn_generate_admin_menu_start')) ? eval($hook) : null;
	if ($return != null)
		return $return;

	if ($submenu)
	{
		$forum_page['admin_submenu'] = array();

		if ($forum_user['g_id'] != FORUM_ADMIN)
		{
			$forum_page['admin_submenu']['index'] = '<li class="'.((FORUM_PAGE == 'admin-information') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.forum_link($forum_url['admin_index']).'">'.
				__('Information', 'admin_common') . '</span></a></li>';
			$forum_page['admin_submenu']['users'] = '<li class="'.((FORUM_PAGE == 'admin-users') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.forum_link($forum_url['admin_users']).'">'.
				__('Searches', 'admin_common').'</a></li>';

			if ($forum_config['o_censoring'] == '1')
				$forum_page['admin_submenu']['censoring'] = '<li class="'.((FORUM_PAGE == 'admin-censoring') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.forum_link($forum_url['admin_censoring']).'">'.
					__('Censoring', 'admin_common').'</a></li>';

			$forum_page['admin_submenu']['reports'] = '<li class="'.((FORUM_PAGE == 'admin-reports') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.forum_link($forum_url['admin_reports']).'">'.
				__('Reports', 'admin_common').'</a></li>';

			if ($forum_user['g_mod_ban_users'] == '1')
				$forum_page['admin_submenu']['bans'] = '<li class="'.((FORUM_PAGE == 'admin-bans') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.forum_link($forum_url['admin_bans']).'">'.
					__('Bans', 'admin_common').'</a></li>';
		}
		else
		{
			if (FORUM_PAGE_SECTION == 'start')
			{
				$forum_page['admin_submenu']['index'] = '<li class="'.((FORUM_PAGE == 'admin-information') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.forum_link($forum_url['admin_index']).'">'.
					__('Information', 'admin_common').'</a></li>';
				$forum_page['admin_submenu']['categories'] = '<li class="'.((FORUM_PAGE == 'admin-categories') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.forum_link($forum_url['admin_categories']).'">'.
					__('Categories', 'admin_common').'</a></li>';
				$forum_page['admin_submenu']['forums'] = '<li class="'.((FORUM_PAGE == 'admin-forums') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.forum_link($forum_url['admin_forums']).'">'.
					__('Forums', 'admin_common').'</a></li>';
			}
			else if (FORUM_PAGE_SECTION == 'users')
			{
				$forum_page['admin_submenu']['users'] = '<li class="'.((FORUM_PAGE == 'admin-users' || FORUM_PAGE == 'admin-uresults' || FORUM_PAGE == 'admin-iresults') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.forum_link($forum_url['admin_users']).'">'.
					__('Searches', 'admin_common').'</a></li>';
				$forum_page['admin_submenu']['groups'] = '<li class="'.((FORUM_PAGE == 'admin-groups') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.forum_link($forum_url['admin_groups']).'">'.
					__('Groups', 'admin_common').'</a></li>';
				$forum_page['admin_submenu']['ranks'] = '<li class="'.((FORUM_PAGE == 'admin-ranks') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.forum_link($forum_url['admin_ranks']).'">'.
					__('Ranks', 'admin_common').'</a></li>';
				$forum_page['admin_submenu']['bans'] = '<li class="'.((FORUM_PAGE == 'admin-bans') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.forum_link($forum_url['admin_bans']).'">'.
					__('Bans', 'admin_common').'</a></li>';
			}
			else if (FORUM_PAGE_SECTION == 'settings')
			{
				$forum_page['admin_submenu']['settings_setup'] = '<li class="'.((FORUM_PAGE == 'admin-settings-setup') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.forum_link($forum_url['admin_settings_setup']).'">'.
					__('Setup', 'admin_common').'</a></li>';
				$forum_page['admin_submenu']['settings_features'] = '<li class="'.((FORUM_PAGE == 'admin-settings-features') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.forum_link($forum_url['admin_settings_features']).'">'.
					__('Features', 'admin_common').'</a></li>';
				$forum_page['admin_submenu']['settings-announcements'] = '<li class="'.((FORUM_PAGE == 'admin-settings-announcements') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.forum_link($forum_url['admin_settings_announcements']).'">'.
					__('Announcements', 'admin_common').'</a></li>';
				$forum_page['admin_submenu']['settings-email'] = '<li class="'.((FORUM_PAGE == 'admin-settings-email') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.forum_link($forum_url['admin_settings_email']).'">'.
					__('E-mail', 'admin_common').'</a></li>';
				$forum_page['admin_submenu']['settings-registration'] = '<li class="'.((FORUM_PAGE == 'admin-settings-registration') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.forum_link($forum_url['admin_settings_registration']).'">'.
					__('Registration', 'admin_common').'</a></li>';
				$forum_page['admin_submenu']['censoring'] = '<li class="'.((FORUM_PAGE == 'admin-censoring') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.forum_link($forum_url['admin_censoring']).'">'.
					__('Censoring', 'admin_common').'</a></li>';
			}
			else if (FORUM_PAGE_SECTION == 'management')
			{
				$forum_page['admin_submenu']['reports'] = '<li class="'.((FORUM_PAGE == 'admin-reports') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.forum_link($forum_url['admin_reports']).'">'.
					__('Reports', 'admin_common').'</a></li>';
				$forum_page['admin_submenu']['prune'] = '<li class="'.((FORUM_PAGE == 'admin-prune') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.forum_link($forum_url['admin_prune']).'">'.
					__('Prune topics', 'admin_common').'</a></li>';
				$forum_page['admin_submenu']['reindex'] = '<li class="'.((FORUM_PAGE == 'admin-reindex') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.forum_link($forum_url['admin_reindex']).'">'.
					__('Rebuild index', 'admin_common').'</a></li>';
				$forum_page['admin_submenu']['options-maintenance'] = '<li class="'.((FORUM_PAGE == 'admin-settings-maintenance') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.forum_link($forum_url['admin_settings_maintenance']).'">'.
					__('Maintenance mode', 'admin_common').'</a></li>';
			}
			else if (FORUM_PAGE_SECTION == 'extensions')
			{
				$forum_page['admin_submenu']['extensions-manage'] = '<li class="'.((FORUM_PAGE == 'admin-extensions-manage') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.forum_link($forum_url['admin_extensions_manage']).'">'.
					__('Manage extensions', 'admin_common').'</a></li>';
				$forum_page['admin_submenu']['extensions-hotfixes'] = '<li class="'.((FORUM_PAGE == 'admin-extensions-hotfixes') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.forum_link($forum_url['admin_extensions_hotfixes']).'">'.
					__('Manage hotfixes', 'admin_common').'</a></li>';
			}
		}

		($hook = get_hook('ca_fn_generate_admin_menu_new_sublink')) ? eval($hook) : null;

		return (!empty($forum_page['admin_submenu'])) ? implode("\n\t\t", $forum_page['admin_submenu']) : '';
	}
	else
	{
		if ($forum_user['g_id'] != FORUM_ADMIN)
			$forum_page['admin_menu']['index'] = '<li class="active first-item"><a href="'.forum_link($forum_url['admin_index']).'"><span>'.
				__('Moderate', 'admin_common').'</span></a></li>';
		else
		{
			$forum_page['admin_menu']['index'] = '<li class="'.((FORUM_PAGE_SECTION == 'start') ? 'active' : 'normal').((empty($forum_page['admin_menu'])) ? ' first-item' : '').'"><a href="'.forum_link($forum_url['admin_index']).'"><span>'.
				__('Start', 'admin_common').'</span></a></li>';
			$forum_page['admin_menu']['settings_setup'] = '<li class="'.((FORUM_PAGE_SECTION == 'settings') ? 'active' : 'normal').((empty($forum_page['admin_menu'])) ? ' first-item' : '').'"><a href="'.forum_link($forum_url['admin_settings_setup']).'"><span>'.
				__('Settings', 'admin_common').'</span></a></li>';
			$forum_page['admin_menu']['users'] = '<li class="'.((FORUM_PAGE_SECTION == 'users') ? 'active' : 'normal').((empty($forum_page['admin_menu'])) ? ' first-item' : '').'"><a href="'.forum_link($forum_url['admin_users']).'"><span>'.
				__('Users', 'admin_common').'</span></a></li>';
			$forum_page['admin_menu']['reports'] = '<li class="'.((FORUM_PAGE_SECTION == 'management') ? 'active' : 'normal').((empty($forum_page['admin_menu'])) ? ' first-item' : '').'"><a href="'.forum_link($forum_url['admin_reports']).'"><span>'.
				__('Management', 'admin_common').'</span></a></li>';
			$forum_page['admin_menu']['extensions_manage'] = '<li class="'.((FORUM_PAGE_SECTION == 'extensions') ? 'active' : 'normal').((empty($forum_page['admin_menu'])) ? ' first-item' : '').'"><a href="'.forum_link($forum_url['admin_extensions_manage']).'"><span>'.
				__('Extensions', 'admin_common').'</span></a></li>';
		}

		($hook = get_hook('ca_fn_generate_admin_menu_new_link')) ? eval($hook) : null;

		return implode("\n\t\t", $forum_page['admin_menu']);
	}
}


//
// Delete topics from $forum_id that are "older than" $prune_date (if $prune_sticky is 1, sticky topics will also be deleted)
//
function prune($forum_id, $prune_sticky, $prune_date)
{
	global $forum_db, $db_type;

	$return = ($hook = get_hook('ca_fn_prune_start')) ? eval($hook) : null;
	if ($return != null)
		return;

	// Fetch topics to prune
	$query = array(
		'SELECT'	=> 't.id',
		'FROM'		=> 'topics AS t',
		'WHERE'		=> 't.forum_id='.$forum_id
	);

	if ($prune_date != -1)
		$query['WHERE'] .= ' AND last_post<'.$prune_date;
	if (!$prune_sticky)
		$query['WHERE'] .= ' AND sticky=\'0\'';

	($hook = get_hook('ca_fn_prune_qr_get_topics_to_prune')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

	$topic_ids = array();
	while ($row = $forum_db->fetch_row($result))
		$topic_ids[] = $row[0];

	if (!empty($topic_ids))
	{
		$topic_ids = implode(',', $topic_ids);

		// Fetch posts to prune (used lated for updating the search index)
		$query = array(
			'SELECT'	=> 'p.id',
			'FROM'		=> 'posts AS p',
			'WHERE'		=> 'p.topic_id IN('.$topic_ids.')'
		);

		($hook = get_hook('ca_fn_prune_qr_get_posts_to_prune')) ? eval($hook) : null;
		$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

		$post_ids = array();
		while ($row = $forum_db->fetch_row($result))
			$post_ids[] = $row[0];

		// Delete topics
		$query = array(
			'DELETE'	=> 'topics',
			'WHERE'		=> 'id IN('.$topic_ids.')'
		);

		($hook = get_hook('ca_fn_prune_qr_prune_topics')) ? eval($hook) : null;
		$forum_db->query_build($query) or error(__FILE__, __LINE__);

		// Delete posts
		$query = array(
			'DELETE'	=> 'posts',
			'WHERE'		=> 'topic_id IN('.$topic_ids.')'
		);

		($hook = get_hook('ca_fn_prune_qr_prune_posts')) ? eval($hook) : null;
		$forum_db->query_build($query) or error(__FILE__, __LINE__);

		// Delete subscriptions
		$query = array(
			'DELETE'	=> 'subscriptions',
			'WHERE'		=> 'topic_id IN('.$topic_ids.')'
		);

		($hook = get_hook('ca_fn_prune_qr_prune_subscriptions')) ? eval($hook) : null;
		$forum_db->query_build($query) or error(__FILE__, __LINE__);

		// We removed a bunch of posts, so now we have to update the search index
		if (!defined('FORUM_SEARCH_IDX_FUNCTIONS_LOADED'))
			require FORUM_ROOT.'include/search_idx.php';

		strip_search_index($post_ids);
	}
}


// Add config value to forum config table
// Warning!
// This function dont refresh config cache - use "forum_clear_cache()" if
// call this function outside install/uninstall extension manifest section
function forum_config_add($name, $value)
{
	global $forum_db, $forum_config;

	if (!empty($name) && !isset($forum_config[$name]))
	{
		$query = array(
			'INSERT'	=> 'conf_name, conf_value',
			'INTO'		=> 'config',
			'VALUES'	=> '\''.$name.'\', \''.$value.'\''
		);
		$forum_db->query_build($query) or error(__FILE__, __LINE__);
	}
}


// Remove config value from forum config table
// Warning!
// This function dont refresh config cache - use "forum_clear_cache()" if
// call this function outside install/uninstall extension manifest section
function forum_config_remove($name)
{
	global $forum_db;

	if (is_array($name) && count($name) > 0)
	{
		if (!function_exists('clean_conf_names'))
		{
			function clean_conf_names($n)
			{
				global $forum_db;
				return '\''.$forum_db->escape($n).'\'';
			}
		}

		$name = array_map('clean_conf_names', $name);

		$query = array(
			'DELETE'	=> 'config',
			'WHERE'		=> 'conf_name in ('.implode(',', $name).')',
		);
		$forum_db->query_build($query) or error(__FILE__, __LINE__);
	}
	else if (!empty($name))
	{
		$query = array(
			'DELETE'	=> 'config',
			'WHERE'		=> 'conf_name=\''.$forum_db->escape($name).'\''
		);
		$forum_db->query_build($query) or error(__FILE__, __LINE__);
	}
}


($hook = get_hook('ca_new_function')) ? eval($hook) : null;
