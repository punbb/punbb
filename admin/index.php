<?php
/**
 * Administration panel index page.
 *
 * Gives an overview of some statistics to administrators and moderators.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */
namespace punbb;

require __DIR__ . '/../vendor/pautoload.php';

($hook = get_hook('ain_start')) ? eval($hook) : null;

if (!user()->is_admmod) {
	message(__('No permission'));
}

// Show phpinfo() output
if (isset($_GET['action']) && $_GET['action'] == 'phpinfo' && user()->g_id == FORUM_ADMIN)
{
	($hook = get_hook('ain_phpinfo_selected')) ? eval($hook) : null;

	// Is phpinfo() a disabled function?
	if (strpos(strtolower((string)@ini_get('disable_functions')), 'phpinfo') !== false)
		message(__('phpinfo disabled', 'admin_index'));

	phpinfo();
	exit;
}


// Generate check for updates text block
if (user()->g_id == FORUM_ADMIN) {
	if (config()->o_check_for_updates == '1')
		$punbb_updates = __('Check for updates enabled', 'admin_index');
	else
	{
		// Get a list of installed hotfix extensions
		$query = array(
			'SELECT'	=> 'e.id',
			'FROM'		=> 'extensions AS e',
			'WHERE'		=> 'e.id LIKE \'hotfix_%\''
		);

		($hook = get_hook('ain_update_check_qr_get_hotfixes')) ? eval($hook) : null;
		$result = db()->query_build($query) or error(__FILE__, __LINE__);

		$hotfixes = array();
		while ($row = db()->fetch_assoc($result))
		{
			$hotfixes[] = urlencode($row['id']);
		}

		$punbb_updates = '<a href="http://punbb.informer.com/update/?version='.urlencode(config()->o_cur_version).'&amp;hotfixes='.implode(',', $hotfixes).'">'.
			__('Check for updates manual', 'admin_index') . '</a>';
	}
}


// Get the server load averages (if possible)
if (function_exists('sys_getloadavg') && is_array($load_averages = sys_getloadavg()))
{
	array_walk($load_averages, create_function('&$v', '$v = forum_number_format(round($v, 2), 2);'));
	$server_load = $load_averages[0].' '.$load_averages[1].' '.$load_averages[2];
}
else if (@/**/is_readable('/proc/loadavg'))
{
	// We use @ just in case
	$fh = @/**/fopen('/proc/loadavg', 'r');
	$load_averages = @fread($fh, 64);
	@/**/fclose($fh);

	$load_averages = empty($load_averages) ? array() : explode(' ', $load_averages);
	$server_load = isset($load_averages[2]) ? forum_number_format(round($load_averages[0], 2), 2).' '.forum_number_format(round($load_averages[1], 2), 2).' '.forum_number_format(round($load_averages[2], 2), 2) : 'Not available';
}
else if (!in_array(PHP_OS, array('WINNT', 'WIN32')) && preg_match('/averages?: ([0-9\.]+),[\s]+([0-9\.]+),[\s]+([0-9\.]+)/i', @exec('uptime'), $load_averages))
	$server_load = forum_number_format(round($load_averages[1], 2), 2).' '.forum_number_format(round($load_averages[2], 2), 2).' '.forum_number_format(round($load_averages[3], 2), 2);
else
	$server_load = __('Not available', 'admin_index');


// Get number of current visitors
$query = array(
	'SELECT'	=> 'COUNT(o.user_id)',
	'FROM'		=> 'online AS o',
	'WHERE'		=> 'o.idle=0'
);

($hook = get_hook('ain_qr_get_users_online')) ? eval($hook) : null;
$result = db()->query_build($query) or error(__FILE__, __LINE__);
$num_online = db()->result($result);

// Collect some additional info about MySQL
if (in_array($db_type, array('mysql', 'mysqli', 'mysql_innodb', 'mysqli_innodb')))
{
	// Calculate total db size/row count
	$result = db()->query('SHOW TABLE STATUS FROM `'.$db_name.'` LIKE \''.$db_prefix.'%\'') or error(__FILE__, __LINE__);

	$total_records = $total_size = 0;
	while ($status = db()->fetch_assoc($result))
	{
		$total_records += $status['Rows'];
		$total_size += $status['Data_length'] + $status['Index_length'];
	}

	$total_size = $total_size / 1024;

	if ($total_size > 1024)
		$total_size = forum_number_format($total_size / 1024, 2).' MB';
	else
		$total_size = forum_number_format($total_size, 2).' KB';
}


// Check for the existance of various PHP opcode caches/optimizers
if (function_exists('mmcache'))
	$php_accelerator = '<a href="http://turck-mmcache.sourceforge.net/">Turck MMCache</a>';
else if (isset($_PHPA))
	$php_accelerator = '<a href="http://www.php-accelerator.co.uk/">ionCube PHP Accelerator</a>';
else if (ini_get('apc.enabled'))
	$php_accelerator ='<a href="http://www.php.net/apc/">Alternative PHP Cache (APC)</a>';
else if (ini_get('zend_optimizer.optimization_level'))
	$php_accelerator = '<a href="http://www.zend.com/products/zend_optimizer/">Zend Optimizer</a>';
else if (ini_get('eaccelerator.enable'))
	$php_accelerator = '<a href="http://eaccelerator.net/">eAccelerator</a>';
else if (ini_get('xcache.cacher'))
	$php_accelerator = '<a href="http://xcache.lighttpd.net/">XCache</a>';
else
	$php_accelerator = __('Not applicable', 'admin_index');

// Setup breadcrumbs
$forum_page['crumbs'] = array(
	array(config()->o_board_title, forum_link($forum_url['index'])),
	array(__('Forum administration', 'admin_common'), forum_link($forum_url['admin_index']))
);
if (user()->g_id == FORUM_ADMIN) {
	$forum_page['crumbs'][] = array(__('Start', 'admin_common'), forum_link($forum_url['admin_index']));
}
$forum_page['crumbs'][] = array(__('Information', 'admin_common'), forum_link($forum_url['admin_index']));

($hook = get_hook('ain_pre_header_load')) ? eval($hook) : null;

define('FORUM_PAGE_SECTION', 'start');
define('FORUM_PAGE', 'admin-information');

$forum_page['item_count'] = 0;

$forum_main_view = 'admin/index/main';
template()->render();
