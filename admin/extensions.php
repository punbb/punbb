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


if (!defined('FORUM_ROOT'))
	define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';
require FORUM_ROOT.'include/common_admin.php';

if (!defined('FORUM_XML_FUNCTIONS_LOADED'))
	require FORUM_ROOT.'include/xml.php';

($hook = get_hook('aex_start')) ? eval($hook) : null;

if ($forum_user['g_id'] != FORUM_ADMIN)
	message($lang_common['No permission']);

// Load the admin.php language file
require FORUM_ROOT.'lang/'.$forum_user['language'].'/admin_common.php';
require FORUM_ROOT.'lang/'.$forum_user['language'].'/admin_ext.php';

// Make sure we have XML support
if (!function_exists('xml_parser_create'))
	message($lang_admin_ext['No XML support']);

$section = isset($_GET['section']) ? $_GET['section'] : null;


// Install an extension
if (isset($_GET['install']) || isset($_GET['install_hotfix']))
{
	($hook = get_hook('aex_install_selected')) ? eval($hook) : null;

	// User pressed the cancel button
	if (isset($_POST['install_cancel']))
		redirect(forum_link(isset($_GET['install']) ? $forum_url['admin_extensions_manage'] : $forum_url['admin_extensions_hotfixes']), $lang_admin_common['Cancel redirect']);

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

	if (!empty($errors))
		message(isset($_GET['install']) ? $lang_common['Bad request'] : $lang_admin_ext['Hotfix download failed']);

	// Get core amd major versions
	if (!defined('FORUM_DISABLE_EXTENSIONS_VERSION_CHECK'))
	{
		list($forum_version_core, $forum_version_major) = explode('.', clean_version($forum_config['o_cur_version']));
		list($extension_maxtestedon_version_core, $extension_maxtestedon_version_major) = explode('.', clean_version($ext_data['extension']['maxtestedon']));

		if (version_compare($forum_version_core.'.'.$forum_version_major, $extension_maxtestedon_version_core.'.'.$extension_maxtestedon_version_major, '>'))
			message($lang_admin_ext['Maxtestedon error']);
	}

	// Make sure we have an array of dependencies
	if (!isset($ext_data['extension']['dependencies']['dependency']))
		$ext_data['extension']['dependencies'] = array();
	else if (!is_array(current($ext_data['extension']['dependencies'])))
		$ext_data['extension']['dependencies'] = array($ext_data['extension']['dependencies']['dependency']);
	else
		$ext_data['extension']['dependencies'] = $ext_data['extension']['dependencies']['dependency'];

	$query = array(
		'SELECT'	=> 'e.id',
		'FROM'		=> 'extensions AS e',
		'WHERE'		=> 'e.disabled=0'
	);

	($hook = get_hook('aex_install_check_dependencies')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

	$installed_ext = array();
	while ($row = $forum_db->fetch_assoc($result))
		$installed_ext[] = $row['id'];

	foreach ($ext_data['extension']['dependencies'] as $dependency)
	{
		if (!in_array($dependency, $installed_ext))
			message(sprintf($lang_admin_ext['Missing dependency'], $dependency));
	}

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array($forum_config['o_board_title'], forum_link($forum_url['index'])),
		array($lang_admin_common['Forum administration'], forum_link($forum_url['admin_index'])),
		array($lang_admin_common['Extensions'], forum_link($forum_url['admin_extensions_manage'])),
		array((strpos($id, 'hotfix_') === 0) ? $lang_admin_common['Manage hotfixes'] : $lang_admin_common['Manage extensions'], (strpos($id, 'hotfix_') === 0) ? forum_link($forum_url['admin_extensions_hotfixes']) : forum_link($forum_url['admin_extensions_manage'])),
		(strpos($id, 'hotfix_') === 0) ? $lang_admin_ext['Install hotfix'] : $lang_admin_ext['Install extension']
	);

	if (isset($_POST['install_comply']))
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
		$uninstall_code = (isset($ext_data['extension']['uninstall']) && forum_trim($ext_data['extension']['uninstall']) != '') ? '\''.$forum_db->escape(forum_trim($ext_data['extension']['uninstall'])).'\'' : 'NULL';

		// Is there an uninstall note to store in the db?
		$uninstall_note = 'NULL';
		foreach ($ext_data['extension']['note'] as $cur_note)
		{
			if ($cur_note['attributes']['type'] == 'uninstall' && forum_trim($cur_note['content']) != '')
				$uninstall_note = '\''.$forum_db->escape(forum_trim($cur_note['content'])).'\'';
		}

		$notices = array();

		// Is this a fresh install or an upgrade?
		$query = array(
			'SELECT'	=> 'e.version',
			'FROM'		=> 'extensions AS e',
			'WHERE'		=> 'e.id=\''.$forum_db->escape($id).'\''
		);

		($hook = get_hook('aex_install_comply_qr_get_current_ext_version')) ? eval($hook) : null;
		$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
		$ext_version = $forum_db->result($result);

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
				'SET'		=> 'title=\''.$forum_db->escape($ext_data['extension']['title']).'\', version=\''.$forum_db->escape($ext_data['extension']['version']).'\', description=\''.$forum_db->escape($ext_data['extension']['description']).'\', author=\''.$forum_db->escape($ext_data['extension']['author']).'\', uninstall='.$uninstall_code.', uninstall_note='.$uninstall_note.', dependencies=\'|'.implode('|', $ext_data['extension']['dependencies']).'|\'',
				'WHERE'		=> 'id=\''.$forum_db->escape($id).'\''
			);

			($hook = get_hook('aex_install_comply_qr_update_ext')) ? eval($hook) : null;
			$forum_db->query_build($query) or error(__FILE__, __LINE__);

			// Delete the old hooks
			$query = array(
				'DELETE'	=> 'extension_hooks',
				'WHERE'		=> 'extension_id=\''.$forum_db->escape($id).'\''
			);

			($hook = get_hook('aex_install_comply_qr_update_ext_delete_hooks')) ? eval($hook) : null;
			$forum_db->query_build($query) or error(__FILE__, __LINE__);
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
				'VALUES'	=> '\''.$forum_db->escape($ext_data['extension']['id']).'\', \''.$forum_db->escape($ext_data['extension']['title']).'\', \''.$forum_db->escape($ext_data['extension']['version']).'\', \''.$forum_db->escape($ext_data['extension']['description']).'\', \''.$forum_db->escape($ext_data['extension']['author']).'\', '.$uninstall_code.', '.$uninstall_note.', \'|'.implode('|', $ext_data['extension']['dependencies']).'|\'',
			);

			($hook = get_hook('aex_install_comply_qr_add_ext')) ? eval($hook) : null;
			$forum_db->query_build($query) or error(__FILE__, __LINE__);
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
						'VALUES'	=> '\''.$forum_db->escape(forum_trim($cur_hook)).'\', \''.$forum_db->escape($id).'\', \''.$forum_db->escape(forum_trim($ext_hook['content'])).'\', '.time().', '.(isset($ext_hook['attributes']['priority']) ? $ext_hook['attributes']['priority'] : 5)
					);

					($hook = get_hook('aex_install_comply_qr_add_hook')) ? eval($hook) : null;
					$forum_db->query_build($query) or error(__FILE__, __LINE__);
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
			require FORUM_ROOT.'header.php';

			// START SUBST - <!-- forum_main -->
			ob_start();

			($hook = get_hook('aex_install_notices_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo end($forum_page['crumbs']) ?> "<?php echo forum_htmlencode($ext_data['extension']['title']) ?>"</span></h2>
	</div>
	<div class="main-content main-frm">
		<div class="ct-box info-box">
			<p><?php echo $lang_admin_ext['Extension installed info'] ?></p>
			<ul class="data-list">
<?php

			foreach ($notices as $cur_notice)
				echo "\t\t\t\t".'<li><span>'.$cur_notice.'</span></li>'."\n";

?>
			</ul>
			<p><a href="<?php echo forum_link($forum_url['admin_extensions_manage']) ?>"><?php echo $lang_admin_common['Manage extensions'] ?></a></p>
		</div>
	</div>
<?php

			($hook = get_hook('aex_install_notices_end')) ? eval($hook) : null;

			$tpl_temp = forum_trim(ob_get_contents());
			$tpl_main = str_replace('<!-- forum_main -->', $tpl_temp, $tpl_main);
			ob_end_clean();
			// END SUBST - <!-- forum_main -->

			require FORUM_ROOT.'footer.php';
		}
		else
		{
			// Add flash message
			if (strpos($id, 'hotfix_') === 0)
				$forum_flash->add_info($lang_admin_ext['Hotfix installed']);
			else
				$forum_flash->add_info($lang_admin_ext['Extension installed']);

			($hook = get_hook('aex_install_comply_pre_redirect')) ? eval($hook) : null;

			if (strpos($id, 'hotfix_') === 0)
				redirect(forum_link($forum_url['admin_extensions_hotfixes']), $lang_admin_ext['Hotfix installed']);
			else
				redirect(forum_link($forum_url['admin_extensions_manage']), $lang_admin_ext['Extension installed']);
		}
	}


	($hook = get_hook('aex_install_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE_SECTION', 'extensions');
	if (strpos($id, 'hotfix_') === 0)
		define('FORUM_PAGE', 'admin-extensions-hotfixes');
	else
		define('FORUM_PAGE', 'admin-extensions-manage');

	require FORUM_ROOT.'header.php';

	// START SUBST - <!-- forum_main -->
	ob_start();

	($hook = get_hook('aex_install_output_start')) ? eval($hook) : null;
?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo end($forum_page['crumbs']) ?> "<?php echo forum_htmlencode($ext_data['extension']['title']) ?>"</span></h2>
	</div>
	<div class="main-content main-frm">
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo $base_url.'/admin/extensions.php'.(isset($_GET['install']) ? '?install=' : '?install_hotfix=').$id ?>">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token($base_url.'/admin/extensions.php'.(isset($_GET['install']) ? '?install=' : '?install_hotfix=').$id) ?>" />
			</div>
			<div class="ct-group data-group">
				<div class="ct-set data-set set1">
					<div class="ct-box data-box">
						<h3 class="ct-legend hn"><span><?php echo forum_htmlencode($ext_data['extension']['title']) ?></span></h3>
						<p><?php echo ((strpos($id, 'hotfix_') !== 0) ? sprintf($lang_admin_ext['Version'], $ext_data['extension']['version']) : $lang_admin_ext['Hotfix']) ?></p>
						<p><?php printf($lang_admin_ext['Extension by'], forum_htmlencode($ext_data['extension']['author'])) ?></p>
						<p><?php echo forum_htmlencode($ext_data['extension']['description']) ?></p>
					</div>
				</div>
			</div>
<?php

	// Setup an array of warnings to display in the form
	$form_warnings = array();
	$forum_page['num_items'] = 0;

	foreach ($ext_data['extension']['note'] as $cur_note)
	{
		if ($cur_note['attributes']['type'] == 'install')
			$form_warnings[] = '<li>'.forum_htmlencode($cur_note['content']).'</li>';
	}

	if (version_compare(clean_version($forum_config['o_cur_version']), clean_version($ext_data['extension']['maxtestedon']), '>'))
		$form_warnings[] = '<li>'.$lang_admin_ext['Maxtestedon warning'].'</li>';

	if (!empty($form_warnings))
	{

?>			<div class="ct-box warn-box">
				<p class="important"><strong><?php echo $lang_admin_ext['Install note'] ?></strong></p>
				<ol class="info-list">
<?php

		echo implode("\n\t\t\t\t\t", $form_warnings)."\n";

?>
				</ol>
			</div>
<?php

	}

?>			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="install_comply" value="<?php echo ((strpos($id, 'hotfix_') !== 0) ? $lang_admin_ext['Install extension'] : $lang_admin_ext['Install hotfix']) ?>" /></span>
				<span class="cancel"><input type="submit" name="install_cancel" value="<?php echo $lang_admin_common['Cancel'] ?>" /></span>
			</div>
		</form>
	</div>
<?php

	($hook = get_hook('aex_install_end')) ? eval($hook) : null;

	$tpl_temp = forum_trim(ob_get_contents());
	$tpl_main = str_replace('<!-- forum_main -->', $tpl_temp, $tpl_main);
	ob_end_clean();
	// END SUBST - <!-- forum_main -->

	require FORUM_ROOT.'footer.php';
}


// Uninstall an extension
else if (isset($_GET['uninstall']))
{
	// User pressed the cancel button
	if (isset($_POST['uninstall_cancel']))
		redirect(forum_link($forum_url['admin_extensions_manage']), $lang_admin_common['Cancel redirect']);

	($hook = get_hook('aex_uninstall_selected')) ? eval($hook) : null;

	$id = preg_replace('/[^0-9a-z_]/', '', $_GET['uninstall']);

	// Fetch info about the extension
	$query = array(
		'SELECT'	=> 'e.title, e.version, e.description, e.author, e.uninstall, e.uninstall_note',
		'FROM'		=> 'extensions AS e',
		'WHERE'		=> 'e.id=\''.$forum_db->escape($id).'\''
	);

	($hook = get_hook('aex_uninstall_qr_get_extension')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	$ext_data = $forum_db->fetch_assoc($result);

	if (!$ext_data)
	{
		message($lang_common['Bad request']);
	}

	// Check dependancies
	$query = array(
		'SELECT'	=> 'e.id',
		'FROM'		=> 'extensions AS e',
		'WHERE'		=> 'e.dependencies LIKE \'%|'.$forum_db->escape($id).'|%\''
	);

	($hook = get_hook('aex_uninstall_qr_check_dependencies')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	$dependency = $forum_db->fetch_assoc($result);

	if (!is_null($dependency) && $dependency !== false)
	{
		message(sprintf($lang_admin_ext['Uninstall dependency'], $dependency['id']));
	}

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array($forum_config['o_board_title'], forum_link($forum_url['index'])),
		array($lang_admin_common['Forum administration'], forum_link($forum_url['admin_index'])),
		array($lang_admin_common['Extensions'], forum_link($forum_url['admin_extensions_manage'])),
		array((strpos($id, 'hotfix_') === 0) ? $lang_admin_common['Manage hotfixes'] : $lang_admin_common['Manage extensions'], (strpos($id, 'hotfix_') === 0) ? forum_link($forum_url['admin_extensions_hotfixes']) : forum_link($forum_url['admin_extensions_manage'])),
		(strpos($id, 'hotfix_') === 0) ? $lang_admin_ext['Uninstall hotfix'] : $lang_admin_ext['Uninstall extension']
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
			'WHERE'		=> 'extension_id=\''.$forum_db->escape($id).'\''
		);

		($hook = get_hook('aex_uninstall_comply_qr_uninstall_delete_hooks')) ? eval($hook) : null;
		$forum_db->query_build($query) or error(__FILE__, __LINE__);

		$query = array(
			'DELETE'	=> 'extensions',
			'WHERE'		=> 'id=\''.$forum_db->escape($id).'\''
		);

		($hook = get_hook('aex_uninstall_comply_qr_delete_extension')) ? eval($hook) : null;
		$forum_db->query_build($query) or error(__FILE__, __LINE__);

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
			require FORUM_ROOT.'header.php';

			// START SUBST - <!-- forum_main -->
			ob_start();

			($hook = get_hook('aex_uninstall_notices_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo end($forum_page['crumbs']) ?> "<?php echo forum_htmlencode($ext_data['title']) ?>"</span></h2>
	</div>
	<div class="main-content main-frm">
		<div class="ct-box info-box">
			<p><?php echo $lang_admin_ext['Extension uninstalled info'] ?></p>
			<ul class="info-list">
<?php

			foreach ($notices as $cur_notice)
				echo "\t\t\t\t".'<li><span>'.$cur_notice.'</span></li>'."\n";

?>
			</ul>
			<p><a href="<?php echo forum_link($forum_url['admin_extensions_manage']) ?>"><?php echo $lang_admin_common['Manage extensions'] ?></a></p>
		</div>
	</div>
<?php

			($hook = get_hook('aex_uninstall_notices_end')) ? eval($hook) : null;

			$tpl_temp = forum_trim(ob_get_contents());
			$tpl_main = str_replace('<!-- forum_main -->', $tpl_temp, $tpl_main);
			ob_end_clean();
			// END SUBST - <!-- forum_main -->

			require FORUM_ROOT.'footer.php';
		}
		else
		{
			// Add flash message
			if (strpos($id, 'hotfix_') === 0)
				$forum_flash->add_info($lang_admin_ext['Hotfix uninstalled']);
			else
				$forum_flash->add_info($lang_admin_ext['Extension uninstalled']);

			($hook = get_hook('aex_uninstall_comply_pre_redirect')) ? eval($hook) : null;

			if (strpos($id, 'hotfix_') === 0)
				redirect(forum_link($forum_url['admin_extensions_hotfixes']), $lang_admin_ext['Hotfix uninstalled']);
			else
				redirect(forum_link($forum_url['admin_extensions_manage']), $lang_admin_ext['Extension uninstalled']);
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
		require FORUM_ROOT.'header.php';

		// START SUBST - <!-- forum_main -->
		ob_start();

		($hook = get_hook('aex_uninstall_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo end($forum_page['crumbs']) ?> "<?php echo forum_htmlencode($ext_data['title']) ?>"</span></h2>
	</div>
	<div class="main-content main-frm">
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo $base_url ?>/admin/extensions.php?section=manage&amp;uninstall=<?php echo $id ?>">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token($base_url.'/admin/extensions.php?section=manage&amp;uninstall='.$id) ?>" />
			</div>
			<div class="ct-group data-group">
				<div class="ct-set data-set set1">
					<div class="ct-box data-box">
						<h3 class="ct-legend hn"><span><?php echo forum_htmlencode($ext_data['title']) ?></span></h3>
						<p><?php echo ((strpos($id, 'hotfix_') !== 0) ? sprintf($lang_admin_ext['Version'], $ext_data['version']) : $lang_admin_ext['Hotfix']) ?></p>
						<p><?php printf($lang_admin_ext['Extension by'], forum_htmlencode($ext_data['author'])) ?></p>
						<p><?php echo forum_htmlencode($ext_data['description']) ?></p>
					</div>
				</div>
			</div>
<?php if ($ext_data['uninstall_note'] != ''): ?>			<div class="ct-box warn-box">
				<p class="important"><strong><?php echo $lang_admin_ext['Uninstall note'] ?></strong></p>
				<p><?php echo forum_htmlencode($ext_data['uninstall_note']) ?></p>
			</div>
<?php endif; ?>
<?php if (strpos($id, 'hotfix_') !== 0): ?>			<div class="ct-box warn-box">
				<p class="warn"><?php echo $lang_admin_ext['Installed extensions warn'] ?></p>
			</div>
<?php endif; ?>				<div class="frm-buttons">
				<span class="submit primary caution"><input type="submit" name="uninstall_comply" value="<?php echo $lang_admin_ext['Uninstall'] ?>" /></span>
				<span class="cancel"><input type="submit" name="uninstall_cancel" value="<?php echo $lang_admin_common['Cancel'] ?>" /></span>
			</div>
		</form>
	</div>
<?php

		($hook = get_hook('aex_uninstall_end')) ? eval($hook) : null;

		$tpl_temp = forum_trim(ob_get_contents());
		$tpl_main = str_replace('<!-- forum_main -->', $tpl_temp, $tpl_main);
		ob_end_clean();
		// END SUBST - <!-- forum_main -->

		require FORUM_ROOT.'footer.php';
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
		'WHERE'		=> 'e.id=\''.$forum_db->escape($id).'\''
	);

	($hook = get_hook('aex_flip_qr_get_disabled_status')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	$ext_status = $forum_db->result($result);

	// No rows
	if (is_null($ext_status) || $ext_status === false)
	{
		message($lang_common['Bad request']);
	}

	// Are we disabling or enabling?
	$disable = $ext_status == '0';

	// Check dependancies
	if ($disable)
	{
		$query = array(
			'SELECT'	=> 'e.id',
			'FROM'		=> 'extensions AS e',
			'WHERE'		=> 'e.disabled=0 AND e.dependencies LIKE \'%|'.$forum_db->escape($id).'|%\''
		);

		($hook = get_hook('aex_flip_qr_get_disable_dependencies')) ? eval($hook) : null;
		$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
		$dependency = $forum_db->fetch_assoc($result);

		if (!is_null($dependency) && $dependency !== false)
		{
			message(sprintf($lang_admin_ext['Disable dependency'], $dependency['id']));
		}
	}
	else
	{
		$query = array(
			'SELECT'	=> 'e.dependencies',
			'FROM'		=> 'extensions AS e',
			'WHERE'		=> 'e.id=\''.$forum_db->escape($id).'\''
		);

		($hook = get_hook('aex_flip_qr_get_enable_dependencies')) ? eval($hook) : null;
		$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

		$dependencies = $forum_db->fetch_assoc($result);
		$dependencies = explode('|', substr($dependencies['dependencies'], 1, -1));

		$query = array(
			'SELECT'	=> 'e.id',
			'FROM'		=> 'extensions AS e',
			'WHERE'		=> 'e.disabled=0'
		);

		($hook = get_hook('aex_flip_qr_check_dependencies')) ? eval($hook) : null;
		$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

		$installed_ext = array();
		while ($row = $forum_db->fetch_assoc($result))
			$installed_ext[] = $row['id'];

		foreach ($dependencies as $dependency)
		{
			if (!empty($dependency) && !in_array($dependency, $installed_ext))
				message(sprintf($lang_admin_ext['Disabled dependency'], $dependency));
		}
	}

	$query = array(
		'UPDATE'	=> 'extensions',
		'SET'		=> 'disabled='.($disable ? '1' : '0'),
		'WHERE'		=> 'id=\''.$forum_db->escape($id).'\''
	);

	($hook = get_hook('aex_flip_qr_update_disabled_status')) ? eval($hook) : null;
	$forum_db->query_build($query) or error(__FILE__, __LINE__);

	// Regenerate the hooks cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_hooks_cache();

	// Add flash message
	if ($section == 'hotfixes')
		$forum_flash->add_info(($disable ? $lang_admin_ext['Hotfix disabled'] : $lang_admin_ext['Hotfix enabled']));
	else
		$forum_flash->add_info(($disable ? $lang_admin_ext['Extension disabled'] : $lang_admin_ext['Extension enabled']));

	($hook = get_hook('aex_flip_pre_redirect')) ? eval($hook) : null;

	if ($section == 'hotfixes')
		redirect(forum_link($forum_url['admin_extensions_hotfixes']), ($disable ? $lang_admin_ext['Hotfix disabled'] : $lang_admin_ext['Hotfix enabled']));
	else
		redirect(forum_link($forum_url['admin_extensions_manage']), ($disable ? $lang_admin_ext['Extension disabled'] : $lang_admin_ext['Extension enabled']));
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
$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
while ($cur_ext = $forum_db->fetch_assoc($result))
	$inst_exts[$cur_ext['id']] = $cur_ext;


// Hotfixes list
if ($section == 'hotfixes')
{
	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array($forum_config['o_board_title'], forum_link($forum_url['index'])),
		array($lang_admin_common['Forum administration'], forum_link($forum_url['admin_index'])),
		array($lang_admin_common['Extensions'], forum_link($forum_url['admin_extensions_manage'])),
		array($lang_admin_common['Manage hotfixes'], forum_link($forum_url['admin_extensions_hotfixes']))
	);

	($hook = get_hook('aex_section_hotfixes_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE_SECTION', 'extensions');
	define('FORUM_PAGE', 'admin-extensions-hotfixes');
	require FORUM_ROOT.'header.php';

	// START SUBST - <!-- forum_main -->
	ob_start();

	($hook = get_hook('aex_section_hotfixes_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo $lang_admin_ext['Hotfixes available'] ?></span></h2>
	</div>
	<div class="main-content main-hotfixes">
<?php

	$num_exts = 0;
	$num_failed = 0;
	$forum_page['item_num'] = 1;
	$forum_page['ext_item'] = array();
	$forum_page['ext_error'] = array();

	// Loop through any available hotfixes
	if (isset($forum_updates['hotfix']))
	{
		// If there's only one hotfix, add one layer of arrays so we can foreach over it
		if (!is_array(current($forum_updates['hotfix'])))
			$forum_updates['hotfix'] = array($forum_updates['hotfix']);

		foreach ($forum_updates['hotfix'] as $hotfix)
		{
			if (!array_key_exists($hotfix['attributes']['id'], $inst_exts))
			{
				$forum_page['ext_item'][] = '<div class="ct-box info-box hotfix available">'."\n\t\t\t".'<h3 class="ct-legend hn">'.forum_htmlencode($hotfix['content']).'</h3>'."\n\t\t\t".'<ul>'."\n\t\t\t\t".'<li><span>'.sprintf($lang_admin_ext['Extension by'], 'PunBB').'</span></li>'."\n\t\t\t\t".'<li><span>'.$lang_admin_ext['Hotfix description'].'</span></li>'."\n\t\t\t".'</ul>'."\n\t\t\t\t".'<p class="options"><span class="first-item"><a href="'.$base_url.'/admin/extensions.php?install_hotfix='.urlencode($hotfix['attributes']['id']).'">'.$lang_admin_ext['Install hotfix'].'</a></span></p>'."\n\t\t".'</div>';
				++$num_exts;
			}
		}
	}

	($hook = get_hook('aex_section_hotfixes_pre_display_available_ext_list')) ? eval($hook) : null;

	if ($num_exts)
		echo "\t\t".implode("\n\t\t", $forum_page['ext_item'])."\n";
	else
	{

?>
		<div class="ct-box info-box">
			<p><?php echo $lang_admin_ext['No available hotfixes'] ?></p>
		</div>
<?php

	}

?>
	</div>
<?php

	($hook = get_hook('aex_section_hotfixes_pre_display_installed_ext_list')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo $lang_admin_ext['Installed hotfixes'] ?></span></h2>
	</div>
	<div class="main-content main-hotfixes">
<?php

	$installed_count = 0;
	foreach ($inst_exts as $id => $ext)
	{
		if (strpos($id, 'hotfix_') !== 0)
				continue;

		$forum_page['ext_actions'] = array(
			'flip'		=> '<span class="first-item"><a href="'.$base_url.'/admin/extensions.php?section=hotfixes&amp;flip='.$id.'&amp;csrf_token='.generate_form_token('flip'.$id).'">'.($ext['disabled'] != '1' ? $lang_admin_ext['Disable'] : $lang_admin_ext['Enable']).'</a></span>',
			'uninstall'	=> '<span><a href="'.$base_url.'/admin/extensions.php?section=hotfixese&amp;uninstall='.$id.'">'.$lang_admin_ext['Uninstall'].'</a></span>'
		);

		($hook = get_hook('aex_section_hotfixes_pre_ext_actions')) ? eval($hook) : null;

?>
		<div class="ct-box info-box hotfix <?php echo $ext['disabled'] == '1' ? 'disabled' : 'enabled' ?>">
			<h3 class="ct-legend hn"><span><?php echo forum_htmlencode($ext['title']) ?><?php if ($ext['disabled'] == '1') echo ' ( <span>'.$lang_admin_ext['Extension disabled'].'</span> )' ?></span></h3>
			<ul class="data-list">
				<li><span><?php echo ((strpos($id, 'hotfix_') !== 0) ? sprintf($lang_admin_ext['Version'], $ext['version']) : $lang_admin_ext['Hotfix']) ?></span></li>
				<li><span><?php printf($lang_admin_ext['Extension by'], forum_htmlencode($ext['author'])) ?></span></li>
				<?php if ($ext['description'] != ''): ?>
					<li><span><?php echo forum_htmlencode($ext['description']) ?></span></li>
				<?php endif; ?>
			</ul>
			<p class="options"><?php echo implode(' ', $forum_page['ext_actions']) ?></p>
		</div>
<?php
		$installed_count++;
	}

	if ($installed_count == 0)
	{

?>
		<div class="ct-box info-box">
			<p><?php echo $lang_admin_ext['No installed hotfixes'] ?></p>
		</div>
<?php

	}

?>
	</div>
<?php

	($hook = get_hook('aex_section_hotfixes_end')) ? eval($hook) : null;

	$tpl_temp = forum_trim(ob_get_contents());
	$tpl_main = str_replace('<!-- forum_main -->', $tpl_temp, $tpl_main);
	ob_end_clean();
	// END SUBST - <!-- forum_main -->

	require FORUM_ROOT.'footer.php';
}
// Extensions list
else
{
	if ($forum_config['o_check_for_versions'] == 1)
	{
		// Check for the new versions of the extensions istalled
		$repository_urls = array('http://punbb.informer.com/extensions/1.4');
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
		$update_new_versions_cache = !defined('FORUM_EXT_VERSIONS_LOADED') || (isset($forum_ext_last_versions) && array_diff($inst_exts, $forum_ext_last_versions) != array()) || $update_hour || ($update_hour && isset($min_timestamp) && (time() - $min_timestamp > 60*60*24));

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
		array($lang_admin_common['Forum administration'], forum_link($forum_url['admin_index'])),
		array($lang_admin_common['Extensions'], forum_link($forum_url['admin_extensions_manage'])),
		array($lang_admin_common['Manage extensions'], forum_link($forum_url['admin_extensions_manage']))
	);

	($hook = get_hook('aex_section_manage_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE_SECTION', 'extensions');
	define('FORUM_PAGE', 'admin-extensions-manage');
	require FORUM_ROOT.'header.php';

	// START SUBST - <!-- forum_main -->
	ob_start();

	($hook = get_hook('aex_section_install_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo $lang_admin_ext['Extensions available'] ?></span></h2>
	</div>
	<div class="main-content main-extensions">
<?php

	$num_exts = 0;
	$num_failed = 0;
	$forum_page['item_num'] = 1;
	$forum_page['ext_item'] = array();
	$forum_page['ext_error'] = array();

	$d = dir(FORUM_ROOT.'extensions');
	while (($entry = $d->read()) !== false)
	{
		if ($entry{0} != '.' && is_dir(FORUM_ROOT.'extensions/'.$entry))
		{
			if (preg_match('/[^0-9a-z_]/', $entry))
			{
				$forum_page['ext_error'][] = '<div class="ext-error databox db'.++$forum_page['item_num'].'">'."\n\t\t\t\t".'<h3 class="legend"><span>'.sprintf($lang_admin_ext['Extension loading error'], forum_htmlencode($entry)).'</span></h3>'."\n\t\t\t\t".'<p>'.$lang_admin_ext['Illegal ID'].'</p>'."\n\t\t\t".'</div>';
				++$num_failed;
				continue;
			}
			else if (!file_exists(FORUM_ROOT.'extensions/'.$entry.'/manifest.xml'))
			{
				$forum_page['ext_error'][] = '<div class="ext-error databox db'.++$forum_page['item_num'].'">'."\n\t\t\t\t".'<h3 class="legend"><span>'.sprintf($lang_admin_ext['Extension loading error'], forum_htmlencode($entry)).'<span></h3>'."\n\t\t\t\t".'<p>'.$lang_admin_ext['Missing manifest'].'</p>'."\n\t\t\t".'</div>';
				++$num_failed;
				continue;
			}

			// Parse manifest.xml into an array
			$ext_data = is_readable(FORUM_ROOT.'extensions/'.$entry.'/manifest.xml') ? xml_to_array(file_get_contents(FORUM_ROOT.'extensions/'.$entry.'/manifest.xml')) : '';
			if (empty($ext_data))
			{
				$forum_page['ext_error'][] = '<div class="ext-error databox db'.++$forum_page['item_num'].'">'."\n\t\t\t\t".'<h3 class="legend"><span>'.sprintf($lang_admin_ext['Extension loading error'], forum_htmlencode($entry)).'<span></h3>'."\n\t\t\t\t".'<p>'.$lang_admin_ext['Failed parse manifest'].'</p>'."\n\t\t\t".'</div>';
				++$num_failed;
				continue;
			}

			// Validate manifest
			$errors = validate_manifest($ext_data, $entry);
			if (!empty($errors))
			{
				$forum_page['ext_error'][] = '<div class="ext-error databox db'.++$forum_page['item_num'].'">'."\n\t\t\t\t".'<h3 class="legend"><span>'.sprintf($lang_admin_ext['Extension loading error'], forum_htmlencode($entry)).'</span></h3>'."\n\t\t\t\t".'<p>'.implode(' ', $errors).'</p>'."\n\t\t\t".'</div>';
				++$num_failed;
			}
			else
			{
				if (!array_key_exists($entry, $inst_exts) || version_compare($inst_exts[$entry]['version'], $ext_data['extension']['version'], '!='))
				{
					$forum_page['ext_item'][] = '<div class="ct-box info-box extension available">'."\n\t\t\t".'<h3 class="ct-legend hn">'.forum_htmlencode($ext_data['extension']['title']).' <em>'.$ext_data['extension']['version'].'</em></h3>'."\n\t\t\t".'<ul class="data-list">'."\n\t\t\t\t".'<li><span>'.sprintf($lang_admin_ext['Extension by'], forum_htmlencode($ext_data['extension']['author'])).'</span></li>'.(($ext_data['extension']['description'] != '') ? "\n\t\t\t\t".'<li><span>'.forum_htmlencode($ext_data['extension']['description']).'</span></li>' : '')."\n\t\t\t".'</ul>'."\n\t\t\t".'<p class="options"><span class="first-item"><a href="'.$base_url.'/admin/extensions.php?install='.urlencode($entry).'">'.(isset($inst_exts[$entry]['version']) ? $lang_admin_ext['Upgrade extension'] : $lang_admin_ext['Install extension']).'</a></span></p>'."\n\t\t".'</div>';
					++$num_exts;
				}
			}
		}
	}
	$d->close();

	($hook = get_hook('aex_section_install_pre_display_available_ext_list')) ? eval($hook) : null;

	if ($num_exts)
		echo "\t\t".implode("\n\t\t", $forum_page['ext_item'])."\n";
	else
	{

?>
		<div class="ct-box info-box">
			<p><?php echo $lang_admin_ext['No available extensions'] ?></p>
		</div>
<?php

	}

	// If any of the extensions had errors
	if ($num_failed)
	{

?>
		<div class="ct-box data-box">
			<p class="important"><?php echo $lang_admin_ext['Invalid extensions'] ?></p>
			<?php echo implode("\n\t\t\t", $forum_page['ext_error'])."\n" ?>
		</div>
<?php

	}

?>
	</div>
<?php

	($hook = get_hook('aex_section_manage_pre_display_installed_ext_list')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo $lang_admin_ext['Installed extensions'] ?></span></h2>
	</div>
	<div class="main-content main-extensions">
<?php

	$installed_count = 0;
	$forum_page['ext_item'] = array();
	foreach ($inst_exts as $id => $ext)
	{
		if (strpos($id, 'hotfix_') === 0)
			continue;

		$forum_page['ext_actions'] = array(
			'flip'		=> '<span class="first-item"><a href="'.$base_url.'/admin/extensions.php?section=manage&amp;flip='.$id.'&amp;csrf_token='.generate_form_token('flip'.$id).'">'.($ext['disabled'] != '1' ? $lang_admin_ext['Disable'] : $lang_admin_ext['Enable']).'</a></span>',
			'uninstall'	=> '<span><a href="'.$base_url.'/admin/extensions.php?section=manage&amp;uninstall='.$id.'">'.$lang_admin_ext['Uninstall'].'</a></span>'
		);

		if ($forum_config['o_check_for_versions'] == 1 && isset($forum_ext_last_versions[$id]) && version_compare($ext['version'], $forum_ext_last_versions[$id]['version'], '<'))
			$forum_page['ext_actions']['latest_ver'] = '<span><a href="'.$forum_ext_last_versions[$id]['repo_url'].'/'.$id.'/'.$id.'.zip">'.$lang_admin_ext['Download latest version'].'</a></span>';

		($hook = get_hook('aex_section_manage_pre_ext_actions')) ? eval($hook) : null;

		if ($ext['disabled'] == '1')
			$forum_page['ext_item'][] = '<div class="ct-box info-box extension disabled">'."\n\t\t".'<h3 class="ct-legend hn">'.forum_htmlencode($ext['title']).' <em>'.$ext['version'].'</em> ('.$lang_admin_ext['Extension disabled'].')</h3>'."\n\t\t".'<ul class="data-list">'."\n\t\t\t".'<li><span>'.sprintf($lang_admin_ext['Extension by'], forum_htmlencode($ext['author'])).'</span></li>'."\n\t\t\t".(($ext['description'] != '') ? '<li><span>'.forum_htmlencode($ext['description']).'</span></li>' : '')."\n\t\t\t".'</ul>'."\n\t\t".'<p class="options">'.implode(' ', $forum_page['ext_actions']).'</p>'."\n\t".'</div>';
		else
			$forum_page['ext_item'][] = '<div class="ct-box info-box extension enabled">'."\n\t\t".'<h3 class="ct-legend hn">'.forum_htmlencode($ext['title']).' <em>'.$ext['version'].'</em></h3>'."\n\t\t".'<ul class="data-list">'."\n\t\t\t".'<li><span>'.sprintf($lang_admin_ext['Extension by'], forum_htmlencode($ext['author'])).'</span></li>'."\n\t\t\t".(($ext['description'] != '') ? '<li><span>'.forum_htmlencode($ext['description']).'</span></li>' : '')."\n\t\t".'</ul>'."\n\t\t".'<p class="options">'.implode(' ', $forum_page['ext_actions']).'</p>'."\n\t".'</div>';

		$installed_count++;
	}

	if ($installed_count > 0)
	{
		echo "\t".implode("\n\t", $forum_page['ext_item'])."\n";
	}
	else
	{

?>
		<div class="ct-box info-box">
			<p><?php echo $lang_admin_ext['No installed extensions'] ?></p>
		</div>
<?php

	}

?>
	</div>
<?php

	($hook = get_hook('aex_section_manage_end')) ? eval($hook) : null;

	$tpl_temp = forum_trim(ob_get_contents());
	$tpl_main = str_replace('<!-- forum_main -->', $tpl_temp, $tpl_main);
	ob_end_clean();
	// END SUBST - <!-- forum_main -->

	require FORUM_ROOT.'footer.php';
}
