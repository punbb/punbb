<?php
/**
 * Forum settings management page.
 *
 * Allows administrators to control many of the settings used in the site.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */
namespace punbb;

require __DIR__ . '/../vendor/autoload.php';

($hook = get_hook('aop_start')) ? eval($hook) : null;

if (user()->g_id != FORUM_ADMIN) {
	message(__('No permission'));
}

$section = isset($_GET['section']) ? $_GET['section'] : null;


if (isset($_POST['form_sent']))
{
	$form = array_map('trim', $_POST['form']);

	($hook = get_hook('aop_form_submitted')) ? eval($hook) : null;

	// Validate input depending on section
	switch ($section)
	{
		case 'setup':
		{
			($hook = get_hook('aop_setup_validation')) ? eval($hook) : null;

			if ($form['board_title'] == '')
				message(__('Error no board title', 'admin_settings'));

			// Clean default_lang, default_style, and sef
			$form['default_style'] = preg_replace('#[\.\\\/]#', '', $form['default_style']);
			$form['default_lang'] = preg_replace('#[\.\\\/]#', '', $form['default_lang']);
			$form['sef'] = preg_replace('#[\.\\\/]#', '', $form['sef']);

			// Make sure default_lang, default_style, and sef exist
			if (!file_exists(theme()->path[$form['default_style']] .
						'/' . $form['default_style'] . '.php')) {
				message(__('Bad request'));
			}
			if (!file_exists(language()->path[$form['default_lang']] . '/common.php')) {
				message(__('Bad request'));
			}
			if (!file_exists(rewrite()->path[$form['sef']] . '/forum_urls.php')) {
				message(__('Bad request'));
			}
			if (!isset($form['default_dst']) || $form['default_dst'] != '1') {
				$form['default_dst'] = '0';
			}

			$form['timeout_visit'] = intval($form['timeout_visit']);
			$form['timeout_online'] = intval($form['timeout_online']);
			$form['redirect_delay'] = intval($form['redirect_delay']);

			if ($form['timeout_online'] >= $form['timeout_visit'])
				message(__('Error timeout value', 'admin_settings'));

			$form['disp_topics_default'] = (intval($form['disp_topics_default']) > 0) ? intval($form['disp_topics_default']) : 1;
			$form['disp_posts_default'] = (intval($form['disp_posts_default']) > 0) ? intval($form['disp_posts_default']) : 1;

			if ($form['additional_navlinks'] != '')
				$form['additional_navlinks'] = forum_trim(forum_linebreaks($form['additional_navlinks']));

			break;
		}

		case 'features':
		{
			($hook = get_hook('aop_features_validation')) ? eval($hook) : null;

			if (!isset($form['search_all_forums']) || $form['search_all_forums'] != '1') $form['search_all_forums'] = '0';
			if (!isset($form['ranks']) || $form['ranks'] != '1') $form['ranks'] = '0';
			if (!isset($form['censoring']) || $form['censoring'] != '1') $form['censoring'] = '0';
			if (!isset($form['quickjump']) || $form['quickjump'] != '1') $form['quickjump'] = '0';
			if (!isset($form['show_version']) || $form['show_version'] != '1') $form['show_version'] = '0';
			if (!isset($form['show_moderators']) || $form['show_moderators'] != '1') $form['show_moderators'] = '0';
			if (!isset($form['users_online']) || $form['users_online'] != '1') $form['users_online'] = '0';

			if (!isset($form['quickpost']) || $form['quickpost'] != '1') $form['quickpost'] = '0';
			if (!isset($form['subscriptions']) || $form['subscriptions'] != '1') $form['subscriptions'] = '0';
			if (!isset($form['force_guest_email']) || $form['force_guest_email'] != '1') $form['force_guest_email'] = '0';
			if (!isset($form['show_dot']) || $form['show_dot'] != '1') $form['show_dot'] = '0';
			if (!isset($form['topic_views']) || $form['topic_views'] != '1') $form['topic_views'] = '0';
			if (!isset($form['show_post_count']) || $form['show_post_count'] != '1') $form['show_post_count'] = '0';
			if (!isset($form['show_user_info']) || $form['show_user_info'] != '1') $form['show_user_info'] = '0';

			if (!isset($form['message_bbcode']) || $form['message_bbcode'] != '1') $form['message_bbcode'] = '0';
			if (!isset($form['message_img_tag']) || $form['message_img_tag'] != '1') $form['message_img_tag'] = '0';
			if (!isset($form['smilies']) || $form['smilies'] != '1') $form['smilies'] = '0';
			if (!isset($form['make_links']) || $form['make_links'] != '1') $form['make_links'] = '0';
			if (!isset($form['message_all_caps']) || $form['message_all_caps'] != '1') $form['message_all_caps'] = '0';
			if (!isset($form['subject_all_caps']) || $form['subject_all_caps'] != '1') $form['subject_all_caps'] = '0';

			$form['indent_num_spaces'] = intval($form['indent_num_spaces']);
			$form['quote_depth'] = intval($form['quote_depth']);

			if (!isset($form['signatures']) || $form['signatures'] != '1') $form['signatures'] = '0';
			if (!isset($form['sig_bbcode']) || $form['sig_bbcode'] != '1') $form['sig_bbcode'] = '0';
			if (!isset($form['sig_img_tag']) || $form['sig_img_tag'] != '1') $form['sig_img_tag'] = '0';
			if (!isset($form['smilies_sig']) || $form['smilies_sig'] != '1') $form['smilies_sig'] = '0';
			if (!isset($form['sig_all_caps']) || $form['sig_all_caps'] != '1') $form['sig_all_caps'] = '0';

			$form['sig_length'] = intval($form['sig_length']);
			$form['sig_lines'] = intval($form['sig_lines']);

			if (!isset($form['avatars']) || $form['avatars'] != '1') $form['avatars'] = '0';

			// Make sure avatars_dir doesn't end with a slash
			if (substr($form['avatars_dir'], -1) == '/')
				$form['avatars_dir'] = substr($form['avatars_dir'], 0, -1);

			$form['avatars_width'] = intval($form['avatars_width']);
			$form['avatars_height'] = intval($form['avatars_height']);
			$form['avatars_size'] = intval($form['avatars_size']);

			if (!isset($form['check_for_updates']) || $form['check_for_updates'] != '1') $form['check_for_updates'] = '0';
			if (!isset($form['check_for_versions']) || $form['check_for_versions'] != '1') $form['check_for_versions'] = '0';

			if (!isset($form['mask_passwords']) || $form['mask_passwords'] != '1') $form['mask_passwords'] = '0';
			if (!isset($form['gzip']) || $form['gzip'] != '1') $form['gzip'] = '0';

			break;
		}

		case 'email':
		{
			($hook = get_hook('aop_email_validation')) ? eval($hook) : null;

			if (!defined('FORUM_EMAIL_FUNCTIONS_LOADED'))
				require FORUM_ROOT.'include/email.php';

			$form['admin_email'] = strtolower($form['admin_email']);
			if (!is_valid_email($form['admin_email']))
				message(__('Error invalid admin e-mail', 'admin_settings'));

			$form['webmaster_email'] = strtolower($form['webmaster_email']);
			if (!is_valid_email($form['webmaster_email']))
				message(__('Error invalid web e-mail', 'admin_settings'));

			if (!isset($form['smtp_ssl']) || $form['smtp_ssl'] != '1') $form['smtp_ssl'] = '0';

			break;
		}

		case 'announcements':
		{
			($hook = get_hook('aop_announcements_validation')) ? eval($hook) : null;

			if (!isset($form['announcement']) || $form['announcement'] != '1') $form['announcement'] = '0';

			if ($form['announcement_message'] != '')
				$form['announcement_message'] = forum_linebreaks($form['announcement_message']);
			else
				$form['announcement_message'] = __('Announcement message default', 'admin_settings');

			break;
		}

		case 'registration':
		{
			($hook = get_hook('aop_registration_validation')) ? eval($hook) : null;

			if (!isset($form['regs_allow']) || $form['regs_allow'] != '1') $form['regs_allow'] = '0';
			if (!isset($form['regs_verify']) || $form['regs_verify'] != '1') $form['regs_verify'] = '0';
			if (!isset($form['allow_banned_email']) || $form['allow_banned_email'] != '1') $form['allow_banned_email'] = '0';
			if (!isset($form['allow_dupe_email']) || $form['allow_dupe_email'] != '1') $form['allow_dupe_email'] = '0';
			if (!isset($form['regs_report']) || $form['regs_report'] != '1') $form['regs_report'] = '0';

			if (!isset($form['rules']) || $form['rules'] != '1') $form['rules'] = '0';

			if ($form['rules_message'] != '')
				$form['rules_message'] = forum_linebreaks($form['rules_message']);
			else
				$form['rules_message'] = __('Rules default', 'admin_settings');

			break;
		}

		case 'maintenance':
		{
			($hook = get_hook('aop_maintenance_validation')) ? eval($hook) : null;

			if (!isset($form['maintenance']) || $form['maintenance'] != '1') $form['maintenance'] = '0';

			if ($form['maintenance_message'] != '')
				$form['maintenance_message'] = forum_linebreaks($form['maintenance_message']);
			else
				$form['maintenance_message'] = __('Maintenance message default', 'admin_settings');

			break;
		}

		default:
		{
			($hook = get_hook('aop_new_section_validation')) ? eval($hook) : null;
			break;
		}
	}

	($hook = get_hook('aop_pre_update_configuration')) ? eval($hook) : null;

	foreach ($form as $key => $input)
	{
		// Only update permission values that have changed
		$pkey = 'p_' . $key;
		if (isset(config()->$pkey) && config()->$pkey != $input) {
			$query = array(
				'UPDATE'	=> 'config',
				'SET'		=> 'conf_value='.intval($input),
				'WHERE'		=> 'conf_name=\'p_'.db()->escape($key).'\''
			);

			($hook = get_hook('aop_qr_update_permission_conf')) ? eval($hook) : null;
			db()->query_build($query) or error(__FILE__, __LINE__);
		}

		// Only update option values that have changed
		$pkey = 'o_' . $key;
		if (isset(config()->$pkey) && config()->$pkey != $input) {
			if ($input != '' || is_int($input))
				$value = '\''.db()->escape($input).'\'';
			else
				$value = 'NULL';

			$query = array(
				'UPDATE'	=> 'config',
				'SET'		=> 'conf_value='.$value,
				'WHERE'		=> 'conf_name=\'o_'.db()->escape($key).'\''
			);

			($hook = get_hook('aop_qr_update_permission_option')) ? eval($hook) : null;
			db()->query_build($query) or error(__FILE__, __LINE__);
		}
	}

	// Regenerate the config cache
	require FORUM_ROOT . 'include/cache.php';
	generate_config_cache();

	// If changed sef - remove quick-jump cache
	if (!empty(config()->o_sef) && !empty($form['sef']))
	{
		if (config()->o_sef != $form['sef'])
		{
			clean_quickjump_cache();
		}
	}

	// Add flash message
	flash()->add_info(__('Settings updated', 'admin_settings'));

	($hook = get_hook('aop_pre_redirect')) ? eval($hook) : null;

	redirect(link('admin_settings_'.$section), __('Settings updated', 'admin_settings'));
}


if (!$section || $section == 'setup')
{
	// Setup the form
	$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array(config()->o_board_title, link('index')),
		array(__('Forum administration', 'admin_common'), link('admin_index')),
		array(__('Settings', 'admin_common'), link('admin_settings_setup')),
		array(__('Setup', 'admin_common'), link('admin_settings_setup'))
	);

	($hook = get_hook('aop_setup_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE_SECTION', 'settings');
	define('FORUM_PAGE', 'admin-settings-setup');

	$main_view = 'admin/settings/setup';
}

else if ($section == 'features')
{
	// Setup the form
	$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array(config()->o_board_title, link('index')),
		array(__('Forum administration', 'admin_common'), link('admin_index')),
		array(__('Settings', 'admin_common'), link('admin_settings_setup')),
		array(__('Features', 'admin_common'), link('admin_settings_features'))
	);

	($hook = get_hook('aop_features_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE_SECTION', 'settings');
	define('FORUM_PAGE', 'admin-settings-features');

	$main_view = 'admin/settings/features';
}
else if ($section == 'announcements')
{
	// Setup the form
	$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array(config()->o_board_title, link('index')),
		array(__('Forum administration', 'admin_common'), link('admin_index')),
		array(__('Settings', 'admin_common'), link('admin_settings_setup')),
		array(__('Announcements', 'admin_common'), link('admin_settings_announcements'))
	);

	($hook = get_hook('aop_announcements_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE_SECTION', 'settings');
	define('FORUM_PAGE', 'admin-settings-announcements');

	$main_view = 'admin/settings/announcements';
}
else if ($section == 'registration')
{
	// Setup the form
	$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array(config()->o_board_title, link('index')),
		array(__('Forum administration', 'admin_common'), link('admin_index')),
		array(__('Settings', 'admin_common'), link('admin_settings_setup')),
		array(__('Registration', 'admin_common'), link('admin_settings_registration'))
	);

	($hook = get_hook('aop_registration_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE_SECTION', 'settings');
	define('FORUM_PAGE', 'admin-settings-registration');

	$main_view = 'admin/settings/registration';
}

else if ($section == 'maintenance')
{
	// Setup the form
	$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array(config()->o_board_title, link('index')),
		array(__('Forum administration', 'admin_common'), link('admin_index')),
		array(__('Management', 'admin_common'), link('admin_reports')),
		array(__('Maintenance mode', 'admin_common'), link('admin_settings_maintenance'))
	);

	($hook = get_hook('aop_maintenance_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE_SECTION', 'management');
	define('FORUM_PAGE', 'admin-settings-maintenance');

	$main_view = 'admin/settings/maintenance';
}

else if ($section == 'email')
{
	// Setup the form
	$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array(config()->o_board_title, link('index')),
		array(__('Forum administration', 'admin_common'), link('admin_index')),
		array(__('Settings', 'admin_common'), link('admin_settings_setup')),
		array(__('E-mail', 'admin_common'), link('admin_settings_email'))
	);

	($hook = get_hook('aop_email_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE_SECTION', 'settings');
	define('FORUM_PAGE', 'admin-settings-email');

	$main_view = 'admin/settings/email';
}
else {
	($hook = get_hook('aop_new_section')) ? eval($hook) : null;
}

($hook = get_hook('aop_end')) ? eval($hook) : null;

template()->render([
	'main_view' => $main_view
]);
