<?php
/**
 * Extension and hotfix management page.
 *
 * Allows administrators to control the extensions and hotfixes installed in the site.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */

require __DIR__ . '/../vendor/pautoload.php';

if (!defined('FORUM_XML_FUNCTIONS_LOADED'))
	require FORUM_ROOT.'include/xml.php';

($hook = get_hook('aex_start')) ? eval($hook) : null;

if ($forum_user['g_id'] != FORUM_ADMIN)
	message(__('No permission'));

// Make sure we have XML support
if (!function_exists('xml_parser_create'))
	message(__('No XML support', 'admin_ext'));

$section = isset($_GET['section']) ? $_GET['section'] : null;


// Install an extension
if (isset($_GET['install']) || isset($_GET['install_hotfix']))
{
	($hook = get_hook('aex_install_selected')) ? eval($hook) : null;

	// User pressed the cancel button
	if (isset($_POST['install_cancel']))
		redirect(forum_link(isset($_GET['install']) ? $forum_url['admin_extensions_manage'] : $forum_url['admin_extensions_hotfixes']), __('Cancel redirect', 'admin_common'));

	$id = preg_replace('/[^0-9a-z_]/', '', isset($_GET['install']) ? $_GET['install'] : $_GET['install_hotfix']);

	// Load manifest (either locally or from punbb.informer.com updates service)
	if (isset($_GET['install']))
		$manifest = is_readable(FORUM_ROOT.'extensions/'.$id.'/manifest.xml') ? file_get_contents(FORUM_ROOT.'extensions/'.$id.'/manifest.xml') : false;
	else
	{
		$remote_file = get_remote_file('http://punbb.informer.com/update/manifest/'.$id.'.xml', 16);
		if (!empty($remote_file['content']))
			$manifest = $remote_file['content'];
	}

	// Parse manifest.xml into an array and validate it
	$ext_data = xml_to_array($manifest);
	$errors = validate_manifest($ext_data, $id);

	/*
	 * TODO
	 * Errors must be fully specified instead "bad request" message only
	 */
	if (!empty($errors))
		message(isset($_GET['install']) ?
			__('Bad request') : __('Hotfix download failed', 'admin_ext'));

	// Get core amd major versions
	if (!defined('FORUM_DISABLE_EXTENSIONS_VERSION_CHECK'))
	{
		list($forum_version_core, $forum_version_major) = explode('.', clean_version($forum_config['o_cur_version']));
		list($extension_maxtestedon_version_core, $extension_maxtestedon_version_major) = explode('.', clean_version($ext_data['extension']['maxtestedon']));

		if (version_compare($forum_version_core.'.'.$forum_version_major, $extension_maxtestedon_version_core.'.'.$extension_maxtestedon_version_major, '>'))
			message(__('Maxtestedon error', 'admin_ext'));
	}

	// Make sure we have an array of dependencies
	if (!isset($ext_data['extension']['dependencies']['dependency']))
		$ext_data['extension']['dependencies'] = array();
	else if (!is_array(current($ext_data['extension']['dependencies'])))
		$ext_data['extension']['dependencies'] = array($ext_data['extension']['dependencies']['dependency']);
	else
		$ext_data['extension']['dependencies'] = $ext_data['extension']['dependencies']['dependency'];

	$query = array(
		'SELECT'	=> 'e.id, e.version',
		'FROM'		=> 'extensions AS e',
		'WHERE'		=> 'e.disabled=0'
	);

	($hook = get_hook('aex_install_check_dependencies')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);

	$installed_ext = array();
	while ($row = db()->fetch_assoc($result))
		$installed_ext[$row['id']] = $row;

	foreach ($ext_data['extension']['dependencies'] as $dependency)
	{

		$ext_dependancy_id = is_array($dependency) ? $dependency['content'] : $dependency;

	    if (!array_key_exists($ext_dependancy_id, $installed_ext))
	    {
		   $errors[] = sprintf(__('Missing dependency', 'admin_ext'), $ext_dependancy_id);
	    }
	    else if (is_array($dependency) AND isset($dependency['attributes']['minversion']) AND version_compare($dependency['attributes']['minversion'], $installed_ext[$ext_dependancy_id]['version']) > 0)
	    {
	    	$errors[] = sprintf(__('Version dependency error', 'admin_ext'), $dependency['content'], $dependency['attributes']['minversion']);
	    }
	}

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array($forum_config['o_board_title'], forum_link($forum_url['index'])),
		array(__('Forum administration', 'admin_common'), forum_link($forum_url['admin_index'])),
		array(__('Extensions', 'admin_common'), forum_link($forum_url['admin_extensions_manage'])),
		array((strpos($id, 'hotfix_') === 0) ? __('Manage hotfixes', 'admin_common') : __('Manage extensions', 'admin_common'), (strpos($id, 'hotfix_') === 0) ? forum_link($forum_url['admin_extensions_hotfixes']) : forum_link($forum_url['admin_extensions_manage'])),
		(strpos($id, 'hotfix_') === 0) ? __('Install hotfix', 'admin_ext') :
			__('Install extension', 'admin_ext')
	);

	if (isset($_POST['install_comply']) AND empty($errors))
	{
		($hook = get_hook('aex_install_comply_form_submitted')) ? eval($hook) : null;

		// $ext_info contains some information about the extension being installed
		$ext_info = array(
			'id'			=> $id,
			'path'			=> FORUM_ROOT.'extensions/'.$id,
			'url'			=> $base_url.'/extensions/'.$id,
			'dependencies'	=> array()
		);

		foreach ($ext_data['extension']['dependencies'] as $dependency)
		{
			$ext_info['dependencies'][$dependency] = array(
				'id'	=> $dependency,
				'path'	=> FORUM_ROOT.'extensions/'.$dependency,
				'url'	=> $base_url.'/extensions/'.$dependency,
			);
		}

		// Is there some uninstall code to store in the db?
		$uninstall_code = (isset($ext_data['extension']['uninstall']) && forum_trim($ext_data['extension']['uninstall']) != '') ? '\''.db()->escape(forum_trim($ext_data['extension']['uninstall'])).'\'' : 'NULL';

		// Is there an uninstall note to store in the db?
		$uninstall_note = 'NULL';
		foreach ($ext_data['extension']['note'] as $cur_note)
		{
			if ($cur_note['attributes']['type'] == 'uninstall' && forum_trim($cur_note['content']) != '')
				$uninstall_note = '\''.db()->escape(forum_trim($cur_note['content'])).'\'';
		}

		$notices = array();

		// Is this a fresh install or an upgrade?
		$query = array(
			'SELECT'	=> 'e.version',
			'FROM'		=> 'extensions AS e',
			'WHERE'		=> 'e.id=\''.db()->escape($id).'\''
		);

		($hook = get_hook('aex_install_comply_qr_get_current_ext_version')) ? eval($hook) : null;
		$result = db()->query_build($query) or error(__FILE__, __LINE__);
		$ext_version = db()->result($result);

		if (!is_null($ext_version) && $ext_version !== false)
		{
			// EXT_CUR_VERSION will be available to the extension install routine (to facilitate extension upgrades)
			define('EXT_CUR_VERSION', $ext_version);

			// Run the author supplied install code
			if (isset($ext_data['extension']['install']) && forum_trim($ext_data['extension']['install']) != '')
				eval($ext_data['extension']['install']);

			// Update the existing extension
			$query = array(
				'UPDATE'	=> 'extensions',
				'SET'		=> 'title=\''.db()->escape($ext_data['extension']['title']).'\', version=\''.db()->escape($ext_data['extension']['version']).'\', description=\''.db()->escape($ext_data['extension']['description']).'\', author=\''.db()->escape($ext_data['extension']['author']).'\', uninstall='.$uninstall_code.', uninstall_note='.$uninstall_note.', dependencies=\'|'.implode('|', $ext_data['extension']['dependencies']).'|\'',
				'WHERE'		=> 'id=\''.db()->escape($id).'\''
			);

			($hook = get_hook('aex_install_comply_qr_update_ext')) ? eval($hook) : null;
			db()->query_build($query) or error(__FILE__, __LINE__);

			// Delete the old hooks
			$query = array(
				'DELETE'	=> 'extension_hooks',
				'WHERE'		=> 'extension_id=\''.db()->escape($id).'\''
			);

			($hook = get_hook('aex_install_comply_qr_update_ext_delete_hooks')) ? eval($hook) : null;
			db()->query_build($query) or error(__FILE__, __LINE__);
		}
		else
		{
			// Run the author supplied install code
			if (isset($ext_data['extension']['install']) && forum_trim($ext_data['extension']['install']) != '')
				eval($ext_data['extension']['install']);

			// Add the new extension
			$query = array(
				'INSERT'	=> 'id, title, version, description, author, uninstall, uninstall_note, dependencies',
				'INTO'		=> 'extensions',
				'VALUES'	=> '\''.db()->escape($ext_data['extension']['id']).'\', \''.db()->escape($ext_data['extension']['title']).'\', \''.db()->escape($ext_data['extension']['version']).'\', \''.db()->escape($ext_data['extension']['description']).'\', \''.db()->escape($ext_data['extension']['author']).'\', '.$uninstall_code.', '.$uninstall_note.', \'|'.implode('|', $ext_data['extension']['dependencies']).'|\'',
			);

			($hook = get_hook('aex_install_comply_qr_add_ext')) ? eval($hook) : null;
			db()->query_build($query) or error(__FILE__, __LINE__);
		}

		// Now insert the hooks
		if (isset($ext_data['extension']['hooks']['hook']))
		{
			foreach ($ext_data['extension']['hooks']['hook'] as $ext_hook)
			{
				$cur_hooks = explode(',', $ext_hook['attributes']['id']);
				foreach ($cur_hooks as $cur_hook)
				{
					$query = array(
						'INSERT'	=> 'id, extension_id, code, installed, priority',
						'INTO'		=> 'extension_hooks',
						'VALUES'	=> '\''.db()->escape(forum_trim($cur_hook)).'\', \''.db()->escape($id).'\', \''.db()->escape(forum_trim($ext_hook['content'])).'\', '.time().', '.(isset($ext_hook['attributes']['priority']) ? $ext_hook['attributes']['priority'] : 5)
					);

					($hook = get_hook('aex_install_comply_qr_add_hook')) ? eval($hook) : null;
					db()->query_build($query) or error(__FILE__, __LINE__);
				}
			}
		}

		// Empty the PHP cache
		forum_clear_cache();

		// Regenerate the hooks cache
		if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
			require FORUM_ROOT.'include/cache.php';

		generate_hooks_cache();

		// Display notices if there are any
		if (!empty($notices))
		{
			($hook = get_hook('aex_install_notices_pre_header_load')) ? eval($hook) : null;

			define('FORUM_PAGE_SECTION', 'extensions');
			if (strpos($id, 'hotfix_') === 0)
				define('FORUM_PAGE', 'admin-extensions-hotfixes');
			else
				define('FORUM_PAGE', 'admin-extensions-manage');

			$forum_main_view = 'admin/ext/install_notices';
			include FORUM_ROOT . 'include/render.php';
		}
		else
		{
			// Add flash message
			if (strpos($id, 'hotfix_') === 0)
				flash()->add_info(__('Hotfix installed', 'admin_ext'));
			else
				flash()->add_info(__('Extension installed', 'admin_ext'));

			($hook = get_hook('aex_install_comply_pre_redirect')) ? eval($hook) : null;

			if (strpos($id, 'hotfix_') === 0)
				redirect(forum_link($forum_url['admin_extensions_hotfixes']), __('Hotfix installed', 'admin_ext'));
			else
				redirect(forum_link($forum_url['admin_extensions_manage']), __('Extension installed', 'admin_ext'));
		}
	}


	($hook = get_hook('aex_install_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE_SECTION', 'extensions');
	if (strpos($id, 'hotfix_') === 0)
		define('FORUM_PAGE', 'admin-extensions-hotfixes');
	else
		define('FORUM_PAGE', 'admin-extensions-manage');

	$forum_main_view = 'admin/ext/install';
	include FORUM_ROOT . 'include/render.php';
}


// Uninstall an extension
else if (isset($_GET['uninstall']))
{
	// User pressed the cancel button
	if (isset($_POST['uninstall_cancel']))
		redirect(forum_link($forum_url['admin_extensions_manage']), __('Cancel redirect', 'admin_common'));

	($hook = get_hook('aex_uninstall_selected')) ? eval($hook) : null;

	$id = preg_replace('/[^0-9a-z_]/', '', $_GET['uninstall']);

	// Fetch info about the extension
	$query = array(
		'SELECT'	=> 'e.title, e.version, e.description, e.author, e.uninstall, e.uninstall_note',
		'FROM'		=> 'extensions AS e',
		'WHERE'		=> 'e.id=\''.db()->escape($id).'\''
	);

	($hook = get_hook('aex_uninstall_qr_get_extension')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);
	$ext_data = db()->fetch_assoc($result);

	if (!$ext_data)
	{
		message(__('Bad request'));
	}

	// Check dependancies
	$query = array(
		'SELECT'	=> 'e.id',
		'FROM'		=> 'extensions AS e',
		'WHERE'		=> 'e.dependencies LIKE \'%|'.db()->escape($id).'|%\''
	);

	($hook = get_hook('aex_uninstall_qr_check_dependencies')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);
	$dependency = db()->fetch_assoc($result);

	if (!is_null($dependency) && $dependency !== false)
	{
		message(sprintf(__('Uninstall dependency', 'admin_ext'), $dependency['id']));
	}

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array($forum_config['o_board_title'], forum_link($forum_url['index'])),
		array(__('Forum administration', 'admin_common'), forum_link($forum_url['admin_index'])),
		array(__('Extensions', 'admin_common'), forum_link($forum_url['admin_extensions_manage'])),
		array((strpos($id, 'hotfix_') === 0) ? __('Manage hotfixes', 'admin_common') : __('Manage extensions', 'admin_common'), (strpos($id, 'hotfix_') === 0) ? forum_link($forum_url['admin_extensions_hotfixes']) : forum_link($forum_url['admin_extensions_manage'])),
		(strpos($id, 'hotfix_') === 0) ? __('Uninstall hotfix', 'admin_ext') : __('Uninstall extension', 'admin_ext')
	);

	// If the user has confirmed the uninstall
	if (isset($_POST['uninstall_comply']))
	{
		($hook = get_hook('aex_uninstall_comply_form_submitted')) ? eval($hook) : null;

		$ext_info = array(
			'id'			=> $id,
			'path'			=> FORUM_ROOT.'extensions/'.$id,
			'url'			=> $base_url.'/extensions/'.$id
		);

		$notices = array();

		// Run uninstall code
		eval($ext_data['uninstall']);

		// Now delete the extension and its hooks from the db
		$query = array(
			'DELETE'	=> 'extension_hooks',
			'WHERE'		=> 'extension_id=\''.db()->escape($id).'\''
		);

		($hook = get_hook('aex_uninstall_comply_qr_uninstall_delete_hooks')) ? eval($hook) : null;
		db()->query_build($query) or error(__FILE__, __LINE__);

		$query = array(
			'DELETE'	=> 'extensions',
			'WHERE'		=> 'id=\''.db()->escape($id).'\''
		);

		($hook = get_hook('aex_uninstall_comply_qr_delete_extension')) ? eval($hook) : null;
		db()->query_build($query) or error(__FILE__, __LINE__);

		// Empty the PHP cache
		forum_clear_cache();

		// Regenerate the hooks cache
		if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
			require FORUM_ROOT.'include/cache.php';

		generate_hooks_cache();

		// Display notices if there are any
		if (!empty($notices))
		{
			($hook = get_hook('aex_uninstall_notices_pre_header_load')) ? eval($hook) : null;

			define('FORUM_PAGE_SECTION', 'extensions');
			define('FORUM_PAGE', 'admin-extensions-manage');

			$forum_main_view = 'admin/ext/uninstall_notices';
			include FORUM_ROOT . 'include/render.php';
		}
		else
		{
			// Add flash message
			if (strpos($id, 'hotfix_') === 0)
				flash()->add_info(__('Hotfix uninstalled', 'admin_ext'));
			else
				flash()->add_info(__('Extension uninstalled', 'admin_ext'));

			($hook = get_hook('aex_uninstall_comply_pre_redirect')) ? eval($hook) : null;

			if (strpos($id, 'hotfix_') === 0)
				redirect(forum_link($forum_url['admin_extensions_hotfixes']), __('Hotfix uninstalled', 'admin_ext'));
			else
				redirect(forum_link($forum_url['admin_extensions_manage']), __('Extension uninstalled', 'admin_ext'));
		}
	}
	else	// If the user hasn't confirmed the uninstall
	{
		($hook = get_hook('aex_uninstall_pre_header_load')) ? eval($hook) : null;

		define('FORUM_PAGE_SECTION', 'extensions');
		if (strpos($id, 'hotfix_') === 0)
			define('FORUM_PAGE', 'admin-extensions-hotfixes');
		else
			define('FORUM_PAGE', 'admin-extensions-manage');

		$forum_main_view = 'admin/ext/uninstall';
		include FORUM_ROOT . 'include/render.php';
	}
}


// Enable or disable an extension
else if (isset($_GET['flip']))
{
	$id = preg_replace('/[^0-9a-z_]/', '', $_GET['flip']);

	// We validate the CSRF token. If it's set in POST and we're at this point, the token is valid.
	// If it's in GET, we need to make sure it's valid.
	if (!isset($_POST['csrf_token']) && (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== generate_form_token('flip'.$id)))
		csrf_confirm_form();

	($hook = get_hook('aex_flip_selected')) ? eval($hook) : null;

	// Fetch the current status of the extension
	$query = array(
		'SELECT'	=> 'e.disabled',
		'FROM'		=> 'extensions AS e',
		'WHERE'		=> 'e.id=\''.db()->escape($id).'\''
	);

	($hook = get_hook('aex_flip_qr_get_disabled_status')) ? eval($hook) : null;
	$result = db()->query_build($query) or error(__FILE__, __LINE__);
	$ext_status = db()->result($result);

	// No rows
	if (is_null($ext_status) || $ext_status === false)
	{
		message(__('Bad request'));
	}

	// Are we disabling or enabling?
	$disable = $ext_status == '0';

	// Check dependancies
	if ($disable)
	{
		$query = array(
			'SELECT'	=> 'e.id',
			'FROM'		=> 'extensions AS e',
			'WHERE'		=> 'e.disabled=0 AND e.dependencies LIKE \'%|'.db()->escape($id).'|%\''
		);

		($hook = get_hook('aex_flip_qr_get_disable_dependencies')) ? eval($hook) : null;
		$result = db()->query_build($query) or error(__FILE__, __LINE__);
		$dependency = db()->fetch_assoc($result);

		if (!is_null($dependency) && $dependency !== false)
		{
			message(sprintf(__('Disable dependency', 'admin_ext'), $dependency['id']));
		}
	}
	else
	{
		$query = array(
			'SELECT'	=> 'e.dependencies',
			'FROM'		=> 'extensions AS e',
			'WHERE'		=> 'e.id=\''.db()->escape($id).'\''
		);

		($hook = get_hook('aex_flip_qr_get_enable_dependencies')) ? eval($hook) : null;
		$result = db()->query_build($query) or error(__FILE__, __LINE__);

		$dependencies = db()->fetch_assoc($result);
		$dependencies = explode('|', substr($dependencies['dependencies'], 1, -1));

		$query = array(
			'SELECT'	=> 'e.id',
			'FROM'		=> 'extensions AS e',
			'WHERE'		=> 'e.disabled=0'
		);

		($hook = get_hook('aex_flip_qr_check_dependencies')) ? eval($hook) : null;
		$result = db()->query_build($query) or error(__FILE__, __LINE__);

		$installed_ext = array();
		while ($row = db()->fetch_assoc($result))
			$installed_ext[] = $row['id'];

		foreach ($dependencies as $dependency)
		{
			if (!empty($dependency) && !in_array($dependency, $installed_ext))
				message(sprintf(__('Disabled dependency', 'admin_ext'), $dependency));
		}
	}

	$query = array(
		'UPDATE'	=> 'extensions',
		'SET'		=> 'disabled='.($disable ? '1' : '0'),
		'WHERE'		=> 'id=\''.db()->escape($id).'\''
	);

	($hook = get_hook('aex_flip_qr_update_disabled_status')) ? eval($hook) : null;
	db()->query_build($query) or error(__FILE__, __LINE__);

	// Regenerate the hooks cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_hooks_cache();

	// Add flash message
	if ($section == 'hotfixes')
		flash()->add_info(($disable ? __('Hotfix disabled', 'admin_ext') : __('Hotfix enabled', 'admin_ext')));
	else
		flash()->add_info(($disable ? __('Extension disabled', 'admin_ext') : __('Extension enabled', 'admin_ext')));

	($hook = get_hook('aex_flip_pre_redirect')) ? eval($hook) : null;

	if ($section == 'hotfixes')
		redirect(forum_link($forum_url['admin_extensions_hotfixes']), ($disable ? __('Hotfix disabled', 'admin_ext') : __('Hotfix enabled', 'admin_ext')));
	else
		redirect(forum_link($forum_url['admin_extensions_manage']), ($disable ? __('Extension disabled', 'admin_ext') : __('Extension enabled', 'admin_ext')));
}

($hook = get_hook('aex_new_action')) ? eval($hook) : null;


// Generate an array of installed extensions
$inst_exts = array();
$query = array(
	'SELECT'	=> 'e.*',
	'FROM'		=> 'extensions AS e',
	'ORDER BY'	=> 'e.title'
);

($hook = get_hook('aex_qr_get_all_extensions')) ? eval($hook) : null;
$result = db()->query_build($query) or error(__FILE__, __LINE__);
while ($cur_ext = db()->fetch_assoc($result))
	$inst_exts[$cur_ext['id']] = $cur_ext;


// Hotfixes list
if ($section == 'hotfixes')
{
	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array($forum_config['o_board_title'], forum_link($forum_url['index'])),
		array(__('Forum administration', 'admin_common'), forum_link($forum_url['admin_index'])),
		array(__('Extensions', 'admin_common'), forum_link($forum_url['admin_extensions_manage'])),
		array(__('Manage hotfixes', 'admin_common'), forum_link($forum_url['admin_extensions_hotfixes']))
	);

	($hook = get_hook('aex_section_hotfixes_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE_SECTION', 'extensions');
	define('FORUM_PAGE', 'admin-extensions-hotfixes');

	$forum_main_view = 'admin/ext/hotfixes';
	include FORUM_ROOT . 'include/render.php';
}
// Extensions list
else
{
	if ($forum_config['o_check_for_versions'] == 1)
	{
		// Check for the new versions of the extensions istalled
		$repository_urls = array(FORUM_PUN_EXTENSION_REPOSITORY_URL);
		($hook = get_hook('aex_add_extensions_repository')) ? eval($hook) : null;

		$repository_url_by_extension = array();
		foreach (array_keys($inst_exts) as $id)
			($hook = get_hook('aex_add_repository_for_'.$id)) ? eval($hook) : null;

		if (is_readable(FORUM_CACHE_DIR.'cache_ext_version_notifications.php'))
			include FORUM_CACHE_DIR.'cache_ext_version_notifications.php';

		// Get latest timestamp in cache
		if (isset($forum_ext_repos))
		{
			$min_timestamp = 10000000000;
			foreach ($forum_ext_repos as $rep)
				$min_timestamp = min($min_timestamp, $rep['timestamp']);
		}

		$update_hour = (isset($forum_ext_versions_update_cache) && (time() - $forum_ext_versions_update_cache > 60 * 60));

		// Update last versions if there is no cahe or some extension was added/removed or one day has gone since last update
		$update_new_versions_cache = !defined('FORUM_EXT_VERSIONS_LOADED') || (isset($forum_ext_last_versions) && array_diff(array_keys($inst_exts), array_keys($forum_ext_last_versions)) != array()) || $update_hour || ($update_hour && isset($min_timestamp) && (time() - $min_timestamp > 60*60*24));

		($hook = get_hook('aex_before_update_checking')) ? eval($hook) : null;

		if ($update_new_versions_cache)
		{
			if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
				require_once FORUM_ROOT.'include/cache.php';

			generate_ext_versions_cache($inst_exts, $repository_urls, $repository_url_by_extension);
			include FORUM_CACHE_DIR.'cache_ext_version_notifications.php';
		}
	}

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array($forum_config['o_board_title'], forum_link($forum_url['index'])),
		array(__('Forum administration', 'admin_common'), forum_link($forum_url['admin_index'])),
		array(__('Extensions', 'admin_common'), forum_link($forum_url['admin_extensions_manage'])),
		array(__('Manage extensions', 'admin_common'), forum_link($forum_url['admin_extensions_manage']))
	);

	($hook = get_hook('aex_section_manage_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE_SECTION', 'extensions');
	define('FORUM_PAGE', 'admin-extensions-manage');

	$forum_main_view = 'admin/ext/manage';
	include FORUM_ROOT . 'include/render.php';
}
