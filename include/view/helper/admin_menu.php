<?php
namespace punbb;

global $forum_page;

if (!function_exists('generate_admin_menu')) {
	function generate_admin_menu($submenu) {
		global $forum_url, $db_type;

		$return = ($hook = get_hook('ca_fn_generate_admin_menu_start')) ? eval($hook) : null;
		if ($return != null) {
			return $return;
		}

		if ($submenu) {
			$forum_page['admin_submenu'] = array();

			if (user()->g_id != FORUM_ADMIN) {
				$forum_page['admin_submenu']['index'] = '<li class="'.((FORUM_PAGE == 'admin-information') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.link('admin_index').'">'.
					__('Information', 'admin_common') . '</span></a></li>';
				$forum_page['admin_submenu']['users'] = '<li class="'.((FORUM_PAGE == 'admin-users') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.link('admin_users').'">'.
					__('Searches', 'admin_common').'</a></li>';

				if (config()->o_censoring == '1')
					$forum_page['admin_submenu']['censoring'] = '<li class="'.((FORUM_PAGE == 'admin-censoring') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.link('admin_censoring').'">'.
						__('Censoring', 'admin_common').'</a></li>';

				$forum_page['admin_submenu']['reports'] = '<li class="'.((FORUM_PAGE == 'admin-reports') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.link('admin_reports').'">'.
					__('Reports', 'admin_common').'</a></li>';

				if (user()->g_mod_ban_users == '1')
					$forum_page['admin_submenu']['bans'] = '<li class="'.((FORUM_PAGE == 'admin-bans') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.link('admin_bans').'">'.
						__('Bans', 'admin_common').'</a></li>';
			}
			else
			{
				if (FORUM_PAGE_SECTION == 'start')
				{
					$forum_page['admin_submenu']['index'] = '<li class="'.((FORUM_PAGE == 'admin-information') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.link('admin_index').'">'.
						__('Information', 'admin_common').'</a></li>';
					$forum_page['admin_submenu']['categories'] = '<li class="'.((FORUM_PAGE == 'admin-categories') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.link('admin_categories').'">'.
						__('Categories', 'admin_common').'</a></li>';
					$forum_page['admin_submenu']['forums'] = '<li class="'.((FORUM_PAGE == 'admin-forums') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.link('admin_forums').'">'.
						__('Forums', 'admin_common').'</a></li>';
				}
				else if (FORUM_PAGE_SECTION == 'users')
				{
					$forum_page['admin_submenu']['users'] = '<li class="'.((FORUM_PAGE == 'admin-users' || FORUM_PAGE == 'admin-uresults' || FORUM_PAGE == 'admin-iresults') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.link('admin_users').'">'.
						__('Searches', 'admin_common').'</a></li>';
					$forum_page['admin_submenu']['groups'] = '<li class="'.((FORUM_PAGE == 'admin-groups') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.link('admin_groups').'">'.
						__('Groups', 'admin_common').'</a></li>';
					$forum_page['admin_submenu']['ranks'] = '<li class="'.((FORUM_PAGE == 'admin-ranks') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.link('admin_ranks').'">'.
						__('Ranks', 'admin_common').'</a></li>';
					$forum_page['admin_submenu']['bans'] = '<li class="'.((FORUM_PAGE == 'admin-bans') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.link('admin_bans').'">'.
						__('Bans', 'admin_common').'</a></li>';
				}
				else if (FORUM_PAGE_SECTION == 'settings')
				{
					$forum_page['admin_submenu']['settings_setup'] = '<li class="'.((FORUM_PAGE == 'admin-settings-setup') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.link('admin_settings_setup').'">'.
						__('Setup', 'admin_common').'</a></li>';
					$forum_page['admin_submenu']['settings_features'] = '<li class="'.((FORUM_PAGE == 'admin-settings-features') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.link('admin_settings_features').'">'.
						__('Features', 'admin_common').'</a></li>';
					$forum_page['admin_submenu']['settings-announcements'] = '<li class="'.((FORUM_PAGE == 'admin-settings-announcements') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.link('admin_settings_announcements').'">'.
						__('Announcements', 'admin_common').'</a></li>';
					$forum_page['admin_submenu']['settings-email'] = '<li class="'.((FORUM_PAGE == 'admin-settings-email') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.link('admin_settings_email').'">'.
						__('E-mail', 'admin_common').'</a></li>';
					$forum_page['admin_submenu']['settings-registration'] = '<li class="'.((FORUM_PAGE == 'admin-settings-registration') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.link('admin_settings_registration').'">'.
						__('Registration', 'admin_common').'</a></li>';
					$forum_page['admin_submenu']['censoring'] = '<li class="'.((FORUM_PAGE == 'admin-censoring') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.link('admin_censoring').'">'.
						__('Censoring', 'admin_common').'</a></li>';
				}
				else if (FORUM_PAGE_SECTION == 'management')
				{
					$forum_page['admin_submenu']['reports'] = '<li class="'.((FORUM_PAGE == 'admin-reports') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.link('admin_reports').'">'.
						__('Reports', 'admin_common').'</a></li>';
					$forum_page['admin_submenu']['prune'] = '<li class="'.((FORUM_PAGE == 'admin-prune') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.link('admin_prune').'">'.
						__('Prune topics', 'admin_common').'</a></li>';
					$forum_page['admin_submenu']['reindex'] = '<li class="'.((FORUM_PAGE == 'admin-reindex') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.link('admin_reindex').'">'.
						__('Rebuild index', 'admin_common').'</a></li>';
					$forum_page['admin_submenu']['options-maintenance'] = '<li class="'.((FORUM_PAGE == 'admin-settings-maintenance') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.link('admin_settings_maintenance').'">'.
						__('Maintenance mode', 'admin_common').'</a></li>';
				}
				else if (FORUM_PAGE_SECTION == 'extensions')
				{
					$forum_page['admin_submenu']['extensions-manage'] = '<li class="'.((FORUM_PAGE == 'admin-extensions-manage') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.link('admin_extensions_manage').'">'.
						__('Manage extensions', 'admin_common').'</a></li>';
					$forum_page['admin_submenu']['extensions-hotfixes'] = '<li class="'.((FORUM_PAGE == 'admin-extensions-hotfixes') ? 'active' : 'normal').((empty($forum_page['admin_submenu'])) ? ' first-item' : '').'"><a href="'.link('admin_extensions_hotfixes').'">'.
						__('Manage hotfixes', 'admin_common').'</a></li>';
				}
			}

			($hook = get_hook('ca_fn_generate_admin_menu_new_sublink')) ? eval($hook) : null;

			return (!empty($forum_page['admin_submenu'])) ? implode("\n\t\t", $forum_page['admin_submenu']) : '';
		}
		else
		{
			if (user()->g_id != FORUM_ADMIN)
				$forum_page['admin_menu']['index'] = '<li class="active first-item"><a href="'.link('admin_index').'"><span>'.
					__('Moderate', 'admin_common').'</span></a></li>';
			else
			{
				$forum_page['admin_menu']['index'] = '<li class="'.((FORUM_PAGE_SECTION == 'start') ? 'active' : 'normal').((empty($forum_page['admin_menu'])) ? ' first-item' : '').'"><a href="'.link('admin_index').'"><span>'.
					__('Start', 'admin_common').'</span></a></li>';
				$forum_page['admin_menu']['settings_setup'] = '<li class="'.((FORUM_PAGE_SECTION == 'settings') ? 'active' : 'normal').((empty($forum_page['admin_menu'])) ? ' first-item' : '').'"><a href="'.link('admin_settings_setup').'"><span>'.
					__('Settings', 'admin_common').'</span></a></li>';
				$forum_page['admin_menu']['users'] = '<li class="'.((FORUM_PAGE_SECTION == 'users') ? 'active' : 'normal').((empty($forum_page['admin_menu'])) ? ' first-item' : '').'"><a href="'.link('admin_users').'"><span>'.
					__('Users', 'admin_common').'</span></a></li>';
				$forum_page['admin_menu']['reports'] = '<li class="'.((FORUM_PAGE_SECTION == 'management') ? 'active' : 'normal').((empty($forum_page['admin_menu'])) ? ' first-item' : '').'"><a href="'.link('admin_reports').'"><span>'.
					__('Management', 'admin_common').'</span></a></li>';
				$forum_page['admin_menu']['extensions_manage'] = '<li class="'.((FORUM_PAGE_SECTION == 'extensions') ? 'active' : 'normal').((empty($forum_page['admin_menu'])) ? ' first-item' : '').'"><a href="'.link('admin_extensions_manage').'"><span>'.
					__('Extensions', 'admin_common').'</span></a></li>';
			}

			($hook = get_hook('ca_fn_generate_admin_menu_new_link')) ? eval($hook) : null;

			return implode("\n\t\t", $forum_page['admin_menu']);
		}
	}
}

if (substr(FORUM_PAGE, 0, 5) == 'admin' && FORUM_PAGE_TYPE != 'paged') {
	$forum_page['admin_sub'] = generate_admin_menu(true);
?>
	<div class="admin-menu gen-content">
		<ul>
			<?= generate_admin_menu(false) ?>
		</ul>
	</div>
	<?php if ($forum_page['admin_sub'] != '') { ?>
		<div class="admin-submenu gen-content">
			<ul>
			  <?= $forum_page['admin_sub'] ?>
			 </ul>
		</div>
	<?php } ?>
<?php } ?>
