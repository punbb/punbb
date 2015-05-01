<?php
/**
 * Installation script.
 *
 * Used to actually install PunBB.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */


define('MIN_PHP_VERSION', '5.0.0');
define('MIN_MYSQL_VERSION', '4.1.2');

define('FORUM_ROOT', '../');
define('FORUM', 1);
define('FORUM_DEBUG', 1);

if (file_exists(FORUM_ROOT.'config.php'))
	exit('The file \'config.php\' already exists which would mean that PunBB is already installed. You should go <a href="'.FORUM_ROOT.'index.php">here</a> instead.');


// Make sure we are running at least MIN_PHP_VERSION
if (!function_exists('version_compare') || version_compare(PHP_VERSION, MIN_PHP_VERSION, '<'))
	exit('You are running PHP version '.PHP_VERSION.'. PunBB requires at least PHP '.MIN_PHP_VERSION.' to run properly. You must upgrade your PHP installation before you can continue.');

// Disable error reporting for uninitialized variables
error_reporting(E_ALL);

// Turn off PHP time limit
@set_time_limit(0);

// Strip out "bad" UTF-8 characters
forum_remove_bad_characters();

//
// Generate output to be used for config.php
//
function generate_config_file()
{
	global $db_type, $db_host, $db_name, $db_username, $db_password, $db_prefix, $base_url, $cookie_name;

	$config_body = '<?php'."\n\n".'$db_type = \''.$db_type."';\n".'$db_host = \''.$db_host."';\n".'$db_name = \''.addslashes($db_name)."';\n".'$db_username = \''.addslashes($db_username)."';\n".'$db_password = \''.addslashes($db_password)."';\n".'$db_prefix = \''.addslashes($db_prefix)."';\n".'$p_connect = false;'."\n\n".'$base_url = \''.$base_url.'\';'."\n\n".'$cookie_name = '."'".$cookie_name."';\n".'$cookie_domain = '."'';\n".'$cookie_path = '."'/';\n".'$cookie_secure = 0;'."\n\ndefine('FORUM', 1);";

	// Add forum options
	$config_body .= "\n\n// Enable DEBUG mode by removing // from the following line\n//define('FORUM_DEBUG', 1);";
	$config_body .= "\n\n// Enable show DB Queries mode by removing // from the following line\n//define('FORUM_SHOW_QUERIES', 1);";
	$config_body .= "\n\n// Enable forum IDNA support by removing // from the following line\n//define('FORUM_ENABLE_IDNA', 1);";
	$config_body .= "\n\n// Disable forum CSRF checking by removing // from the following line\n//define('FORUM_DISABLE_CSRF_CONFIRM', 1);";
	$config_body .= "\n\n// Disable forum hooks (extensions) by removing // from the following line\n//define('FORUM_DISABLE_HOOKS', 1);";
	$config_body .= "\n\n// Disable forum output buffering by removing // from the following line\n//define('FORUM_DISABLE_BUFFERING', 1);";
	$config_body .= "\n\n// Disable forum async JS loader by removing // from the following line\n//define('FORUM_DISABLE_ASYNC_JS_LOADER', 1);";
	$config_body .= "\n\n// Disable forum extensions version check by removing // from the following line\n//define('FORUM_DISABLE_EXTENSIONS_VERSION_CHECK', 1);";

	return $config_body;
}

$language = isset($_GET['lang']) ? $_GET['lang'] : (isset($_POST['req_language']) ? forum_trim($_POST['req_language']) : 'English');
$language = preg_replace('#[\.\\\/]#', '', $language);
if (!file_exists(language()->path[$language] . '/install.php')) {
	exit('The language pack you have chosen doesn\'t seem to exist or is corrupt. Please recheck and try again.');
}

if (isset($_POST['generate_config'])) {
	header('Content-Type: text/x-delimtext; name="config.php"');
	header('Content-disposition: attachment; filename=config.php');

	$db_type = $_POST['db_type'];
	$db_host = $_POST['db_host'];
	$db_name = $_POST['db_name'];
	$db_username = $_POST['db_username'];
	$db_password = $_POST['db_password'];
	$db_prefix = $_POST['db_prefix'];
	$base_url = $_POST['base_url'];
	$cookie_name = $_POST['cookie_name'];

	echo generate_config_file();
	exit;
}

header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: cache-control: no-store', false);

if (!isset($_POST['form_sent']))
{
	// Determine available database extensions
	$db_extensions = array();

	if (function_exists('mysqli_connect'))
	{
		$db_extensions[] = array('mysqli', 'MySQL Improved');
		$db_extensions[] = array('mysqli_innodb', 'MySQL Improved (InnoDB)');
	}

	if (function_exists('mysql_connect'))
	{
		$db_extensions[] = array('mysql', 'MySQL Standard');
		$db_extensions[] = array('mysql_innodb', 'MySQL Standard (InnoDB)');
	}

	if (function_exists('sqlite_open'))
		$db_extensions[] = array('sqlite', 'SQLite');

	if (class_exists('SQLite3'))
		$db_extensions[] = array('sqlite3', 'SQLite3');

	if (function_exists('pg_connect'))
		$db_extensions[] = array('pgsql', 'PostgreSQL');

	if (empty($db_extensions))
		error(__('No database support', 'install'));

	// Make an educated guess regarding base_url
	$base_url_guess = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://').preg_replace('/:80$/', '', $_SERVER['HTTP_HOST']).substr(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), 0, -6);
	if (substr($base_url_guess, -1) == '/')
		$base_url_guess = substr($base_url_guess, 0, -1);

	// Check for available language packs
	$languages = get_language_packs();

?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html class="oldie ie6" lang="en" dir="ltr"> <![endif]-->
<!--[if IE 7 ]>    <html class="oldie ie7" lang="en" dir="ltr"> <![endif]-->
<!--[if IE 8 ]>    <html class="oldie ie8" lang="en" dir="ltr"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="en" dir="ltr"> <!--<![endif]-->
<head>
	<meta charset="utf-8" />
	<title>PunBB Installation</title>
	<link rel="stylesheet" type="text/css"
		href="<?= theme()->url['Oxygen'] ?>/Oxygen.min.css" />
</head>
<body>
<div id="brd-install" class="brd-page">
<div id="brd-wrap" class="brd">

<div id="brd-head" class="gen-content">
	<p id="brd-title"><strong><?php printf(__('Install PunBB', 'install'), FORUM_VERSION) ?></strong></p>
	<p id="brd-desc"><?php echo __('Install intro', 'install') ?></p>
</div>

<div id="brd-main" class="main">

	<div class="main-head">
		<h1 class="hn"><span><?php printf(__('Install PunBB', 'install'), FORUM_VERSION) ?></span></h1>
	</div>

<?php

	if (count($languages) > 1)
	{

?>	<form class="frm-form" method="get" accept-charset="utf-8" action="install.php">
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo __('Choose language', 'install') ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<fieldset class="frm-group group1">
			<legend class="group-legend"><strong><?php echo __('Choose language legend', 'install') ?></strong></legend>
			<div class="sf-set set1">
				<div class="sf-box text">
					<label for="fld0"><span><?php echo __('Installer language', 'install') ?></span> <small><?php echo __('Choose language help', 'install') ?></small></label><br />
					<span class="fld-input"><select id="fld0" name="lang">
<?php

		foreach ($languages as $lang)
			echo "\t\t\t\t\t".'<option value="'.$lang.'"'.($language == $lang ? ' selected="selected"' : '').'>'.$lang.'</option>'."\n";

?>					</select></span>
				</div>
			</div>
		</fieldset>
		<div class="frm-buttons">
			<span class="submit primary"><input type="submit" name="changelang" value="<?php echo __('Choose language', 'install') ?>" /></span>
		</div>
	</div>
	</form>
<?php

	}

?>	<form class="frm-form frm-suggest-username" method="post" accept-charset="utf-8" action="install.php">
	<div class="hidden">
		<input type="hidden" name="form_sent" value="1" />
	</div>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo __('Part1', 'install') ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<div class="ct-box info-box">
			<p><?php echo __('Part1 intro', 'install') ?></p>
			<ul class="spaced list-clean">
				<li><span><strong><?php echo __('Database type', 'install') ?></strong> <?php echo __('Database type info', 'install'); if (count($db_extensions) > 1) echo ' '.__('Mysql type info', 'install') ?></span></li>
				<li><span><strong><?php echo __('Database server', 'install') ?></strong> <?php echo __('Database server info', 'install') ?></span></li>
				<li><span><strong><?php echo __('Database name', 'install') ?></strong> <?php echo __('Database name info', 'install') ?></span></li>
				<li><span><strong><?php echo __('Database user pass', 'install') ?></strong> <?php echo __('Database username info', 'install') ?></span></li>
				<li><span><strong><?php echo __('Table prefix', 'install') ?></strong> <?php echo __('Table prefix info', 'install') ?></span></li>
			</ul>
		</div>
		<div id="req-msg" class="req-warn ct-box error-box">
			<p class="important"><?php echo __('Required warn', 'install') ?></p>
		</div>
		<fieldset class="frm-group group1">
			<legend class="group-legend"><strong><?php echo __('Part1 legend', 'install') ?></strong></legend>
			<div class="sf-set set1">
				<div class="sf-box select required">
					<label for="req_db_type"><span><?php echo __('Database type', 'install') ?></span> <small><?php echo __('Database type help', 'install') ?></small></label><br />
					<span class="fld-input"><select id="req_db_type" name="req_db_type">
<?php

	foreach ($db_extensions as $db_type)
		echo "\t\t\t\t\t".'<option value="'.$db_type[0].'">'.$db_type[1].'</option>'."\n";

?>					</select></span>
				</div>
			</div>
			<div class="sf-set set1" id="db_host_block">
				<div class="sf-box text required">
					<label for="db_host"><span><?php echo __('Database server', 'install') ?></span> <small><?php echo __('Database server help', 'install') ?></small></label><br />
					<span class="fld-input"><input id="db_host" type="text" name="req_db_host" value="localhost" size="35" maxlength="100" required /></span>
				</div>
			</div>
			<div class="sf-set set2">
				<div class="sf-box text required">
					<label for="fld3"><span><?php echo __('Database name', 'install') ?></span> <small><?php echo __('Database name help', 'install') ?></small></label><br />
					<span class="fld-input"><input id="fld3" type="text" name="req_db_name" size="35" maxlength="50" required /></span>
				</div>
			</div>
			<div class="sf-set set3" id="db_username_block">
				<div class="sf-box text">
					<label for="fld4"><span><?php echo __('Database username', 'install') ?></span> <small><?php echo __('Database username help', 'install') ?></small></label><br />
					<span class="fld-input"><input id="fld4" type="text" name="db_username" size="35" maxlength="50" /></span>
				</div>
			</div>
			<div class="sf-set set4" id="db_password_block">
				<div class="sf-box text">
					<label for="fld5"><span><?php echo __('Database password', 'install') ?></span> <small><?php echo __('Database password help', 'install') ?></small></label><br />
					<span class="fld-input"><input id="fld5" type="text" name="db_password" size="35" autocomplete="off" /></span>
				</div>
			</div>
			<div class="sf-set set5">
				<div class="sf-box text">
					<label for="fld6"><span><?php echo __('Table prefix', 'install') ?></span> <small><?php echo __('Table prefix help', 'install') ?></small></label><br />
					<span class="fld-input"><input id="fld6" type="text" name="db_prefix" size="35" maxlength="30" /></span>
				</div>
			</div>
		</fieldset>
	</div>

	<div class="main-subhead">
		<h2 class="hn"><span><?php echo __('Part2', 'install') ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<div class="ct-box info-box">
			<p><?php echo __('Part2 intro', 'install') ?></p>
		</div>
		<fieldset class="frm-group group1">
			<legend class="group-legend"><strong><?php echo __('Part2 legend', 'install') ?></strong></legend>
			<div class="sf-set set4">
				<div class="sf-box text required">
					<label for="admin_email"><span><?php echo __('Admin e-mail', 'install') ?></span> <small><?php echo __('E-mail address help', 'install') ?></small></label><br />
					<span class="fld-input"><input id="admin_email" type="email" data-suggest-role="email" name="req_email" size="35" maxlength="80" required /></span>
				</div>
			</div>
			<div class="sf-set set1 prepend-top">
				<div class="sf-box text required">
					<label for="admin_username"><span><?php echo __('Admin username', 'install') ?></span> <small><?php echo __('Username help', 'install') ?></small></label><br />
					<span class="fld-input"><input id="admin_username" type="text" data-suggest-role="username" name="req_username" size="35" maxlength="25" required /></span>
				</div>
			</div>
			<div class="sf-set set2">
				<div class="sf-box text required">
					<label for="fld8"><span><?php echo __('Admin password', 'install') ?></span> <small><?php echo __('Password help', 'install') ?></small></label><br />
					<span class="fld-input"><input id="fld8" type="text" name="req_password1" size="35" required autocomplete="off" /></span>
				</div>
			</div>
		</fieldset>
	</div>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo __('Part3', 'install') ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<div class="ct-box info-box">
			<p><?php echo __('Part3 intro', 'install') ?></p>
			<ul class="spaced list-clean">
				<li><span><strong><?php echo __('Base URL', 'install') ?></strong> <?php echo __('Base URL info', 'install') ?></span></li>
			</ul>
		</div>
		<fieldset class="frm-group group1">
			<legend class="group-legend"><strong><?php echo __('Part3 legend', 'install') ?></strong></legend>
			<div class="sf-set set3">
				<div class="sf-box text required">
					<label for="fld10"><span><?php echo __('Base URL', 'install') ?></span> <small><?php echo __('Base URL help', 'install') ?></small></label><br />
					<span class="fld-input"><input id="fld10" type="url" name="req_base_url" value="<?php echo $base_url_guess ?>" size="35" maxlength="100" required /></span>
				</div>
			</div>
<?php

	if (count($languages) > 1)
	{

?>			<div class="sf-set set4">
				<div class="sf-box text">
					<label for="fld11"><span><?php echo __('Default language', 'install') ?></span> <small><?php echo __('Default language help', 'install') ?></small></label><br />
					<span class="fld-input"><select id="fld11" name="req_language">
<?php

		foreach ($languages as $lang)
			echo "\t\t\t\t\t".'<option value="'.$lang.'"'.($language == $lang ? ' selected="selected"' : '').'>'.$lang.'</option>'."\n";

?>					</select></span>
				</div>
			</div>
<?php

	}
	else
	{

?>			<div class="hidden">
				<input type="hidden" name="req_language" value="<?php echo $languages[0] ?>" />
			</div>
<?php
	}

	if (file_exists(FORUM_ROOT.'extensions/pun_repository/manifest.xml'))
	{

?>			<div class="sf-set set5">
				<div class="sf-box checkbox">
					<span class="fld-input"><input id="fld12" type="checkbox" name="install_pun_repository" value="1" checked="checked" /></span>
					<label for="fld12"><span><?php echo __('Pun repository', 'install') ?></span> <?php echo __('Pun repository help', 'install') ?></label><br />
				</div>
			</div>
<?php

	}

?>
		</fieldset>
		<div class="frm-buttons">
			<span class="submit primary"><input type="submit" name="start" value="<?php echo __('Start install', 'install') ?>" /></span>
		</div>
	</div>
	</form>
</div>

</div>
</div>
	<script src="<?php echo FORUM_ROOT ?>include/js/min/punbb.common.min.js"></script>
	<script src="<?php echo FORUM_ROOT ?>include/js/min/punbb.install.min.js"></script>
</body>
</html>
<?php

}
else
{
	//
	// Strip slashes only if magic_quotes_gpc is on.
	//
	function unescape($str)
	{
		return (get_magic_quotes_gpc() == 1) ? stripslashes($str) : $str;
	}


	$db_type = $_POST['req_db_type'];
	$db_host = forum_trim($_POST['req_db_host']);
	$db_name = forum_trim($_POST['req_db_name']);
	$db_username = unescape(forum_trim($_POST['db_username']));
	$db_password = unescape(forum_trim($_POST['db_password']));
	$db_prefix = forum_trim($_POST['db_prefix']);
	$username = unescape(forum_trim($_POST['req_username']));
	$email = unescape(strtolower(forum_trim($_POST['req_email'])));
	$password1 = unescape(forum_trim($_POST['req_password1']));
	$default_lang = preg_replace('#[\.\\\/]#', '', unescape(forum_trim($_POST['req_language'])));
	$install_pun_repository = !empty($_POST['install_pun_repository']);

	// Make sure base_url doesn't end with a slash
	if (substr($_POST['req_base_url'], -1) == '/')
		$base_url = substr($_POST['req_base_url'], 0, -1);
	else
		$base_url = $_POST['req_base_url'];

	// Validate form
	if (utf8_strlen($db_name) == 0)
		error(__('Missing database name', 'install'));
	if (utf8_strlen($username) < 2)
		error(__('Username too short', 'install'));
	if (utf8_strlen($username) > 25)
		error(__('Username too long', 'install'));
	if (utf8_strlen($password1) < 4)
		error(__('Pass too short', 'install'));
	if (strtolower($username) == 'guest')
		error(__('Username guest', 'install'));
	if (preg_match('/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/', $username) || preg_match('/((([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}:[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){0,5}:((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(::([0-9A-Fa-f]{1,4}:){0,5}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|([0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}[0-9A-Fa-f]{1,4})|(::([0-9A-Fa-f]{1,4}:){0,6}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){1,7}:))/', $username))
		error(__('Username IP', 'install'));
	if ((strpos($username, '[') !== false || strpos($username, ']') !== false) && strpos($username, '\'') !== false && strpos($username, '"') !== false)
		error(__('Username reserved chars', 'install'));
	if (preg_match('/(?:\[\/?(?:b|u|i|h|colou?r|quote|code|img|url|email|list)\]|\[(?:code|quote|list)=)/i', $username))
		error(__('Username BBCode', 'install'));

	// Validate email
	if (!defined('FORUM_EMAIL_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/email.php';

	if (!is_valid_email($email))
		error(__('Invalid email', 'install'));

	// Make sure board title and description aren't left blank
	$board_title = 'My PunBB forum';
	$board_descrip = 'Unfortunately no one can be told what PunBB is — you have to see it for yourself';

	if (utf8_strlen($base_url) == 0)
		error(__('Missing base url', 'install'));

	if (!file_exists(language()->path[$default_lang] . '/common.php')) {
		error(__('Invalid language', 'install'));
	}

	// Load the appropriate DB layer class
	switch ($db_type)
	{
		case 'mysql':
			require FORUM_ROOT.'include/dblayer/mysql.php';
			break;

		case 'mysql_innodb':
			require FORUM_ROOT.'include/dblayer/mysql_innodb.php';
			break;

		case 'mysqli':
			require FORUM_ROOT.'include/dblayer/mysqli.php';
			break;

		case 'mysqli_innodb':
			require FORUM_ROOT.'include/dblayer/mysqli_innodb.php';
			break;

		case 'pgsql':
			require FORUM_ROOT.'include/dblayer/pgsql.php';
			break;

		case 'sqlite':
			require FORUM_ROOT.'include/dblayer/sqlite.php';
			break;

		case 'sqlite3':
			require FORUM_ROOT.'include/dblayer/sqlite3.php';
			break;

		default:
			error(sprintf(__('No such database type', 'install'), forum_htmlencode($db_type)));
	}

	// Create the database object (and connect/select db)
	db() = db();

	// If MySQL, make sure it's at least 4.1.2
	if (in_array($db_type, array('mysql', 'mysqli', 'mysql_innodb', 'mysqli_innodb')))
	{
		$mysql_info = db()->get_version();
		if (version_compare($mysql_info['version'], MIN_MYSQL_VERSION, '<'))
			error(sprintf(__('Invalid MySQL version', 'install'), forum_htmlencode($mysql_info['version']), MIN_MYSQL_VERSION));

		// Check InnoDB support in DB
		if (in_array($db_type, array('mysql_innodb', 'mysqli_innodb')))
		{
			$result = db()->query('SHOW VARIABLES LIKE \'have_innodb\'');
			$row = db()->fetch_assoc($result);

			if (!$row || !isset($row['Value']) || strtolower($row['Value']) != 'yes')
			{
				error(__('MySQL InnoDB Not Supported', 'install'));
			}
		}
	}

	// Validate prefix
	if (strlen($db_prefix) > 0 && (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $db_prefix) || strlen($db_prefix) > 40))
		error(sprintf(__('Invalid table prefix', 'install'), $db_prefix));

	// Check SQLite prefix collision
	if (in_array($db_type, array('sqlite', 'sqlite3')) && strtolower($db_prefix) == 'sqlite_')
		error(__('SQLite prefix collision', 'install'));


	// Make sure PunBB isn't already installed
	if (db()->table_exists('users'))
	{
		$query = array(
			'SELECT'	=> 'COUNT(id)',
			'FROM'		=> 'users',
			'WHERE'		=> 'id=1'
		);

		$result = db()->query_build($query);
		if (db()->result($result) > 0)
			error(sprintf(__('PunBB already installed', 'install'), $db_prefix, $db_name));
	}

	// Start a transaction
	db()->start_transaction();


	// Create all tables
	$schema = array(
		'FIELDS'		=> array(
			'id'			=> array(
				'datatype'		=> 'SERIAL',
				'allow_null'	=> false
			),
			'username'		=> array(
				'datatype'		=> 'VARCHAR(200)',
				'allow_null'	=> true
			),
			'ip'			=> array(
				'datatype'		=> 'VARCHAR(255)',
				'allow_null'	=> true
			),
			'email'			=> array(
				'datatype'		=> 'VARCHAR(80)',
				'allow_null'	=> true
			),
			'message'		=> array(
				'datatype'		=> 'VARCHAR(255)',
				'allow_null'	=> true
			),
			'expire'		=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> true
			),
			'ban_creator'	=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			)
		),
		'PRIMARY KEY'	=> array('id')
	);

	db()->create_table('bans', $schema);


	$schema = array(
		'FIELDS'		=> array(
			'id'			=> array(
				'datatype'		=> 'SERIAL',
				'allow_null'	=> false
			),
			'cat_name'		=> array(
				'datatype'		=> 'VARCHAR(80)',
				'allow_null'	=> false,
				'default'		=> '\'New Category\''
			),
			'disp_position'	=> array(
				'datatype'		=> 'INT(10)',
				'allow_null'	=> false,
				'default'		=> '0'
			)
		),
		'PRIMARY KEY'	=> array('id')
	);

	db()->create_table('categories', $schema);


	$schema = array(
		'FIELDS'		=> array(
			'id'			=> array(
				'datatype'		=> 'SERIAL',
				'allow_null'	=> false
			),
			'search_for'	=> array(
				'datatype'		=> 'VARCHAR(60)',
				'allow_null'	=> false,
				'default'		=> '\'\''
			),
			'replace_with'	=> array(
				'datatype'		=> 'VARCHAR(60)',
				'allow_null'	=> false,
				'default'		=> '\'\''
			)
		),
		'PRIMARY KEY'	=> array('id')
	);

	db()->create_table('censoring', $schema);


	$schema = array(
		'FIELDS'		=> array(
			'conf_name'		=> array(
				'datatype'		=> 'VARCHAR(255)',
				'allow_null'	=> false,
				'default'		=> '\'\''
			),
			'conf_value'	=> array(
				'datatype'		=> 'TEXT',
				'allow_null'	=> true
			)
		),
		'PRIMARY KEY'	=> array('conf_name')
	);

	db()->create_table('config', $schema);


	$schema = array(
		'FIELDS'		=> array(
			'id'				=> array(
				'datatype'		=> 'VARCHAR(150)',
				'allow_null'	=> false,
				'default'		=> '\'\''
			),
			'title'				=> array(
				'datatype'		=> 'VARCHAR(255)',
				'allow_null'	=> false,
				'default'		=> '\'\''
			),
			'version'			=> array(
				'datatype'		=> 'VARCHAR(25)',
				'allow_null'	=> false,
				'default'		=> '\'\''
			),
			'description'		=> array(
				'datatype'		=> 'TEXT',
				'allow_null'	=> true
			),
			'author'			=> array(
				'datatype'		=> 'VARCHAR(50)',
				'allow_null'	=> false,
				'default'		=> '\'\''
			),
			'uninstall'			=> array(
				'datatype'		=> 'TEXT',
				'allow_null'	=> true
			),
			'uninstall_note'	=> array(
				'datatype'		=> 'TEXT',
				'allow_null'	=> true
			),
			'disabled'			=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'dependencies'		=> array(
				'datatype'		=> 'VARCHAR(255)',
				'allow_null'	=> false,
				'default'		=> '\'\''
			)
		),
		'PRIMARY KEY'	=> array('id')
	);

	db()->create_table('extensions', $schema);


	$schema = array(
		'FIELDS'		=> array(
			'id'			=> array(
				'datatype'		=> 'VARCHAR(150)',
				'allow_null'	=> false,
				'default'		=> '\'\''
			),
			'extension_id'	=> array(
				'datatype'		=> 'VARCHAR(50)',
				'allow_null'	=> false,
				'default'		=> '\'\''
			),
			'code'			=> array(
				'datatype'		=> 'TEXT',
				'allow_null'	=> true
			),
			'installed'		=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'priority'		=> array(
				'datatype'		=> 'TINYINT(1) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '5'
			)
		),
		'PRIMARY KEY'	=> array('id', 'extension_id')
	);

	db()->create_table('extension_hooks', $schema);


	$schema = array(
		'FIELDS'		=> array(
			'group_id'		=> array(
				'datatype'		=> 'INT(10)',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'forum_id'		=> array(
				'datatype'		=> 'INT(10)',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'read_forum'	=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '1'
			),
			'post_replies'	=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '1'
			),
			'post_topics'	=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '1'
			)
		),
		'PRIMARY KEY'	=> array('group_id', 'forum_id')
	);

	db()->create_table('forum_perms', $schema);


	$schema = array(
		'FIELDS'		=> array(
			'id'			=> array(
				'datatype'		=> 'SERIAL',
				'allow_null'	=> false
			),
			'forum_name'	=> array(
				'datatype'		=> 'VARCHAR(80)',
				'allow_null'	=> false,
				'default'		=> '\'New forum\''
			),
			'forum_desc'	=> array(
				'datatype'		=> 'TEXT',
				'allow_null'	=> true
			),
			'redirect_url'	=> array(
				'datatype'		=> 'VARCHAR(100)',
				'allow_null'	=> true
			),
			'moderators'	=> array(
				'datatype'		=> 'TEXT',
				'allow_null'	=> true
			),
			'num_topics'	=> array(
				'datatype'		=> 'MEDIUMINT(8) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'num_posts'		=> array(
				'datatype'		=> 'MEDIUMINT(8) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'last_post'		=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> true
			),
			'last_post_id'	=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> true
			),
			'last_poster'	=> array(
				'datatype'		=> 'VARCHAR(200)',
				'allow_null'	=> true
			),
			'sort_by'		=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'disp_position'	=> array(
				'datatype'		=> 'INT(10)',
				'allow_null'	=> false,
				'default'		=>	'0'
			),
			'cat_id'		=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=>	'0'
			)
		),
		'PRIMARY KEY'	=> array('id')
	);

	db()->create_table('forums', $schema);


	$schema = array(
		'FIELDS'		=> array(
			'g_id'						=> array(
				'datatype'		=> 'SERIAL',
				'allow_null'	=> false
			),
			'g_title'					=> array(
				'datatype'		=> 'VARCHAR(50)',
				'allow_null'	=> false,
				'default'		=> '\'\''
			),
			'g_user_title'				=> array(
				'datatype'		=> 'VARCHAR(50)',
				'allow_null'	=> true
			),
			'g_moderator'				=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'g_mod_edit_users'			=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'g_mod_rename_users'		=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'g_mod_change_passwords'	=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'g_mod_ban_users'			=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'g_read_board'				=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '1'
			),
			'g_view_users'				=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '1'
			),
			'g_post_replies'			=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '1'
			),
			'g_post_topics'				=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '1'
			),
			'g_edit_posts'				=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '1'
			),
			'g_delete_posts'			=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '1'
			),
			'g_delete_topics'			=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '1'
			),
			'g_set_title'				=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '1'
			),
			'g_search'					=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '1'
			),
			'g_search_users'			=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '1'
			),
			'g_send_email'				=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '1'
			),
			'g_post_flood'				=> array(
				'datatype'		=> 'SMALLINT(6)',
				'allow_null'	=> false,
				'default'		=> '30'
			),
			'g_search_flood'			=> array(
				'datatype'		=> 'SMALLINT(6)',
				'allow_null'	=> false,
				'default'		=> '30'
			),
			'g_email_flood'				=> array(
				'datatype'		=> 'SMALLINT(6)',
				'allow_null'	=> false,
				'default'		=> '60'
			)
		),
		'PRIMARY KEY'	=> array('g_id')
	);

	db()->create_table('groups', $schema);


	$schema = array(
		'FIELDS'		=> array(
			'user_id'		=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '1'
			),
			'ident'			=> array(
				'datatype'		=> 'VARCHAR(200)',
				'allow_null'	=> false,
				'default'		=> '\'\''
			),
			'logged'		=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'idle'			=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'csrf_token'	=> array(
				'datatype'		=> 'VARCHAR(40)',
				'allow_null'	=> false,
				'default'		=> '\'\''
			),
			'prev_url'		=> array(
				'datatype'		=> 'VARCHAR(255)',
				'allow_null'	=> true
			),
			'last_post'			=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> true
			),
			'last_search'		=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> true
			),
		),
		'UNIQUE KEYS'	=> array(
			'user_id_ident_idx'	=> array('user_id', 'ident')
		),
		'INDEXES'		=> array(
			'ident_idx'		=> array('ident'),
			'logged_idx'	=> array('logged')
		),
		'ENGINE'		=> 'HEAP'
	);

	if (in_array($db_type, array('mysql', 'mysqli', 'mysql_innodb', 'mysqli_innodb')))
	{
		$schema['UNIQUE KEYS']['user_id_ident_idx'] = array('user_id', 'ident(25)');
		$schema['INDEXES']['ident_idx'] = array('ident(25)');
	}

	db()->create_table('online', $schema);


	$schema = array(
		'FIELDS'		=> array(
			'id'			=> array(
				'datatype'		=> 'SERIAL',
				'allow_null'	=> false
			),
			'poster'		=> array(
				'datatype'		=> 'VARCHAR(200)',
				'allow_null'	=> false,
				'default'		=> '\'\''
			),
			'poster_id'		=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '1'
			),
			'poster_ip'		=> array(
				'datatype'		=> 'VARCHAR(39)',
				'allow_null'	=> true
			),
			'poster_email'	=> array(
				'datatype'		=> 'VARCHAR(80)',
				'allow_null'	=> true
			),
			'message'		=> array(
				'datatype'		=> 'TEXT',
				'allow_null'	=> true
			),
			'hide_smilies'	=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'posted'		=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'edited'		=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> true
			),
			'edited_by'		=> array(
				'datatype'		=> 'VARCHAR(200)',
				'allow_null'	=> true
			),
			'topic_id'		=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			)
		),
		'PRIMARY KEY'	=> array('id'),
		'INDEXES'		=> array(
			'topic_id_idx'	=> array('topic_id'),
			'multi_idx'		=> array('poster_id', 'topic_id'),
			'posted_idx'	=> array('posted')
		)
	);

	db()->create_table('posts', $schema);


	$schema = array(
		'FIELDS'		=> array(
			'id'			=> array(
				'datatype'		=> 'SERIAL',
				'allow_null'	=> false
			),
			'rank'			=> array(
				'datatype'		=> 'VARCHAR(50)',
				'allow_null'	=> false,
				'default'		=> '\'\''
			),
			'min_posts'		=> array(
				'datatype'		=> 'MEDIUMINT(8) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			)
		),
		'PRIMARY KEY'	=> array('id')
	);

	db()->create_table('ranks', $schema);


	$schema = array(
		'FIELDS'		=> array(
			'id'			=> array(
				'datatype'		=> 'SERIAL',
				'allow_null'	=> false
			),
			'post_id'		=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'topic_id'		=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'forum_id'		=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'reported_by'	=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'created'		=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'message'		=> array(
				'datatype'		=> 'TEXT',
				'allow_null'	=> true
			),
			'zapped'		=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> true
			),
			'zapped_by'		=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> true
			)
		),
		'PRIMARY KEY'	=> array('id'),
		'INDEXES'		=> array(
			'zapped_idx'	=> array('zapped')
		)
	);

	db()->create_table('reports', $schema);


	$schema = array(
		'FIELDS'		=> array(
			'id'			=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'ident'			=> array(
				'datatype'		=> 'VARCHAR(200)',
				'allow_null'	=> false,
				'default'		=> '\'\''
			),
			'search_data'	=> array(
				'datatype'		=> 'TEXT',
				'allow_null'	=> true
			)
		),
		'PRIMARY KEY'	=> array('id'),
		'INDEXES'		=> array(
			'ident_idx'	=> array('ident')
		)
	);

	if (in_array($db_type, array('mysql', 'mysqli', 'mysql_innodb', 'mysqli_innodb')))
		$schema['INDEXES']['ident_idx'] = array('ident(8)');

	db()->create_table('search_cache', $schema);


	$schema = array(
		'FIELDS'		=> array(
			'post_id'		=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'word_id'		=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'subject_match'	=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '0'
			)
		),
		'INDEXES'		=> array(
			'word_id_idx'	=> array('word_id'),
			'post_id_idx'	=> array('post_id')
		)
	);

	db()->create_table('search_matches', $schema);


	$schema = array(
		'FIELDS'		=> array(
			'id'			=> array(
				'datatype'		=> 'SERIAL',
				'allow_null'	=> false
			),
			'word'			=> array(
				'datatype'		=> 'VARCHAR(20)',
				'allow_null'	=> false,
				'default'		=> '\'\'',
				'collation'		=> 'bin'
			)
		),
		'PRIMARY KEY'	=> array('word'),
		'INDEXES'		=> array(
			'id_idx'	=> array('id')
		)
	);

	if ($db_type == 'sqlite' || $db_type == 'sqlite3')
	{
		$schema['PRIMARY KEY'] = array('id');
		$schema['UNIQUE KEYS'] = array('word_idx'	=> array('word'));
	}

	db()->create_table('search_words', $schema);


	$schema = array(
		'FIELDS'		=> array(
			'user_id'		=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'topic_id'		=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			)
		),
		'PRIMARY KEY'	=> array('user_id', 'topic_id')
	);

	db()->create_table('subscriptions', $schema);


	$schema = array(
		'FIELDS'		=> array(
			'user_id'		=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'forum_id'		=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			)
		),
		'PRIMARY KEY'	=> array('user_id', 'forum_id')
	);

	db()->create_table('forum_subscriptions', $schema);


	$schema = array(
		'FIELDS'		=> array(
			'id'			=> array(
				'datatype'		=> 'SERIAL',
				'allow_null'	=> false
			),
			'poster'		=> array(
				'datatype'		=> 'VARCHAR(200)',
				'allow_null'	=> false,
				'default'		=> '\'\''
			),
			'subject'		=> array(
				'datatype'		=> 'VARCHAR(255)',
				'allow_null'	=> false,
				'default'		=> '\'\''
			),
			'posted'		=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'first_post_id'	=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'last_post'		=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'last_post_id'	=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'last_poster'	=> array(
				'datatype'		=> 'VARCHAR(200)',
				'allow_null'	=> true
			),
			'num_views'		=> array(
				'datatype'		=> 'MEDIUMINT(8) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'num_replies'	=> array(
				'datatype'		=> 'MEDIUMINT(8) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'closed'		=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'sticky'		=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'moved_to'		=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> true
			),
			'forum_id'		=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			)
		),
		'PRIMARY KEY'	=> array('id'),
		'INDEXES'		=> array(
			'forum_id_idx'		=> array('forum_id'),
			'moved_to_idx'		=> array('moved_to'),
			'last_post_idx'		=> array('last_post'),
			'first_post_id_idx'	=> array('first_post_id')
		)
	);

	db()->create_table('topics', $schema);


	$schema = array(
		'FIELDS'		=> array(
			'id'				=> array(
				'datatype'		=> 'SERIAL',
				'allow_null'	=> false
			),
			'group_id'			=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '3'
			),
			'username'			=> array(
				'datatype'		=> 'VARCHAR(200)',
				'allow_null'	=> false,
				'default'		=> '\'\''
			),
			'password'			=> array(
				'datatype'		=> 'VARCHAR(40)',
				'allow_null'	=> false,
				'default'		=> '\'\''
			),
			'salt'				=> array(
				'datatype'		=> 'VARCHAR(12)',
				'allow_null'	=> true
			),
			'email'				=> array(
				'datatype'		=> 'VARCHAR(80)',
				'allow_null'	=> false,
				'default'		=> '\'\''
			),
			'title'				=> array(
				'datatype'		=> 'VARCHAR(50)',
				'allow_null'	=> true
			),
			'realname'			=> array(
				'datatype'		=> 'VARCHAR(40)',
				'allow_null'	=> true
			),
			'url'				=> array(
				'datatype'		=> 'VARCHAR(100)',
				'allow_null'	=> true
			),
			'facebook'			=> array(
				'datatype'		=> 'VARCHAR(100)',
				'allow_null'	=> true
			),
			'twitter'			=> array(
				'datatype'		=> 'VARCHAR(100)',
				'allow_null'	=> true
			),
			'linkedin'			=> array(
				'datatype'		=> 'VARCHAR(100)',
				'allow_null'	=> true
			),
			'skype'			=> array(
				'datatype'		=> 'VARCHAR(100)',
				'allow_null'	=> true
			),
			'jabber'			=> array(
				'datatype'		=> 'VARCHAR(80)',
				'allow_null'	=> true
			),
			'icq'				=> array(
				'datatype'		=> 'VARCHAR(12)',
				'allow_null'	=> true
			),
			'msn'				=> array(
				'datatype'		=> 'VARCHAR(80)',
				'allow_null'	=> true
			),
			'aim'				=> array(
				'datatype'		=> 'VARCHAR(30)',
				'allow_null'	=> true
			),
			'yahoo'				=> array(
				'datatype'		=> 'VARCHAR(30)',
				'allow_null'	=> true
			),
			'location'			=> array(
				'datatype'		=> 'VARCHAR(30)',
				'allow_null'	=> true
			),
			'signature'			=> array(
				'datatype'		=> 'TEXT',
				'allow_null'	=> true
			),
			'disp_topics'		=> array(
				'datatype'		=> 'TINYINT(3) UNSIGNED',
				'allow_null'	=> true
			),
			'disp_posts'		=> array(
				'datatype'		=> 'TINYINT(3) UNSIGNED',
				'allow_null'	=> true
			),
			'email_setting'		=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '1'
			),
			'notify_with_post'	=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'auto_notify'		=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'show_smilies'		=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '1'
			),
			'show_img'			=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '1'
			),
			'show_img_sig'		=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '1'
			),
			'show_avatars'		=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '1'
			),
			'show_sig'			=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '1'
			),
			'access_keys'		=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'timezone'			=> array(
				'datatype'		=> 'FLOAT',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'dst'				=> array(
				'datatype'		=> 'TINYINT(1)',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'time_format'		=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'date_format'		=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'language'			=> array(
				'datatype'		=> 'VARCHAR(25)',
				'allow_null'	=> false,
				'default'		=> '\'English\''
			),
			'style'				=> array(
				'datatype'		=> 'VARCHAR(25)',
				'allow_null'	=> false,
				'default'		=> '\'Oxygen\''
			),
			'num_posts'			=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'last_post'			=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> true
			),
			'last_search'		=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> true
			),
			'last_email_sent'	=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> true
			),
			'registered'		=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'registration_ip'	=> array(
				'datatype'		=> 'VARCHAR(39)',
				'allow_null'	=> false,
				'default'		=> '\'0.0.0.0\''
			),
			'last_visit'		=> array(
				'datatype'		=> 'INT(10) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> '0'
			),
			'admin_note'		=> array(
				'datatype'		=> 'VARCHAR(30)',
				'allow_null'	=> true
			),
			'activate_string'	=> array(
				'datatype'		=> 'VARCHAR(80)',
				'allow_null'	=> true
			),
			'activate_key'		=> array(
				'datatype'		=> 'VARCHAR(8)',
				'allow_null'	=> true
			),
			'avatar'			=> array(
				'datatype'		=> 'TINYINT(3) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> 0,
			),
			'avatar_width'		=> array(
				'datatype'		=> 'TINYINT(3) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> 0,
			),
			'avatar_height'		=> array(
				'datatype'		=> 'TINYINT(3) UNSIGNED',
				'allow_null'	=> false,
				'default'		=> 0,
			),
		),
		'PRIMARY KEY'	=> array('id'),
		'INDEXES'		=> array(
			'registered_idx'	=> array('registered'),
			'username_idx'		=> array('username')
		)
	);

	if (in_array($db_type, array('mysql', 'mysqli', 'mysql_innodb', 'mysqli_innodb')))
		$schema['INDEXES']['username_idx'] = array('username(8)');

	db()->create_table('users', $schema);



	$now = time();

	// Insert the four preset groups
	$query = array(
		'INSERT'	=> 'g_title, g_user_title, g_moderator, g_mod_edit_users, g_mod_rename_users, g_mod_change_passwords, g_mod_ban_users, g_read_board, g_view_users, g_post_replies, g_post_topics, g_edit_posts, g_delete_posts, g_delete_topics, g_set_title, g_search, g_search_users, g_send_email, g_post_flood, g_search_flood, g_email_flood',
		'INTO'		=> 'groups',
		'VALUES'	=> '\'Administrators\', \'Administrator\', 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0'
	);

	if ($db_type != 'pgsql')
	{
		$query['INSERT'] .= ', g_id';
		$query['VALUES'] .= ', 1';
	}

	db()->query_build($query) or error(__FILE__, __LINE__);

	$query = array(
		'INSERT'	=> 'g_title, g_user_title, g_moderator, g_mod_edit_users, g_mod_rename_users, g_mod_change_passwords, g_mod_ban_users, g_read_board, g_view_users, g_post_replies, g_post_topics, g_edit_posts, g_delete_posts, g_delete_topics, g_set_title, g_search, g_search_users, g_send_email, g_post_flood, g_search_flood, g_email_flood',
		'INTO'		=> 'groups',
		'VALUES'	=> '\'Guest\', NULL, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 1, 1, 0, 60, 30, 0'
	);

	if ($db_type != 'pgsql')
	{
		$query['INSERT'] .= ', g_id';
		$query['VALUES'] .= ', 2';
	}

	db()->query_build($query) or error(__FILE__, __LINE__);

	$query = array(
		'INSERT'	=> 'g_title, g_user_title, g_moderator, g_mod_edit_users, g_mod_rename_users, g_mod_change_passwords, g_mod_ban_users, g_read_board, g_view_users, g_post_replies, g_post_topics, g_edit_posts, g_delete_posts, g_delete_topics, g_set_title, g_search, g_search_users, g_send_email, g_post_flood, g_search_flood, g_email_flood',
		'INTO'		=> 'groups',
		'VALUES'	=> '\'Members\', NULL, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 0, 1, 1, 1, 60, 30, 60'
	);

	if ($db_type != 'pgsql')
	{
		$query['INSERT'] .= ', g_id';
		$query['VALUES'] .= ', 3';
	}

	db()->query_build($query) or error(__FILE__, __LINE__);

	$query = array(
		'INSERT'	=> 'g_title, g_user_title, g_moderator, g_mod_edit_users, g_mod_rename_users, g_mod_change_passwords, g_mod_ban_users, g_read_board, g_view_users, g_post_replies, g_post_topics, g_edit_posts, g_delete_posts, g_delete_topics, g_set_title, g_search, g_search_users, g_send_email, g_post_flood, g_search_flood, g_email_flood',
		'INTO'		=> 'groups',
		'VALUES'	=> '\'Moderators\', \'Moderator\', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0'
	);

	if ($db_type != 'pgsql')
	{
		$query['INSERT'] .= ', g_id';
		$query['VALUES'] .= ', 4';
	}

	db()->query_build($query) or error(__FILE__, __LINE__);

	// Insert guest and first admin user
	$query = array(
		'INSERT'	=> 'group_id, username, password, email',
		'INTO'		=> 'users',
		'VALUES'	=> '2, \'Guest\', \'Guest\', \'Guest\''
	);

	if ($db_type != 'pgsql')
	{
		$query['INSERT'] .= ', id';
		$query['VALUES'] .= ', 1';
	}

	db()->query_build($query) or error(__FILE__, __LINE__);

	$salt = random_key(12);

	$query = array(
		'INSERT'	=> 'group_id, username, password, email, language, num_posts, last_post, registered, registration_ip, last_visit, salt',
		'INTO'		=> 'users',
		'VALUES'	=> '1, \''.db()->escape($username).'\', \''.forum_hash($password1, $salt).'\', \''.db()->escape($email).'\', \''.db()->escape($default_lang).'\', 1, '.$now.', '.$now.', \'127.0.0.1\', '.$now.', \''.db()->escape($salt).'\''
	);

	db()->query_build($query) or error(__FILE__, __LINE__);
	$new_uid = db()->insert_id();

	// Enable/disable avatars depending on file_uploads setting in PHP configuration
	$avatars = in_array(strtolower(@ini_get('file_uploads')), array('on', 'true', '1')) ? 1 : 0;

	// Enable/disable automatic check for updates depending on PHP environment (require cURL, fsockopen or allow_url_fopen)
	$check_for_updates = (function_exists('curl_init') || function_exists('fsockopen') || in_array(strtolower(@ini_get('allow_url_fopen')), array('on', 'true', '1'))) ? 1 : 0;

	// Insert config data
	$config = array(
		'o_cur_version'				=> "'".FORUM_VERSION."'",
		'o_database_revision'		=> "'".FORUM_DB_REVISION."'",
		'o_board_title'				=> "'".db()->escape($board_title)."'",
		'o_board_desc'				=> "'".db()->escape($board_descrip)."'",
		'o_default_timezone'		=> "'0'",
		'o_time_format'				=> "'H:i:s'",
		'o_date_format'				=> "'Y-m-d'",
		'o_check_for_updates'		=> "'$check_for_updates'",
		'o_check_for_versions'		=> "'$check_for_updates'",
		'o_timeout_visit'			=> "'5400'",
		'o_timeout_online'			=> "'300'",
		'o_redirect_delay'			=> "'0'",
		'o_show_version'			=> "'0'",
		'o_show_user_info'			=> "'1'",
		'o_show_post_count'			=> "'1'",
		'o_signatures'				=> "'1'",
		'o_smilies'					=> "'1'",
		'o_smilies_sig'				=> "'1'",
		'o_make_links'				=> "'1'",
		'o_default_lang'			=> "'".db()->escape($default_lang)."'",
		'o_default_style'			=> "'Oxygen'",
		'o_default_user_group'		=> "'3'",
		'o_topic_review'			=> "'15'",
		'o_disp_topics_default'		=> "'30'",
		'o_disp_posts_default'		=> "'25'",
		'o_indent_num_spaces'		=> "'4'",
		'o_quote_depth'				=> "'3'",
		'o_quickpost'				=> "'1'",
		'o_users_online'			=> "'1'",
		'o_censoring'				=> "'0'",
		'o_ranks'					=> "'1'",
		'o_show_dot'				=> "'0'",
		'o_topic_views'				=> "'1'",
		'o_quickjump'				=> "'1'",
		'o_gzip'					=> "'0'",
		'o_additional_navlinks'		=> "''",
		'o_report_method'			=> "'0'",
		'o_regs_report'				=> "'0'",
		'o_default_email_setting'	=> "'1'",
		'o_mailing_list'			=> "'".db()->escape($email)."'",
		'o_avatars'					=> "'$avatars'",
		'o_avatars_dir'				=> "'img/avatars'",
		'o_avatars_width'			=> "'60'",
		'o_avatars_height'			=> "'60'",
		'o_avatars_size'			=> "'15360'",
		'o_search_all_forums'		=> "'1'",
		'o_sef'						=> "'Default'",
		'o_admin_email'				=> "'".db()->escape($email)."'",
		'o_webmaster_email'			=> "'".db()->escape($email)."'",
		'o_subscriptions'			=> "'1'",
		'o_smtp_host'				=> "NULL",
		'o_smtp_user'				=> "NULL",
		'o_smtp_pass'				=> "NULL",
		'o_smtp_ssl'				=> "'0'",
		'o_regs_allow'				=> "'1'",
		'o_regs_verify'				=> "'0'",
		'o_announcement'			=> "'0'",
		'o_announcement_heading'	=> "'".__('Default announce heading', 'install')."'",
		'o_announcement_message'	=> "'".__('Default announce message', 'install')."'",
		'o_rules'					=> "'0'",
		'o_rules_message'			=> "'".__('Default rules', 'install')."'",
		'o_maintenance'				=> "'0'",
		'o_maintenance_message'		=> "'".__('Maintenance message default', 'admin_settings')."'",
		'o_default_dst'				=> "'0'",
		'p_message_bbcode'			=> "'1'",
		'p_message_img_tag'			=> "'1'",
		'p_message_all_caps'		=> "'1'",
		'p_subject_all_caps'		=> "'1'",
		'p_sig_all_caps'			=> "'1'",
		'p_sig_bbcode'				=> "'1'",
		'p_sig_img_tag'				=> "'0'",
		'p_sig_length'				=> "'400'",
		'p_sig_lines'				=> "'4'",
		'p_allow_banned_email'		=> "'1'",
		'p_allow_dupe_email'		=> "'0'",
		'p_force_guest_email'		=> "'1'",
		'o_show_moderators'			=> "'0'",
		'o_mask_passwords'			=> "'1'"
	);

	foreach ($config as $conf_name => $conf_value)
	{
		$query = array(
			'INSERT'	=> 'conf_name, conf_value',
			'INTO'		=> 'config',
			'VALUES'	=> '\''.$conf_name.'\', '.$conf_value.''
		);

		db()->query_build($query) or error(__FILE__, __LINE__);
	}

	// Insert some other default data
	$query = array(
		'INSERT'	=> 'cat_name, disp_position',
		'INTO'		=> 'categories',
		'VALUES'	=> '\''.__('Default category name', 'install').'\', 1'
	);

	db()->query_build($query) or error(__FILE__, __LINE__);

	$query = array(
		'INSERT'	=> 'forum_name, forum_desc, num_topics, num_posts, last_post, last_post_id, last_poster, disp_position, cat_id',
		'INTO'		=> 'forums',
		'VALUES'	=> '\''.__('Default forum name', 'install').'\', \''.__('Default forum descrip', 'install').'\', 1, 1, '.$now.', 1, \''.db()->escape($username).'\', 1, '.db()->insert_id().''
	);

	db()->query_build($query) or error(__FILE__, __LINE__);

	$query = array(
		'INSERT'	=> 'poster, subject, posted, first_post_id, last_post, last_post_id, last_poster, forum_id',
		'INTO'		=> 'topics',
		'VALUES'	=> '\''.db()->escape($username).'\', \''.__('Default topic subject', 'install').'\', '.$now.', 1, '.$now.', 1, \''.db()->escape($username).'\', '.db()->insert_id().''
	);

	db()->query_build($query) or error(__FILE__, __LINE__);

	$query = array(
		'INSERT'	=> 'poster, poster_id, poster_ip, message, posted, topic_id',
		'INTO'		=> 'posts',
		'VALUES'	=> '\''.db()->escape($username).'\', '.$new_uid.', \'127.0.0.1\', \''.__('Default post contents', 'install').'\', '.$now.', '.db()->insert_id().''
	);

	if ($db_type != 'pgsql')
	{
		$query['INSERT'] .= ', id';
		$query['VALUES'] .= ', 1';
	}

	db()->query_build($query) or error(__FILE__, __LINE__);

	// Add new post to search table
	require FORUM_ROOT.'include/search_idx.php';
	update_search_index('post', db()->insert_id(), __('Default post contents', 'install'), __('Default topic subject', 'install'));

	// Insert the default ranks
	$query = array(
		'INSERT'	=> 'rank, min_posts',
		'INTO'		=> 'ranks',
		'VALUES'	=> '\''.__('Default rank 1', 'install') . '\', 0'
	);

	db()->query_build($query) or error(__FILE__, __LINE__);

	$query = array(
		'INSERT'	=> 'rank, min_posts',
		'INTO'		=> 'ranks',
		'VALUES'	=> '\''.__('Default rank 2', 'install') . '\', 10'
	);

	db()->query_build($query) or error(__FILE__, __LINE__);

	db()->end_transaction();


	$alerts = array();

	// Check if the cache directory is writable and clear cache dir
	if (is_writable(FORUM_ROOT.'cache/'))
	{
		$cache_dir = dir(FORUM_ROOT.'cache/');
		if ($cache_dir)
		{
			while (($entry = $cache_dir->read()) !== false)
			{
				if (substr($entry, strlen($entry)-4) == '.php')
					@unlink(FORUM_ROOT.'cache/'.$entry);
			}
			$cache_dir->close();
		}
	}
	else
	{
		$alerts[] = '<li><span>'.__('No cache write', 'install').'</span></li>';
	}

	// Check if default avatar directory is writable
	if (!is_writable(FORUM_ROOT.'img/avatars/'))
		$alerts[] = '<li><span>'.__('No avatar write', 'install').'</span></li>';

	// Check if we disabled uploading avatars because file_uploads was disabled
	if ($avatars == '0')
		$alerts[] = '<li><span>'.__('File upload alert', 'install').'</span></li>';

	// Add some random bytes at the end of the cookie name to prevent collisions
	$cookie_name = 'forum_cookie_'.random_key(6, false, true);

	/// Generate the config.php file data
	$config = generate_config_file();

	// Attempt to write config.php and serve it up for download if writing fails
	$written = false;
	if (is_writable(FORUM_ROOT))
	{
		$fh = @fopen(FORUM_ROOT.'config.php', 'wb');
		if ($fh)
		{
			fwrite($fh, $config);
			fclose($fh);

			$written = true;
		}
	}


	if ($install_pun_repository && is_readable(FORUM_ROOT.'extensions/pun_repository/manifest.xml'))
	{
		require FORUM_ROOT.'include/xml.php';

		$ext_data = xml_to_array(file_get_contents(FORUM_ROOT.'extensions/pun_repository/manifest.xml'));

		if (!empty($ext_data))
		{
			$query = array(
				'INSERT'	=> 'id, title, version, description, author, uninstall, uninstall_note, dependencies',
				'INTO'		=> 'extensions',
				'VALUES'	=> '\'pun_repository\', \''.db()->escape($ext_data['extension']['title']).'\', \''.db()->escape($ext_data['extension']['version']).'\', \''.db()->escape($ext_data['extension']['description']).'\', \''.db()->escape($ext_data['extension']['author']).'\', NULL, NULL, \'||\'',
			);

			db()->query_build($query) or error(__FILE__, __LINE__);

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
							'VALUES'	=> '\''.db()->escape(forum_trim($cur_hook)).'\', \'pun_repository\', \''.db()->escape(forum_trim($ext_hook['content'])).'\', '.time().', '.(isset($ext_hook['attributes']['priority']) ? $ext_hook['attributes']['priority'] : 5)
						);

						db()->query_build($query) or error(__FILE__, __LINE__);
					}
				}
			}
		}
	}


?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html class="oldie ie6" lang="en" dir="ltr"> <![endif]-->
<!--[if IE 7 ]>    <html class="oldie ie7" lang="en" dir="ltr"> <![endif]-->
<!--[if IE 8 ]>    <html class="oldie ie8" lang="en" dir="ltr"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="en" dir="ltr"> <!--<![endif]-->
<head>
	<meta charset="utf-8" />
	<title>PunBB Installation</title>
	<link rel="stylesheet" type="text/css"
		href="<?= theme()->url['Oxygen'] ?>/Oxygen.min.css" />
</head>
<body>
<div id="brd-install" class="brd-page">
	<div id="brd-wrap" class="brd">
		<div id="brd-head" class="gen-content">
			<p id="brd-title"><strong><?php printf(__('Install PunBB', 'install'), FORUM_VERSION) ?></strong></p>
			<p id="brd-desc"><?php printf(__('Success description', 'install'), FORUM_VERSION) ?></p>
		</div>
		<div id="brd-main" class="main basic">
			<div class="main-head">
				<h1 class="hn"><span><?php echo __('Final instructions', 'install') ?></span></h1>
			</div>
			<div class="main-content main-frm">
<?php if (!empty($alerts)): ?>
				<div class="ct-box error-box">
					<p class="warn"><strong><?php echo __('Warning', 'install') ?></strong></p>
					<ul>
						<?php echo implode("\n\t\t\t\t", $alerts)."\n" ?>
					</ul>
				</div>
<?php endif;

if (!$written)
{
?>
				<div class="ct-box info-box">
					<p class="warn"><?php echo __('No write info 1', 'install') ?></p>
					<p class="warn"><?php printf(__('No write info 2', 'install'), '<a href="'.FORUM_ROOT.'index.php">'.__('Go to index', 'install').'</a>') ?></p>
				</div>
				<form class="frm-form" method="post" accept-charset="utf-8" action="install.php">
					<div class="hidden">
					<input type="hidden" name="generate_config" value="1" />
					<input type="hidden" name="db_type" value="<?php echo $db_type ?>" />
					<input type="hidden" name="db_host" value="<?php echo $db_host ?>" />
					<input type="hidden" name="db_name" value="<?php echo forum_htmlencode($db_name) ?>" />
					<input type="hidden" name="db_username" value="<?php echo forum_htmlencode($db_username) ?>" />
					<input type="hidden" name="db_password" value="<?php echo forum_htmlencode($db_password) ?>" />
					<input type="hidden" name="db_prefix" value="<?php echo forum_htmlencode($db_prefix) ?>" />
					<input type="hidden" name="base_url" value="<?php echo forum_htmlencode($base_url) ?>" />
					<input type="hidden" name="cookie_name" value="<?php echo forum_htmlencode($cookie_name) ?>" />
					</div>
					<div class="frm-buttons">
						<span class="submit"><input type="submit" value="<?php echo __('Download config', 'install') ?>" /></span>
					</div>
				</form>
<?php
}
else
{
?>
				<div class="ct-box info-box">
					<p class="warn"><?php printf(__('Write info', 'install'), '<a href="'.FORUM_ROOT.'index.php">'.__('Go to index', 'install').'</a>') ?></p>
				</div>
<?php
}
?>
			</div>
		</div>
	</div>
</div>
</body>
</html>
<?php
}
