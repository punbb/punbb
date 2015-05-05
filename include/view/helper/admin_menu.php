<?php
namespace punbb;

if (!function_exists('generate_admin_menu')) {
	function generate_admin_menu($submenu) {
		if ($submenu) {
			$admin_submenu = array();

			if (user()->g_id != FORUM_ADMIN) {
				$admin_submenu['index'] = '<li class="'.((FORUM_PAGE == 'admin-information') ? 'active' : 'normal').
					((empty($admin_submenu)) ? ' first-item' : '').'"><a href="'.link('admin_index').'">'.
					__('Information', 'admin_common') . '</span></a></li>';
				$admin_submenu['users'] = '<li class="'.((FORUM_PAGE == 'admin-users') ? 'active' : 'normal').
					((empty($admin_submenu)) ? ' first-item' : '').'"><a href="'.link('admin_users').'">'.
					__('Searches', 'admin_common').'</a></li>';

				if (config()->o_censoring == '1')
					$admin_submenu['censoring'] = '<li class="'.((FORUM_PAGE == 'admin-censoring') ? 'active' : 'normal').
					((empty($admin_submenu)) ? ' first-item' : '').'"><a href="'.link('admin_censoring').'">'.
						__('Censoring', 'admin_common').'</a></li>';

				$admin_submenu['reports'] = '<li class="'.((FORUM_PAGE == 'admin-reports') ? 'active' : 'normal').
					((empty($admin_submenu)) ? ' first-item' : '').'"><a href="'.link('admin_reports').'">'.
					__('Reports', 'admin_common').'</a></li>';

				if (user()->g_mod_ban_users == '1')
					$admin_submenu['bans'] = '<li class="'.((FORUM_PAGE == 'admin-bans') ? 'active' : 'normal').
					((empty($admin_submenu)) ? ' first-item' : '').'"><a href="'.link('admin_bans').'">'.
						__('Bans', 'admin_common').'</a></li>';
			}
			else {
				if (FORUM_PAGE_SECTION == 'start') {
					$admin_submenu['index'] = '<li class="'.((FORUM_PAGE == 'admin-information') ? 'active' : 'normal').
						((empty($admin_submenu)) ? ' first-item' : '').'"><a href="'.link('admin_index').'">'.
						__('Information', 'admin_common').'</a></li>';
					$admin_submenu['categories'] = '<li class="'.((FORUM_PAGE == 'admin-categories') ? 'active' : 'normal').
						((empty($admin_submenu)) ? ' first-item' : '').'"><a href="'.link('admin_categories').'">'.
						__('Categories', 'admin_common').'</a></li>';
					$admin_submenu['forums'] = '<li class="'.((FORUM_PAGE == 'admin-forums') ? 'active' : 'normal').
						((empty($admin_submenu)) ? ' first-item' : '').'"><a href="'.link('admin_forums').'">'.
						__('Forums', 'admin_common').'</a></li>';
				}
				else if (FORUM_PAGE_SECTION == 'users') {
					$admin_submenu['users'] = '<li class="'.((FORUM_PAGE == 'admin-users' || FORUM_PAGE == 'admin-uresults' || FORUM_PAGE == 'admin-iresults') ? 'active' : 'normal').
						((empty($admin_submenu)) ? ' first-item' : '').'"><a href="'.link('admin_users').'">'.
						__('Searches', 'admin_common').'</a></li>';
					$admin_submenu['groups'] = '<li class="'.((FORUM_PAGE == 'admin-groups') ? 'active' : 'normal').
						((empty($admin_submenu)) ? ' first-item' : '').'"><a href="'.link('admin_groups').'">'.
						__('Groups', 'admin_common').'</a></li>';
					$admin_submenu['ranks'] = '<li class="'.((FORUM_PAGE == 'admin-ranks') ? 'active' : 'normal').
						((empty($admin_submenu)) ? ' first-item' : '').'"><a href="'.link('admin_ranks').'">'.
						__('Ranks', 'admin_common').'</a></li>';
					$admin_submenu['bans'] = '<li class="'.((FORUM_PAGE == 'admin-bans') ? 'active' : 'normal').
						((empty($admin_submenu)) ? ' first-item' : '').'"><a href="'.link('admin_bans').'">'.
						__('Bans', 'admin_common').'</a></li>';
				}
				else if (FORUM_PAGE_SECTION == 'settings') {
					$admin_submenu['settings_setup'] = '<li class="'.((FORUM_PAGE == 'admin-settings-setup') ? 'active' : 'normal').
						((empty($admin_submenu)) ? ' first-item' : '').'"><a href="'.link('admin_settings_setup').'">'.
						__('Setup', 'admin_common').'</a></li>';
					$admin_submenu['settings_features'] = '<li class="'.((FORUM_PAGE == 'admin-settings-features') ? 'active' : 'normal').
						((empty($admin_submenu)) ? ' first-item' : '').'"><a href="'.link('admin_settings_features').'">'.
						__('Features', 'admin_common').'</a></li>';
					$admin_submenu['settings-announcements'] = '<li class="'.((FORUM_PAGE == 'admin-settings-announcements') ? 'active' : 'normal').
						((empty($admin_submenu)) ? ' first-item' : '').'"><a href="'.link('admin_settings_announcements').'">'.
						__('Announcements', 'admin_common').'</a></li>';
					$admin_submenu['settings-email'] = '<li class="'.((FORUM_PAGE == 'admin-settings-email') ? 'active' : 'normal').
						((empty($admin_submenu)) ? ' first-item' : '').'"><a href="'.link('admin_settings_email').'">'.
						__('E-mail', 'admin_common').'</a></li>';
					$admin_submenu['settings-registration'] = '<li class="'.((FORUM_PAGE == 'admin-settings-registration') ? 'active' : 'normal').
						((empty($admin_submenu) ? ' first-item' : '').'"><a href="'.link('admin_settings_registration').'">'.
						__('Registration', 'admin_common').'</a></li>';
					$admin_submenu['censoring'] = '<li class="'.((FORUM_PAGE == 'admin-censoring') ? 'active' : 'normal').
						((empty($admin_submenu)) ? ' first-item' : '').'"><a href="'.link('admin_censoring').'">'.
						__('Censoring', 'admin_common').'</a></li>';
				}
				else if (FORUM_PAGE_SECTION == 'management') {
					$admin_submenu['reports'] = '<li class="'.((FORUM_PAGE == 'admin-reports') ? 'active' : 'normal').
						((empty($admin_submenu)) ? ' first-item' : '').'"><a href="'.link('admin_reports').'">'.
						__('Reports', 'admin_common').'</a></li>';
					$admin_submenu['prune'] = '<li class="'.((FORUM_PAGE == 'admin-prune') ? 'active' : 'normal').
						((empty($admin_submenu)) ? ' first-item' : '').'"><a href="'.link('admin_prune').'">'.
						__('Prune topics', 'admin_common').'</a></li>';
					$admin_submenu['reindex'] = '<li class="'.((FORUM_PAGE == 'admin-reindex') ? 'active' : 'normal').
						((empty($admin_submenu)) ? ' first-item' : '').'"><a href="'.link('admin_reindex').'">'.
						__('Rebuild index', 'admin_common').'</a></li>';
					$admin_submenu['options-maintenance'] = '<li class="'.((FORUM_PAGE == 'admin-settings-maintenance') ? 'active' : 'normal').
						((empty($admin_submenu)) ? ' first-item' : '').'"><a href="'.link('admin_settings_maintenance').'">'.
						__('Maintenance mode', 'admin_common').'</a></li>';
				}
				else if (FORUM_PAGE_SECTION == 'extensions') {
					$admin_submenu['extensions-manage'] = '<li class="'.((FORUM_PAGE == 'admin-extensions-manage') ? 'active' : 'normal').
						((empty($admin_submenu)) ? ' first-item' : '').'"><a href="'.link('admin_extensions_manage').'">'.
						__('Manage extensions', 'admin_common').'</a></li>';
					$admin_submenu['extensions-hotfixes'] = '<li class="'.((FORUM_PAGE == 'admin-extensions-hotfixes') ? 'active' : 'normal').
						((empty($admin_submenu)) ? ' first-item' : '').'"><a href="'.link('admin_extensions_hotfixes').'">'.
						__('Manage hotfixes', 'admin_common').'</a></li>';
				}
			}

			return !empty($admin_submenu)? implode("\n\t\t", $admin_submenu) : '';
		}
		else {
			if (user()->g_id != FORUM_ADMIN) {
				$admin_menu['index'] = '<li class="active first-item"><a href="'.link('admin_index').'"><span>'.
					__('Moderate', 'admin_common').'</span></a></li>';
			}
			else {
				$admin_menu['index'] = '<li class="'.((FORUM_PAGE_SECTION == 'start') ? 'active' : 'normal').
					((empty($admin_menu)) ? ' first-item' : '').'"><a href="'.link('admin_index').'"><span>'.
					__('Start', 'admin_common').'</span></a></li>';
				$admin_menu['settings_setup'] = '<li class="'.((FORUM_PAGE_SECTION == 'settings') ? 'active' : 'normal').
					((empty($admin_menu)) ? ' first-item' : '').'"><a href="'.link('admin_settings_setup').'"><span>'.
					__('Settings', 'admin_common').'</span></a></li>';
				$admin_menu['users'] = '<li class="'.((FORUM_PAGE_SECTION == 'users') ? 'active' : 'normal').
					((empty($admin_menu)) ? ' first-item' : '').'"><a href="'.link('admin_users').'"><span>'.
					__('Users', 'admin_common').'</span></a></li>';
				$admin_menu['reports'] = '<li class="'.((FORUM_PAGE_SECTION == 'management') ? 'active' : 'normal').
					((empty($admin_menu)) ? ' first-item' : '').'"><a href="'.link('admin_reports').'"><span>'.
					__('Management', 'admin_common').'</span></a></li>';
				$admin_menu['extensions_manage'] = '<li class="'.((FORUM_PAGE_SECTION == 'extensions') ? 'active' : 'normal').
					((empty($admin_menu)) ? ' first-item' : '').'"><a href="'.link('admin_extensions_manage').'"><span>'.
					__('Extensions', 'admin_common').'</span></a></li>';
			}

			return implode("\n\t\t", $admin_menu);
		}
	}
}

if (substr(FORUM_PAGE, 0, 5) == 'admin' && FORUM_PAGE_TYPE != 'paged') {
	$admin_sub = generate_admin_menu(true);
?>
	<div class="admin-menu gen-content">
		<ul>
			<?= generate_admin_menu(false) ?>
		</ul>
	</div>
	<?php if ($admin_sub != '') { ?>
		<div class="admin-submenu gen-content">
			<ul>
			  <?= $admin_sub ?>
			 </ul>
		</div>
	<?php } ?>
<?php } ?>
