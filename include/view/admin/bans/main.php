<?php

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
