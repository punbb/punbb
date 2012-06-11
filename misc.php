<?php
/**
 * Provides various features for forum users (ie: display rules, send emails through the forum, mark a forum as read, etc).
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */


if (isset($_GET['action']))
	define('FORUM_QUIET_VISIT', 1);

if (!defined('FORUM_ROOT'))
	define('FORUM_ROOT', './');
require FORUM_ROOT.'include/common.php';

($hook = get_hook('mi_start')) ? eval($hook) : null;

// Load the misc.php language file
require FORUM_ROOT.'lang/'.$forum_user['language'].'/misc.php';


$action = isset($_GET['action']) ? $_GET['action'] : null;
$errors = array();

// Show the forum rules?
if ($action == 'rules')
{
	if ($forum_config['o_rules'] == '0' || ($forum_user['is_guest'] && $forum_user['g_read_board'] == '0' && $forum_config['o_regs_allow'] == '0'))
		message($lang_common['Bad request']);

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array($forum_config['o_board_title'], forum_link($forum_url['index'])),
		$lang_common['Rules']
	);

	($hook = get_hook('mi_rules_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE', 'rules');
	require FORUM_ROOT.'header.php';

	// START SUBST - <!-- forum_main -->
	ob_start();

	($hook = get_hook('mi_rules_output_start')) ? eval($hook) : null;

?>
	<div class="main-head">
		<h2 class="hn"><span><?php echo $lang_common['Rules'] ?></span></h2>
	</div>

	<div class="main-content main-frm">
		<div id="rules-content" class="ct-box user-box">
			<?php echo $forum_config['o_rules_message']."\n" ?>
		</div>
	</div>
<?php

	($hook = get_hook('mi_rules_end')) ? eval($hook) : null;

	$tpl_temp = forum_trim(ob_get_contents());
	$tpl_main = str_replace('<!-- forum_main -->', $tpl_temp, $tpl_main);
	ob_end_clean();
	// END SUBST - <!-- forum_main -->

	require FORUM_ROOT.'footer.php';
}


// Mark all topics/posts as read?
else if ($action == 'markread')
{
	if ($forum_user['is_guest'])
		message($lang_common['No permission']);

	// We validate the CSRF token. If it's set in POST and we're at this point, the token is valid.
	// If it's in GET, we need to make sure it's valid.
	if (!isset($_POST['csrf_token']) && (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== generate_form_token('markread'.$forum_user['id'])))
		csrf_confirm_form();

	($hook = get_hook('mi_markread_selected')) ? eval($hook) : null;

	$query = array(
		'UPDATE'	=> 'users',
		'SET'		=> 'last_visit='.$forum_user['logged'],
		'WHERE'		=> 'id='.$forum_user['id']
	);

	($hook = get_hook('mi_markread_qr_update_last_visit')) ? eval($hook) : null;
	$forum_db->query_build($query) or error(__FILE__, __LINE__);

	// Reset tracked topics
	set_tracked_topics(null);

	$forum_flash->add_info($lang_misc['Mark read redirect']);

	($hook = get_hook('mi_markread_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['index']), $lang_misc['Mark read redirect']);
}


// Mark the topics/posts in a forum as read?
else if ($action == 'markforumread')
{
	if ($forum_user['is_guest'])
		message($lang_common['No permission']);

	$fid = isset($_GET['fid']) ? intval($_GET['fid']) : 0;
	if ($fid < 1)
		message($lang_common['Bad request']);

	// We validate the CSRF token. If it's set in POST and we're at this point, the token is valid.
	// If it's in GET, we need to make sure it's valid.
	if (!isset($_POST['csrf_token']) && (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== generate_form_token('markforumread'.$fid.$forum_user['id'])))
		csrf_confirm_form();

	($hook = get_hook('mi_markforumread_selected')) ? eval($hook) : null;

	// Fetch some info about the forum
	$query = array(
		'SELECT'	=> 'f.forum_name',
		'FROM'		=> 'forums AS f',
		'JOINS'		=> array(
			array(
				'LEFT JOIN'		=> 'forum_perms AS fp',
				'ON'			=> '(fp.forum_id=f.id AND fp.group_id='.$forum_user['g_id'].')'
			)
		),
		'WHERE'		=> '(fp.read_forum IS NULL OR fp.read_forum=1) AND f.id='.$fid
	);

	($hook = get_hook('mi_markforumread_qr_get_forum_info')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	$forum_name = $forum_db->result($result);

	if (!$forum_name)
	{
		message($lang_common['Bad request']);
	}

	$tracked_topics = get_tracked_topics();
	$tracked_topics['forums'][$fid] = time();
	set_tracked_topics($tracked_topics);

	$forum_flash->add_info($lang_misc['Mark forum read redirect']);

	($hook = get_hook('mi_markforumread_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['forum'], array($fid, sef_friendly($forum_name))), $lang_misc['Mark forum read redirect']);
}

// OpenSearch plugin?
else if ($action == 'opensearch')
{
	// Send XML/no cache headers
	header('Content-Type: text/xml; charset=utf-8');
	header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');

	echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
	echo '<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/" xmlns:moz="http://www.mozilla.org/2006/browser/search/">'."\n";
	echo "\t".'<ShortName>'.forum_htmlencode($forum_config['o_board_title']).'</ShortName>'."\n";
	echo "\t".'<Description>'.forum_htmlencode($forum_config['o_board_desc']).'</Description>'."\n";
	echo "\t".'<InputEncoding>utf-8</InputEncoding>'."\n";
	echo "\t".'<OutputEncoding>utf-8</OutputEncoding>'."\n";
	echo "\t".'<Image width="16" height="16" type="image/x-icon">'.$base_url.'/favicon.ico</Image>'."\n";
	echo "\t".'<Url type="text/html" method="get" template="'.$base_url.'/search.php?action=search&amp;source=opensearch&amp;keywords={searchTerms}"/>'."\n";
	echo "\t".'<Url type="application/opensearchdescription+xml" rel="self" template="'.forum_link($forum_url['opensearch']).'"/>'."\n";
	echo "\t".'<Contact>'.forum_htmlencode($forum_config['o_admin_email']).'</Contact>'."\n";

	if ($forum_config['o_show_version'] == '1')
		echo "\t".'<Attribution>PunBB '.$forum_config['o_cur_version'].'</Attribution>'."\n";
	else
		echo "\t".'<Attribution>PunBB</Attribution>'."\n";

	echo "\t".'<moz:SearchForm>'.forum_link($forum_url['search']).'</moz:SearchForm>'."\n";
	echo '</OpenSearchDescription>'."\n";

	exit;
}


// Send form e-mail?
else if (isset($_GET['email']))
{
	if ($forum_user['is_guest'] || $forum_user['g_send_email'] == '0')
		message($lang_common['No permission']);

	$recipient_id = intval($_GET['email']);

	if ($recipient_id < 2)
		message($lang_common['Bad request']);

	($hook = get_hook('mi_email_selected')) ? eval($hook) : null;

	// User pressed the cancel button
	if (isset($_POST['cancel']))
		redirect(forum_htmlencode($_POST['redirect_url']), $lang_common['Cancel redirect']);

	$query = array(
		'SELECT'	=> 'u.username, u.email, u.email_setting',
		'FROM'		=> 'users AS u',
		'WHERE'		=> 'u.id='.$recipient_id
	);

	($hook = get_hook('mi_email_qr_get_form_email_data')) ? eval($hook) : null;

	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	$recipient_info = $forum_db->fetch_assoc($result);

	if (!$recipient_info)
	{
		message($lang_common['Bad request']);
	}

	if ($recipient_info['email_setting'] == 2 && !$forum_user['is_admmod'])
		message($lang_misc['Form e-mail disabled']);

	if ($recipient_info['email'] == '')
		message($lang_common['Bad request']);

	if (isset($_POST['form_sent']))
	{
		($hook = get_hook('mi_email_form_submitted')) ? eval($hook) : null;

		// Clean up message and subject from POST
		$subject = forum_trim($_POST['req_subject']);
		$message = forum_trim($_POST['req_message']);

		if ($subject == '')
			$errors[] = $lang_misc['No e-mail subject'];
		else if (utf8_strlen($subject) > FORUM_SUBJECT_MAXIMUM_LENGTH)
	     	$errors[] = sprintf($lang_misc['Too long e-mail subject'], FORUM_SUBJECT_MAXIMUM_LENGTH);

		if ($message == '')
			$errors[] = $lang_misc['No e-mail message'];
		else if (strlen($message) > FORUM_MAX_POSTSIZE_BYTES)
			$errors[] = sprintf($lang_misc['Too long e-mail message'],
				forum_number_format(strlen($message)), forum_number_format(FORUM_MAX_POSTSIZE_BYTES));

		if ($forum_user['last_email_sent'] != '' && (time() - $forum_user['last_email_sent']) < $forum_user['g_email_flood'] && (time() - $forum_user['last_email_sent']) >= 0)
			$errors[] = sprintf($lang_misc['Email flood'], $forum_user['g_email_flood']);

		($hook = get_hook('mi_email_end_validation')) ? eval($hook) : null;

		// Did everything go according to plan?
		if (empty($errors))
		{
			// Load the "form e-mail" template
			$mail_tpl = forum_trim(file_get_contents(FORUM_ROOT.'lang/'.$forum_user['language'].'/mail_templates/form_email.tpl'));

			// The first row contains the subject
			$first_crlf = strpos($mail_tpl, "\n");
			$mail_subject = forum_trim(substr($mail_tpl, 8, $first_crlf-8));
			$mail_message = forum_trim(substr($mail_tpl, $first_crlf));

			$mail_subject = str_replace('<mail_subject>', $subject, $mail_subject);
			$mail_message = str_replace('<sender>', $forum_user['username'], $mail_message);
			$mail_message = str_replace('<board_title>', $forum_config['o_board_title'], $mail_message);
			$mail_message = str_replace('<mail_message>', $message, $mail_message);
			$mail_message = str_replace('<board_mailer>', sprintf($lang_common['Forum mailer'], $forum_config['o_board_title']), $mail_message);

			($hook = get_hook('mi_email_new_replace_data')) ? eval($hook) : null;

			if (!defined('FORUM_EMAIL_FUNCTIONS_LOADED'))
				require FORUM_ROOT.'include/email.php';

			forum_mail($recipient_info['email'], $mail_subject, $mail_message, $forum_user['email'], $forum_user['username']);

			// Set the user's last_email_sent time
			$query = array(
				'UPDATE'	=> 'users',
				'SET'		=> 'last_email_sent='.time(),
				'WHERE'		=> 'id='.$forum_user['id'],
			);

			($hook = get_hook('mi_email_qr_update_last_email_sent')) ? eval($hook) : null;
			$forum_db->query_build($query) or error(__FILE__, __LINE__);

			$forum_flash->add_info($lang_misc['E-mail sent redirect']);

			($hook = get_hook('mi_email_pre_redirect')) ? eval($hook) : null;

			redirect(forum_htmlencode($_POST['redirect_url']), $lang_misc['E-mail sent redirect']);
		}
	}

	// Setup form
	$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;
	$forum_page['form_action'] = forum_link($forum_url['email'], $recipient_id);

	$forum_page['hidden_fields'] = array(
		'form_sent'		=> '<input type="hidden" name="form_sent" value="1" />',
		'redirect_url'	=> '<input type="hidden" name="redirect_url" value="'.forum_htmlencode($forum_user['prev_url']).'" />',
		'csrf_token'	=> '<input type="hidden" name="csrf_token" value="'.generate_form_token($forum_page['form_action']).'" />'
	);

	// Setup main heading
	$forum_page['main_head'] = sprintf($lang_misc['Send forum e-mail'], forum_htmlencode($recipient_info['username']));

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array($forum_config['o_board_title'], forum_link($forum_url['index'])),
		sprintf($lang_misc['Send forum e-mail'], forum_htmlencode($recipient_info['username']))
	);

	($hook = get_hook('mi_email_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE', 'formemail');
	require FORUM_ROOT.'header.php';

	// START SUBST - <!-- forum_main -->
	ob_start();

	($hook = get_hook('mi_email_output_start')) ? eval($hook) : null;

?>
	<div class="main-head">
		<h2 class="hn"><span><?php echo $forum_page['main_head'] ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<div class="ct-box warn-box">
			<p class="important"><?php echo $lang_misc['E-mail disclosure note'] ?></p>
		</div>
<?php

	// If there were any errors, show them
	if (!empty($errors))
	{
		$forum_page['errors'] = array();
		foreach ($errors as $cur_error)
			$forum_page['errors'][] = '<li class="warn"><span>'.$cur_error.'</span></li>';

		($hook = get_hook('mi_pre_email_errors')) ? eval($hook) : null;

?>
		<div class="ct-box error-box">
			<h2 class="warn hn"><?php echo $lang_misc['Form e-mail errors'] ?></h2>
			<ul class="error-list">
				<?php echo implode("\n\t\t\t\t", $forum_page['errors'])."\n" ?>
			</ul>
		</div>
<?php

	}

?>
		<div id="req-msg" class="req-warn ct-box error-box">
			<p class="important"><?php echo $lang_common['Required warn'] ?></p>
		</div>
		<form id="afocus" class="frm-form" method="post" accept-charset="utf-8" action="<?php echo $forum_page['form_action'] ?>">
			<div class="hidden">
				<?php echo implode("\n\t\t\t\t", $forum_page['hidden_fields'])."\n" ?>
			</div>
<?php ($hook = get_hook('mi_email_pre_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo $lang_misc['Write e-mail'] ?></strong></legend>
<?php ($hook = get_hook('mi_email_pre_subject')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text required longtext">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_misc['E-mail subject'] ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="req_subject" value="<?php echo(isset($_POST['req_subject']) ? forum_htmlencode($_POST['req_subject']) : '') ?>" size="<?php echo FORUM_SUBJECT_MAXIMUM_LENGTH ?>" maxlength="<?php echo FORUM_SUBJECT_MAXIMUM_LENGTH ?>" required /></span>
					</div>
				</div>
<?php ($hook = get_hook('mi_email_pre_message_contents')) ? eval($hook) : null; ?>
				<div class="txt-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="txt-box textarea required">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_misc['E-mail message'] ?></span></label>
						<div class="txt-input"><span class="fld-input"><textarea id="fld<?php echo $forum_page['fld_count'] ?>" name="req_message" rows="10" cols="95" required><?php echo(isset($_POST['req_message']) ? forum_htmlencode($_POST['req_message']) : '') ?></textarea></span></div>
					</div>
				</div>
<?php ($hook = get_hook('mi_email_pre_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('mi_email_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="submit" value="<?php echo $lang_common['Submit'] ?>" /></span>
				<span class="cancel"><input type="submit" name="cancel" value="<?php echo $lang_common['Cancel'] ?>" formnovalidate /></span>
			</div>
		</form>
	</div>
<?php

	($hook = get_hook('mi_email_end')) ? eval($hook) : null;

	$tpl_temp = forum_trim(ob_get_contents());
	$tpl_main = str_replace('<!-- forum_main -->', $tpl_temp, $tpl_main);
	ob_end_clean();
	// END SUBST - <!-- forum_main -->

	require FORUM_ROOT.'footer.php';
}


// Report a post?
else if (isset($_GET['report']))
{
	if ($forum_user['is_guest'])
		message($lang_common['No permission']);

	$post_id = intval($_GET['report']);
	if ($post_id < 1)
		message($lang_common['Bad request']);


	($hook = get_hook('mi_report_selected')) ? eval($hook) : null;

	// User pressed the cancel button
	if (isset($_POST['cancel']))
		redirect(forum_link($forum_url['post'], $post_id), $lang_common['Cancel redirect']);


	if (isset($_POST['form_sent']))
	{
		($hook = get_hook('mi_report_form_submitted')) ? eval($hook) : null;

		// Start with a clean slate
		$errors = array();

		// Flood protection
		if ($forum_user['last_email_sent'] != '' && (time() - $forum_user['last_email_sent']) < $forum_user['g_email_flood'] && (time() - $forum_user['last_email_sent']) >= 0)
			message(sprintf($lang_misc['Report flood'], $forum_user['g_email_flood']));

		// Clean up reason from POST
		$reason = forum_linebreaks(forum_trim($_POST['req_reason']));
		if ($reason == '')
			message($lang_misc['No reason']);

		if (strlen($reason) > FORUM_MAX_POSTSIZE_BYTES)
		{
			$errors[] = sprintf($lang_misc['Too long reason'], forum_number_format(strlen($reason)), forum_number_format(FORUM_MAX_POSTSIZE_BYTES));
		}

		if (empty($errors)) {
			// Get some info about the topic we're reporting
			$query = array(
				'SELECT'	=> 't.id, t.subject, t.forum_id',
				'FROM'		=> 'posts AS p',
				'JOINS'		=> array(
					array(
						'INNER JOIN'	=> 'topics AS t',
						'ON'			=> 't.id=p.topic_id'
					)
				),
				'WHERE'		=> 'p.id='.$post_id
			);

			($hook = get_hook('mi_report_qr_get_topic_data')) ? eval($hook) : null;
			$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
			$topic_info = $forum_db->fetch_assoc($result);

			if (!$topic_info)
			{
				message($lang_common['Bad request']);
			}

			($hook = get_hook('mi_report_pre_reports_sent')) ? eval($hook) : null;

			// Should we use the internal report handling?
			if ($forum_config['o_report_method'] == 0 || $forum_config['o_report_method'] == 2)
			{
				$query = array(
					'INSERT'	=> 'post_id, topic_id, forum_id, reported_by, created, message',
					'INTO'		=> 'reports',
					'VALUES'	=> $post_id.', '.$topic_info['id'].', '.$topic_info['forum_id'].', '.$forum_user['id'].', '.time().', \''.$forum_db->escape($reason).'\''
				);

				($hook = get_hook('mi_report_add_report')) ? eval($hook) : null;
				$forum_db->query_build($query) or error(__FILE__, __LINE__);
			}

			// Should we e-mail the report?
			if ($forum_config['o_report_method'] == 1 || $forum_config['o_report_method'] == 2)
			{
				// We send it to the complete mailing-list in one swoop
				if ($forum_config['o_mailing_list'] != '')
				{
					$mail_subject = 'Report('.$topic_info['forum_id'].') - \''.$topic_info['subject'].'\'';
					$mail_message = 'User \''.$forum_user['username'].'\' has reported the following message:'."\n".forum_link($forum_url['post'], $post_id)."\n\n".'Reason:'."\n".$reason;

					if (!defined('FORUM_EMAIL_FUNCTIONS_LOADED'))
						require FORUM_ROOT.'include/email.php';

					($hook = get_hook('mi_report_modify_message')) ? eval($hook) : null;

					forum_mail($forum_config['o_mailing_list'], $mail_subject, $mail_message);
				}
			}

			// Set last_email_sent time to prevent flooding
			$query = array(
				'UPDATE'	=> 'users',
				'SET'		=> 'last_email_sent='.time(),
				'WHERE'		=> 'id='.$forum_user['id']
			);

			($hook = get_hook('mi_report_qr_update_last_email_sent')) ? eval($hook) : null;
			$forum_db->query_build($query) or error(__FILE__, __LINE__);

			$forum_flash->add_info($lang_misc['Report redirect']);

			($hook = get_hook('mi_report_pre_redirect')) ? eval($hook) : null;

			redirect(forum_link($forum_url['post'], $post_id), $lang_misc['Report redirect']);
		}
	}

	// Setup form
	$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;
	$forum_page['form_action'] = forum_link($forum_url['report'], $post_id);

	$forum_page['hidden_fields'] = array(
		'form_sent'		=> '<input type="hidden" name="form_sent" value="1" />',
		'csrf_token'	=> '<input type="hidden" name="csrf_token" value="'.generate_form_token($forum_page['form_action']).'" />'
	);

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array($forum_config['o_board_title'], forum_link($forum_url['index'])),
		$lang_misc['Report post']
	);

	// Setup main heading
	$forum_page['main_head'] = end($forum_page['crumbs']);

	($hook = get_hook('mi_report_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE', 'report');
	require FORUM_ROOT.'header.php';

	// START SUBST - <!-- forum_main -->
	ob_start();

	($hook = get_hook('mi_report_output_start')) ? eval($hook) : null;

?>
	<div class="main-head">
		<h2 class="hn"><span><?php echo $forum_page['main_head'] ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<div id="req-msg" class="req-warn ct-box error-box">
			<p class="important"><?php echo $lang_common['Required warn'] ?></p>
		</div>
<?php
		// If there were any errors, show them
		if (!empty($errors)) {
			$forum_page['errors'] = array();
			foreach ($errors as $cur_error) {
				$forum_page['errors'][] = '<li class="warn"><span>'.$cur_error.'</span></li>';
			}

			($hook = get_hook('mi_pre_report_errors')) ? eval($hook) : null;
?>
		<div class="ct-box error-box">
			<h2 class="warn hn"><?php echo $lang_misc['Report errors'] ?></h2>
			<ul class="error-list">
				<?php echo implode("\n\t\t\t\t", $forum_page['errors'])."\n" ?>
			</ul>
		</div>
<?php
		}
?>
		<form id="afocus" class="frm-form" method="post" accept-charset="utf-8" action="<?php echo $forum_page['form_action'] ?>">
			<div class="hidden">
				<?php echo implode("\n\t\t\t\t", $forum_page['hidden_fields'])."\n" ?>
			</div>
<?php ($hook = get_hook('mi_report_pre_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo $lang_common['Required information'] ?></strong></legend>
<?php ($hook = get_hook('mi_report_pre_reason')) ? eval($hook) : null; ?>
				<div class="txt-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="txt-box textarea required">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_misc['Reason'] ?></span> <small><?php echo $lang_misc['Reason help'] ?></small></label><br />
						<div class="txt-input"><span class="fld-input"><textarea id="fld<?php echo $forum_page['fld_count'] ?>" name="req_reason" rows="5" cols="60" required></textarea></span></div>
					</div>
				</div>
<?php ($hook = get_hook('mi_report_pre_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('mi_report_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="submit" value="<?php echo $lang_common['Submit'] ?>" /></span>
				<span class="cancel"><input type="submit" name="cancel" value="<?php echo $lang_common['Cancel'] ?>" formnovalidate /></span>
			</div>
		</form>
	</div>
<?php

	($hook = get_hook('mi_report_end')) ? eval($hook) : null;

	$tpl_temp = forum_trim(ob_get_contents());
	$tpl_main = str_replace('<!-- forum_main -->', $tpl_temp, $tpl_main);
	ob_end_clean();
	// END SUBST - <!-- forum_main -->

	require FORUM_ROOT.'footer.php';
}


// Subscribe to a topic?
else if (isset($_GET['subscribe']))
{
	if ($forum_user['is_guest'] || $forum_config['o_subscriptions'] != '1')
		message($lang_common['No permission']);

	$topic_id = intval($_GET['subscribe']);
	if ($topic_id < 1)
		message($lang_common['Bad request']);

	// We validate the CSRF token. If it's set in POST and we're at this point, the token is valid.
	// If it's in GET, we need to make sure it's valid.
	if (!isset($_POST['csrf_token']) && (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== generate_form_token('subscribe'.$topic_id.$forum_user['id'])))
		csrf_confirm_form();

	($hook = get_hook('mi_subscribe_selected')) ? eval($hook) : null;

	// Make sure the user can view the topic
	$query = array(
		'SELECT'	=> 'subject',
		'FROM'		=> 'topics AS t',
		'JOINS'		=> array(
			array(
				'LEFT JOIN'		=> 'forum_perms AS fp',
				'ON'			=> '(fp.forum_id=t.forum_id AND fp.group_id='.$forum_user['g_id'].')'
			)
		),
		'WHERE'		=> '(fp.read_forum IS NULL OR fp.read_forum=1) AND t.id='.$topic_id.' AND t.moved_to IS NULL'
	);
	($hook = get_hook('mi_subscribe_qr_topic_exists')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	$subject = $forum_db->result($result);

	if (!$subject)
	{
		message($lang_common['Bad request']);
	}

	$query = array(
		'SELECT'	=> 'COUNT(s.user_id)',
		'FROM'		=> 'subscriptions AS s',
		'WHERE'		=> 'user_id='.$forum_user['id'].' AND topic_id='.$topic_id
	);

	($hook = get_hook('mi_subscribe_qr_check_subscribed')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

	if ($forum_db->result($result) > 0)
	{
		message($lang_misc['Already subscribed']);
	}

	$query = array(
		'INSERT'	=> 'user_id, topic_id',
		'INTO'		=> 'subscriptions',
		'VALUES'	=> $forum_user['id'].' ,'.$topic_id
	);

	($hook = get_hook('mi_subscribe_add_subscription')) ? eval($hook) : null;
	$forum_db->query_build($query) or error(__FILE__, __LINE__);

	$forum_flash->add_info($lang_misc['Subscribe redirect']);

	($hook = get_hook('mi_subscribe_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['topic'], array($topic_id, sef_friendly($subject))), $lang_misc['Subscribe redirect']);
}


// Unsubscribe from a topic?
else if (isset($_GET['unsubscribe']))
{
	if ($forum_user['is_guest'] || $forum_config['o_subscriptions'] != '1')
		message($lang_common['No permission']);

	$topic_id = intval($_GET['unsubscribe']);
	if ($topic_id < 1)
		message($lang_common['Bad request']);

	// We validate the CSRF token. If it's set in POST and we're at this point, the token is valid.
	// If it's in GET, we need to make sure it's valid.
	if (!isset($_POST['csrf_token']) && (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== generate_form_token('unsubscribe'.$topic_id.$forum_user['id'])))
		csrf_confirm_form();

	($hook = get_hook('mi_unsubscribe_selected')) ? eval($hook) : null;

	$query = array(
		'SELECT'	=> 't.subject',
		'FROM'		=> 'topics AS t',
		'JOINS'		=> array(
			array(
				'INNER JOIN'	=> 'subscriptions AS s',
				'ON'			=> 's.user_id='.$forum_user['id'].' AND s.topic_id=t.id'
			)
		),
		'WHERE'		=> 't.id='.$topic_id
	);

	($hook = get_hook('mi_unsubscribe_qr_check_subscribed')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	$subject = $forum_db->result($result);

	if (!$subject)
	{
		message($lang_misc['Not subscribed']);
	}

	$query = array(
		'DELETE'	=> 'subscriptions',
		'WHERE'		=> 'user_id='.$forum_user['id'].' AND topic_id='.$topic_id
	);

	($hook = get_hook('mi_unsubscribe_qr_delete_subscription')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

	$forum_flash->add_info($lang_misc['Unsubscribe redirect']);

	($hook = get_hook('mi_unsubscribe_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['topic'], array($topic_id, sef_friendly($subject))), $lang_misc['Unsubscribe redirect']);
}


// Subscribe to a forum?
else if (isset($_GET['forum_subscribe']))
{
	if ($forum_user['is_guest'] || $forum_config['o_subscriptions'] != '1')
		message($lang_common['No permission']);

	$forum_id = intval($_GET['forum_subscribe']);
	if ($forum_id < 1)
		message($lang_common['Bad request']);

	// We validate the CSRF token. If it's set in POST and we're at this point, the token is valid.
	// If it's in GET, we need to make sure it's valid.
	if (!isset($_POST['csrf_token']) && (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== generate_form_token('forum_subscribe'.$forum_id.$forum_user['id'])))
		csrf_confirm_form();

	($hook = get_hook('mi_forum_subscribe_selected')) ? eval($hook) : null;

	// Make sure the user can view the forum
	$query = array(
		'SELECT'	=> 'f.forum_name',
		'FROM'		=> 'forums AS f',
		'JOINS'		=> array(
			array(
				'LEFT JOIN'		=> 'forum_perms AS fp',
				'ON'			=> '(fp.forum_id=f.id AND fp.group_id='.$forum_user['g_id'].')'
			)
		),
		'WHERE'		=> '(fp.read_forum IS NULL OR fp.read_forum=1) AND f.id='.$forum_id
	);
	($hook = get_hook('mi_forum_subscribe_qr_forum_exists')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	$forum_name = $forum_db->result($result);

	if (!$forum_name)
	{
		message($lang_common['Bad request']);
	}

	$query = array(
		'SELECT'	=> 'COUNT(fs.user_id)',
		'FROM'		=> 'forum_subscriptions AS fs',
		'WHERE'		=> 'user_id='.$forum_user['id'].' AND forum_id='.$forum_id
	);

	($hook = get_hook('mi_forum_subscribe_qr_check_subscribed')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

	if ($forum_db->result($result) > 0)
	{
		message($lang_misc['Already subscribed']);
	}

	$query = array(
		'INSERT'	=> 'user_id, forum_id',
		'INTO'		=> 'forum_subscriptions',
		'VALUES'	=> $forum_user['id'].' ,'.$forum_id
	);

	($hook = get_hook('mi_forum_subscribe_add_subscription')) ? eval($hook) : null;
	$forum_db->query_build($query) or error(__FILE__, __LINE__);

	$forum_flash->add_info($lang_misc['Subscribe redirect']);

	($hook = get_hook('mi_forum_subscribe_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['forum'], array($forum_id, sef_friendly($forum_name))), $lang_misc['Subscribe redirect']);
}


// Unsubscribe from a topic?
else if (isset($_GET['forum_unsubscribe']))
{
	if ($forum_user['is_guest'] || $forum_config['o_subscriptions'] != '1')
		message($lang_common['No permission']);

	$forum_id = intval($_GET['forum_unsubscribe']);
	if ($forum_id < 1)
		message($lang_common['Bad request']);

	// We validate the CSRF token. If it's set in POST and we're at this point, the token is valid.
	// If it's in GET, we need to make sure it's valid.
	if (!isset($_POST['csrf_token']) && (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== generate_form_token('forum_unsubscribe'.$forum_id.$forum_user['id'])))
		csrf_confirm_form();

	($hook = get_hook('mi_forum_unsubscribe_selected')) ? eval($hook) : null;

	// Make sure the user can view the forum
	$query = array(
		'SELECT'	=> 'f.forum_name',
		'FROM'		=> 'forums AS f',
		'JOINS'		=> array(
			array(
				'LEFT JOIN'		=> 'forum_perms AS fp',
				'ON'			=> '(fp.forum_id=f.id AND fp.group_id='.$forum_user['g_id'].')'
			)
		),
		'WHERE'		=> '(fp.read_forum IS NULL OR fp.read_forum=1) AND f.id='.$forum_id
	);

	($hook = get_hook('mi_forum_unsubscribe_qr_check_subscribed')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	$forum_name = $forum_db->result($result);

	if (!$forum_name)
	{
		message($lang_misc['Not subscribed']);
	}

	$query = array(
		'DELETE'	=> 'forum_subscriptions',
		'WHERE'		=> 'user_id='.$forum_user['id'].' AND forum_id='.$forum_id
	);

	($hook = get_hook('mi_unsubscribe_qr_delete_subscription')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

	$forum_flash->add_info($lang_misc['Unsubscribe redirect']);

	($hook = get_hook('mi_forum_unsubscribe_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['forum'], array($forum_id, sef_friendly($forum_name))), $lang_misc['Unsubscribe redirect']);
}


($hook = get_hook('mi_new_action')) ? eval($hook) : null;

message($lang_common['Bad request']);
