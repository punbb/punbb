<?php
/**
 * Ban management page.
 *
 * Allows administrators and moderators to create, modify, and delete bans.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */


if (!defined('FORUM_ROOT'))
	define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';
require FORUM_ROOT.'include/common_admin.php';

($hook = get_hook('aba_start')) ? eval($hook) : null;

if ($forum_user['g_id'] != FORUM_ADMIN && ($forum_user['g_moderator'] != '1' || $forum_user['g_mod_ban_users'] == '0'))
	message($lang_common['No permission']);

// Load the admin.php language file
require FORUM_ROOT.'lang/'.$forum_user['language'].'/admin_common.php';
require FORUM_ROOT.'lang/'.$forum_user['language'].'/admin_bans.php';

// Add/edit a ban (stage 1)
if (isset($_REQUEST['add_ban']) || isset($_GET['edit_ban']))
{
	if (isset($_GET['add_ban']) || isset($_POST['add_ban']))
	{
		// If the id of the user to ban was provided through GET (a link from profile.php)
		if (isset($_GET['add_ban']))
		{
			$add_ban = intval($_GET['add_ban']);
			if ($add_ban < 2)
				message($lang_common['Bad request']);

			$user_id = $add_ban;

			($hook = get_hook('aba_add_ban_selected')) ? eval($hook) : null;

			$query = array(
				'SELECT'	=> 'u.group_id, u.username, u.email, u.registration_ip',
				'FROM'		=> 'users AS u',
				'WHERE'		=> 'u.id='.$user_id
			);

			($hook = get_hook('aba_add_ban_qr_get_user_by_id')) ? eval($hook) : null;
			$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
			$banned_user_info = $forum_db->fetch_row($result);
			if (!$banned_user_info)
			{
				message($lang_admin_bans['No user id message']);
			}

			list($group_id, $ban_user, $ban_email, $ban_ip) = $banned_user_info;
		}
		else	// Otherwise the username is in POST
		{
			$ban_user = forum_trim($_POST['new_ban_user']);

			($hook = get_hook('aba_add_ban_form_submitted')) ? eval($hook) : null;

			if ($ban_user != '')
			{
				$query = array(
					'SELECT'	=> 'u.id, u.group_id, u.username, u.email, u.registration_ip',
					'FROM'		=> 'users AS u',
					'WHERE'		=> 'u.username=\''.$forum_db->escape($ban_user).'\' AND u.id>1'
				);

				($hook = get_hook('aba_add_ban_qr_get_user_by_username')) ? eval($hook) : null;
				$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
				$banned_user_info = $forum_db->fetch_row($result);
				if (!$banned_user_info)
				{
					message($lang_admin_bans['No user username message']);
				}

				list($user_id, $group_id, $ban_user, $ban_email, $ban_ip) = $banned_user_info;
			}
		}

		// Make sure we're not banning an admin
		if (isset($group_id) && $group_id == FORUM_ADMIN)
			message($lang_admin_bans['User is admin message']);

		// If we have a $user_id, we can try to find the last known IP of that user
		if (isset($user_id))
		{
			$query = array(
				'SELECT'	=> 'p.poster_ip',
				'FROM'		=> 'posts AS p',
				'WHERE'		=> 'p.poster_id='.$user_id,
				'ORDER BY'	=> 'p.posted DESC',
				'LIMIT'		=> '1'
			);

			($hook = get_hook('aba_add_ban_qr_get_last_known_ip')) ? eval($hook) : null;
			$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
			$ban_ip_from_db = $forum_db->result($result);

			if ($ban_ip_from_db)
			{
				$ban_ip = $ban_ip_from_db;
			}
		}

		$mode = 'add';
	}
	else	// We are editing a ban
	{
		$ban_id = intval($_GET['edit_ban']);
		if ($ban_id < 1)
			message($lang_common['Bad request']);

		($hook = get_hook('aba_edit_ban_selected')) ? eval($hook) : null;

		$query = array(
			'SELECT'	=> 'b.username, b.ip, b.email, b.message, b.expire',
			'FROM'		=> 'bans AS b',
			'WHERE'		=> 'b.id='.$ban_id
		);

		($hook = get_hook('aba_edit_ban_qr_get_ban_data')) ? eval($hook) : null;
		$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
		$banned_user_info = $forum_db->fetch_row($result);

		if (!$banned_user_info)
		{
			message($lang_common['Bad request']);
		}

		list($ban_user, $ban_ip, $ban_email, $ban_message, $ban_expire) = $banned_user_info;

		// We just use GMT for expire dates, as its a date rather than a day I don't think its worth worrying about
		$ban_expire = ($ban_expire != '') ? gmdate('Y-m-d', $ban_expire) : '';

		$mode = 'edit';
	}


	// Setup the form
	$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array($forum_config['o_board_title'], forum_link($forum_url['index'])),
		array($lang_admin_common['Forum administration'], forum_link($forum_url['admin_index']))
	);
	if ($forum_user['g_id'] == FORUM_ADMIN)
		$forum_page['crumbs'][] = array($lang_admin_common['Users'], forum_link($forum_url['admin_users']));
	$forum_page['crumbs'][] = array($lang_admin_common['Bans'], forum_link($forum_url['admin_bans']));
	$forum_page['crumbs'][] = $lang_admin_bans['Ban advanced'];

	($hook = get_hook('aba_add_edit_ban_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE_SECTION', 'users');
	define('FORUM_PAGE', 'admin-bans');
	require FORUM_ROOT.'header.php';

	// START SUBST - <!-- forum_main -->
	ob_start();

	($hook = get_hook('aba_add_edit_ban_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo $lang_admin_bans['Ban advanced heading'] ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<div class="ct-box warn-box">
			<p class="warn"><?php echo $lang_admin_bans['Ban IP warning'] ?></p>
		</div>
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo forum_link($forum_url['admin_bans']) ?>">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token(forum_link($forum_url['admin_bans'])) ?>" />
				<input type="hidden" name="mode" value="<?php echo $mode ?>" />
<?php if ($mode == 'edit'): ?>
				<input type="hidden" name="ban_id" value="<?php echo $ban_id ?>" />
<?php endif; ?>
			</div>
<?php ($hook = get_hook('aba_add_edit_ban_pre_criteria_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><span><?php echo $lang_admin_bans['Ban criteria legend'] ?></span></legend>
<?php ($hook = get_hook('aba_add_edit_ban_pre_username')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_bans['Username to ban label'] ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="ban_user" size="40" maxlength="25" value="<?php if (isset($ban_user)) echo forum_htmlencode($ban_user); ?>" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aba_add_edit_ban_pre_email')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_bans['E-mail/domain to ban label'] ?></span> <small><?php echo $lang_admin_bans['E-mail/domain help'] ?></small></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="ban_email" size="40" maxlength="80" value="<?php if (isset($ban_email)) echo forum_htmlencode(strtolower($ban_email)); ?>" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aba_add_edit_ban_pre_ip')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_bans['IP-addresses to ban label'] ?></span> <small><?php echo $lang_admin_bans['IP-addresses help']; if ($ban_user != '' && isset($user_id)) echo ' '.$lang_admin_bans['IP-addresses help stats'].'<a href="'.forum_link($forum_url['admin_users']).'?ip_stats='.$user_id.'">'.$lang_admin_bans['IP-addresses help link'].'</a>' ?></small></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="ban_ip" size="40" maxlength="255" value="<?php if (isset($ban_ip)) echo $ban_ip; ?>" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aba_add_edit_ban_pre_message')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_bans['Ban message label'] ?></span> <small><?php echo $lang_admin_bans['Ban message help'] ?></small></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="ban_message" size="40" maxlength="255" value="<?php if (isset($ban_message)) echo forum_htmlencode($ban_message); ?>" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aba_add_edit_ban_pre_expire')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_bans['Expire date label'] ?></span> <small><?php echo $lang_admin_bans['Expire date help'] ?></small></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="ban_expire" size="20" maxlength="10" value="<?php if (isset($ban_expire)) echo $ban_expire; ?>" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aba_add_edit_ban_criteria_pre_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('aba_add_edit_ban_criteria_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="add_edit_ban" value=" <?php echo $lang_admin_bans['Save ban'] ?>" /></span>
			</div>
		</form>
	</div>
<?php

	($hook = get_hook('aba_add_edit_ban_end')) ? eval($hook) : null;

	$tpl_temp = forum_trim(ob_get_contents());
	$tpl_main = str_replace('<!-- forum_main -->', $tpl_temp, $tpl_main);
	ob_end_clean();
	// END SUBST - <!-- forum_main -->

	require FORUM_ROOT.'footer.php';
}


// Add/edit a ban (stage 2)
else if (isset($_POST['add_edit_ban']))
{
	$ban_user = forum_trim($_POST['ban_user']);
	$ban_ip = forum_trim($_POST['ban_ip']);
	$ban_email = strtolower(forum_trim($_POST['ban_email']));
	$ban_message = forum_trim($_POST['ban_message']);
	$ban_expire = forum_trim($_POST['ban_expire']);

	if ($ban_user == '' && $ban_ip == '' && $ban_email == '')
		message($lang_admin_bans['Must enter message']);
	else if (strtolower($ban_user) == 'guest')
		message($lang_admin_bans['Can\'t ban guest user']);

	($hook = get_hook('aba_add_edit_ban_form_submitted')) ? eval($hook) : null;

	// Validate IP/IP range (it's overkill, I know)
	if ($ban_ip != '')
	{
		$ban_ip = preg_replace('/[\s]{2,}/', ' ', $ban_ip);
		$addresses = explode(' ', $ban_ip);
		$addresses = array_map('trim', $addresses);

		for ($i = 0; $i < count($addresses); ++$i)
		{
			if (strpos($addresses[$i], ':') !== false)
			{
				$octets = explode(':', $addresses[$i]);


				for ($c = 0; $c < count($octets); ++$c)
				{

					$octets[$c] = ltrim($octets[$c], "0");

					if ($c > 7 || (!empty($octets[$c]) && !ctype_xdigit($octets[$c])) || intval($octets[$c], 16) > 65535)
						message($lang_admin_bans['Invalid IP message']);
				}

				$cur_address = implode(':', $octets);
				$addresses[$i] = $cur_address;
			}
			else
			{
				$octets = explode('.', $addresses[$i]);

				for ($c = 0; $c < count($octets); ++$c)
				{

					$octets[$c] = (strlen($octets[$c]) > 1) ? ltrim($octets[$c], "0") : $octets[$c];

					if ($c > 3 || !ctype_digit($octets[$c]) || intval($octets[$c]) > 255)
						message($lang_admin_bans['Invalid IP message']);
				}

				$cur_address = implode('.', $octets);
				$addresses[$i] = $cur_address;
			}
		}

		$ban_ip = implode(' ', $addresses);
	}

	if (!defined('FORUM_EMAIL_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/email.php';

	if ($ban_email != '' && !is_valid_email($ban_email))
	{
		if (!preg_match('/^[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/', $ban_email))
			message($lang_admin_bans['Invalid e-mail message']);
	}

	if ($ban_expire != '' && $ban_expire != 'Never')
	{
		$ban_expire = strtotime($ban_expire);

		if ($ban_expire == -1 || $ban_expire <= time())
			message($lang_admin_bans['Invalid expire message']);
	}
	else
		$ban_expire = 'NULL';

	$ban_user = ($ban_user != '') ? '\''.$forum_db->escape($ban_user).'\'' : 'NULL';
	$ban_ip = ($ban_ip != '') ? '\''.$forum_db->escape($ban_ip).'\'' : 'NULL';
	$ban_email = ($ban_email != '') ? '\''.$forum_db->escape($ban_email).'\'' : 'NULL';
	$ban_message = ($ban_message != '') ? '\''.$forum_db->escape($ban_message).'\'' : 'NULL';

	if ($_POST['mode'] == 'add')
	{
		$query = array(
			'INSERT'	=> 'username, ip, email, message, expire, ban_creator',
			'INTO'		=> 'bans',
			'VALUES'	=> $ban_user.', '.$ban_ip.', '.$ban_email.', '.$ban_message.', '.$ban_expire.', '.$forum_user['id']
		);

		($hook = get_hook('aba_add_edit_ban_qr_add_ban')) ? eval($hook) : null;
		$forum_db->query_build($query) or error(__FILE__, __LINE__);
	}
	else
	{
		$query = array(
			'UPDATE'	=> 'bans',
			'SET'		=> 'username='.$ban_user.', ip='.$ban_ip.', email='.$ban_email.', message='.$ban_message.', expire='.$ban_expire,
			'WHERE'		=> 'id='.intval($_POST['ban_id'])
		);

		($hook = get_hook('aba_qr_update_ban')) ? eval($hook) : null;
		$forum_db->query_build($query) or error(__FILE__, __LINE__);
	}

	// Regenerate the bans cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_bans_cache();

	$forum_flash->add_info((($_POST['mode'] == 'edit') ? $lang_admin_bans['Ban edited'] : $lang_admin_bans['Ban added']));

	($hook = get_hook('aba_add_edit_ban_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['admin_bans']), (($_POST['mode'] == 'edit') ? $lang_admin_bans['Ban edited'] : $lang_admin_bans['Ban added']));
}


// Remove a ban
else if (isset($_GET['del_ban']))
{
	$ban_id = intval($_GET['del_ban']);
	if ($ban_id < 1)
		message($lang_common['Bad request']);

	// Validate the CSRF token
	if (!isset($_POST['csrf_token']) && (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== generate_form_token('del_ban'.$ban_id)))
		csrf_confirm_form();

	($hook = get_hook('aba_del_ban_form_submitted')) ? eval($hook) : null;

	$query = array(
		'DELETE'	=> 'bans',
		'WHERE'		=> 'id='.$ban_id
	);

	($hook = get_hook('aba_del_ban_qr_delete_ban')) ? eval($hook) : null;
	$forum_db->query_build($query) or error(__FILE__, __LINE__);

	// Regenerate the bans cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_bans_cache();

	$forum_flash->add_info($lang_admin_bans['Ban removed']);

	($hook = get_hook('aba_del_ban_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['admin_bans']), $lang_admin_bans['Ban removed']);
}


// Setup the form
$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;
$forum_page['form_action'] = forum_link($forum_url['admin_bans']).'&amp;action=more';

$forum_page['hidden_fields'] = array(
	'csrf_token'	=> '<input type="hidden" name="csrf_token" value="'.generate_form_token($forum_page['form_action']).'" />'
);

// Setup breadcrumbs
$forum_page['crumbs'] = array(
	array($forum_config['o_board_title'], forum_link($forum_url['index'])),
	array($lang_admin_common['Forum administration'], forum_link($forum_url['admin_index']))
);
if ($forum_user['g_id'] == FORUM_ADMIN)
	$forum_page['crumbs'][] = array($lang_admin_common['Users'], forum_link($forum_url['admin_users']));
$forum_page['crumbs'][] = array($lang_admin_common['Bans'], forum_link($forum_url['admin_bans']));


// Fetch user count
$query = array(
	'SELECT'	=>	'COUNT(id)',
	'FROM'		=>	'bans'
);

$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
$forum_page['num_bans'] = $forum_db->result($result);
$forum_page['num_pages'] = ceil($forum_page['num_bans'] / $forum_user['disp_topics']);
$forum_page['page'] = (!isset($_GET['p']) || !is_numeric($_GET['p']) || $_GET['p'] <= 1 || $_GET['p'] > $forum_page['num_pages']) ? 1 : intval($_GET['p']);
$forum_page['start_from'] = $forum_user['disp_topics'] * ($forum_page['page'] - 1);
$forum_page['finish_at'] = min(($forum_user['disp_topics']), ($forum_page['num_bans']));

// Generate paging
$forum_page['page_post']['paging']='<p class="paging"><span class="pages">'.$lang_common['Pages'].'</span> '.paginate($forum_page['num_pages'], $forum_page['page'], $forum_url['admin_bans'], $lang_common['Paging separator'], null, true).'</p>';

// Navigation links for header and page numbering for title/meta description
if ($forum_page['page'] < $forum_page['num_pages'])
{
	$forum_page['nav']['last'] = '<link rel="last" href="'.forum_sublink($forum_url['admin_bans'], $forum_url['page'], $forum_page['num_pages']).'" title="'.$lang_common['Page'].' '.$forum_page['num_pages'].'" />';
	$forum_page['nav']['next'] = '<link rel="next" href="'.forum_sublink($forum_url['admin_bans'], $forum_url['page'], ($forum_page['page'] + 1)).'" title="'.$lang_common['Page'].' '.($forum_page['page'] + 1).'" />';
}
if ($forum_page['page'] > 1)
{
	$forum_page['nav']['prev'] = '<link rel="prev" href="'.forum_sublink($forum_url['admin_bans'], $forum_url['page'], ($forum_page['page'] - 1)).'" title="'.$lang_common['Page'].' '.($forum_page['page'] - 1).'" />';
	$forum_page['nav']['first'] = '<link rel="first" href="'.forum_link($forum_url['admin_bans']).'" title="'.$lang_common['Page'].' 1" />';
}

($hook = get_hook('aba_pre_header_load')) ? eval($hook) : null;

define('FORUM_PAGE_SECTION', 'users');
define('FORUM_PAGE', 'admin-bans');
require FORUM_ROOT.'header.php';

// START SUBST - <!-- forum_main -->
ob_start();

($hook = get_hook('aba_main_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo $lang_admin_bans['New ban heading'] ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<div class="ct-box">
			<p><?php echo $lang_admin_bans['Advanced ban info'] ?></p>
		</div>
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo $forum_page['form_action'] ?>">
			<div class="hidden">
				<?php echo implode("\n\t\t\t\t", $forum_page['hidden_fields'])."\n" ?>
			</div>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo $lang_admin_bans['New ban legend'] ?></strong></legend>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_bans['Username to ban label'] ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="new_ban_user" size="25" maxlength="25" /></span>
					</div>
				</div>
			</fieldset>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="add_ban" value=" <?php echo $lang_admin_bans['Add ban'] ?> " /></span>
			</div>
		</form>
	</div>
<?php

// Reset counters
$forum_page['group_count'] = $forum_page['item_count'] = 0;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo $lang_admin_bans['Existing bans heading'] ?></span></h2>
	</div>
	<div class="main-content main-frm">
<?php

if ($forum_page['num_bans'] > 0)
{

?>
		<div class="ct-group">
<?php

	// Grab the bans
	$query = array(
		'SELECT'	=> 'b.*, u.username AS ban_creator_username',
		'FROM'		=> 'bans AS b',
		'JOINS'		=> array(
			array(
				'LEFT JOIN'		=> 'users AS u',
				'ON'			=> 'u.id=b.ban_creator'
			)
		),
		'ORDER BY'	=> 'b.id',
		'LIMIT'		=> $forum_page['start_from'].', '.$forum_page['finish_at']
	);

	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

	$forum_page['item_num'] = 0;
	while ($cur_ban = $forum_db->fetch_assoc($result))
	{
		$forum_page['ban_info'] = array();
		$forum_page['ban_creator'] = ($cur_ban['ban_creator_username'] != '') ? '<a href="'.forum_link($forum_url['user'], $cur_ban['ban_creator']).'">'.forum_htmlencode($cur_ban['ban_creator_username']).'</a>' : $lang_admin_common['Unknown'];

		if ($cur_ban['username'] != '')
			$forum_page['ban_info']['username'] = '<li><span>'.$lang_admin_bans['Username'].'</span> <strong>'.forum_htmlencode($cur_ban['username']).'</strong></li>';

		if ($cur_ban['email'] != '')
			$forum_page['ban_info']['email'] = '<li><span>'.$lang_admin_bans['E-mail'].'</span> <strong>'.forum_htmlencode($cur_ban['email']).'</strong></li>';

		if ($cur_ban['ip'] != '')
			$forum_page['ban_info']['ip'] = '<li><span>'.$lang_admin_bans['IP-ranges'].'</span> <strong>'.$cur_ban['ip'].'</strong></li>';

		if ($cur_ban['expire'] != '')
			$forum_page['ban_info']['expire'] = '<li><span>'.$lang_admin_bans['Expires'].'</span> <strong>'.format_time($cur_ban['expire'], 1).'</strong></li>';

		if ($cur_ban['message'] != '')
			$forum_page['ban_info']['message'] ='<li><span>'.$lang_admin_bans['Message'].'</span> <strong>'.forum_htmlencode($cur_ban['message']).'</strong></li>';

		($hook = get_hook('aba_view_ban_pre_display')) ? eval($hook) : null;

?>
			<div class="ct-set set<?php echo ++$forum_page['item_num'] ?>">
				<div class="ct-box">
					<div class="ct-legend">
						<h3><span><?php printf($lang_admin_bans['Current ban head'], $forum_page['ban_creator']) ?></span></h3>
						<p><?php printf($lang_admin_bans['Edit or remove'], '<a href="'.forum_link($forum_url['admin_bans']).'&amp;edit_ban='.$cur_ban['id'].'">'.$lang_admin_bans['Edit ban'].'</a>', '<a href="'.forum_link($forum_url['admin_bans']).'&amp;del_ban='.$cur_ban['id'].'&amp;csrf_token='.generate_form_token('del_ban'.$cur_ban['id']).'">'.$lang_admin_bans['Remove ban'].'</a>') ?></p>
					</div>
<?php if (!empty($forum_page['ban_info'])): ?>
				<ul>
					<?php echo implode("\n", $forum_page['ban_info'])."\n" ?>
					</ul>
<?php endif; ?>
				</div>
			</div>
<?php

	}

?>
		</div>
<?php

}
else
{

?>
		<div class="ct-box">
			<p><?php echo $lang_admin_bans['No bans'] ?></p>
		</div>
<?php

}

?>
	</div>
<?php

($hook = get_hook('aba_end')) ? eval($hook) : null;

$tpl_temp = forum_trim(ob_get_contents());
$tpl_main = str_replace('<!-- forum_main -->', $tpl_temp, $tpl_main);
ob_end_clean();
// END SUBST - <!-- forum_main -->

require FORUM_ROOT.'footer.php';
