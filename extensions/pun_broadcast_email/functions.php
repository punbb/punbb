<?php
/**
 * pun_broadcast_email functions
 *
 * @copyright (C) 2008-2009 PunBB
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package pun_broadcast_email
 */
 
// Make sure no one attempts to run this script "directly"
if (!defined('FORUM'))
	exit;

function pun_broadcast_email_parse_string($subject, $user_info)
{	
	$tpl_vars = pun_broadcast_email_gen_tpl_vars($user_info);
	foreach ($tpl_vars as $tpl_var => $tpl_value)
		$subject = str_ireplace($tpl_var, $tpl_value, $subject);

	return $subject;
}

function pun_broadcast_email_gen_tpl_vars($user_data)
{
	global $forum_url;

	$tpl_vars = array();
	$tpl_vars['%_username_%'] = $user_data['username'];
	$tpl_vars['%_title_%'] = $user_data['title'];
	$tpl_vars['%_realname_%'] = $user_data['realname'];
	$tpl_vars['%_num_posts_%'] = $user_data['num_posts'];
	$tpl_vars['%_last_post_%'] = format_time($user_data['last_post']);
	$tpl_vars['%_registered_%'] = format_time($user_data['registered']);
	$tpl_vars['%_registration_ip_%'] = $user_data['registration_ip'];
	$tpl_vars['%_last_visit_%'] = format_time($user_data['last_visit']);
	$tpl_vars['%_admin_note_%'] = $user_data['admin_note'];
	$tpl_vars['%_profile_url_%'] = forum_link($forum_url['user'], $user_data['id']);

	return $tpl_vars;
}

function pun_broadcast_email_send_mail($subject, $message, $user_data, $parse_message = TRUE)
{
	$tmp_subject = $parse_message ? pun_broadcast_email_parse_string($subject, $user_data) : $subject;
	$tmp_message = $parse_message ? pun_broadcast_email_parse_string($message, $user_data) : $message;

	forum_mail($user_data['email'], $tmp_subject, $tmp_message);
}


?>