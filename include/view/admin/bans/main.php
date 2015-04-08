<?php

($hook = get_hook('aba_main_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?= __('New ban heading', 'admin_bans') ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<div class="ct-box">
			<p><?= __('Advanced ban info', 'admin_bans') ?></p>
		</div>
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo $forum_page['form_action'] ?>">
			<div class="hidden">
				<?php echo implode("\n\t\t\t\t", $forum_page['hidden_fields'])."\n" ?>
			</div>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?= __('New ban legend', 'admin_bans') ?></strong></legend>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Username to ban label', 'admin_bans') ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="new_ban_user" size="25" maxlength="25" /></span>
					</div>
				</div>
			</fieldset>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="add_ban" value=" <?= __('Add ban', 'admin_bans') ?> " /></span>
			</div>
		</form>
	</div>
<?php

// Reset counters
$forum_page['group_count'] = $forum_page['item_count'] = 0;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?= __('Existing bans heading', 'admin_bans') ?></span></h2>
	</div>
	<div class="main-content main-frm">
<?php

if ($forum_page['num_bans'] > 0)
{

?>
		<div class="ct-group">
<?php

	$forum_page['item_num'] = 0;
	while ($cur_ban = $forum_db->fetch_assoc($result))
	{
		$forum_page['ban_info'] = array();
		$forum_page['ban_creator'] = ($cur_ban['ban_creator_username'] != '') ? '<a href="'.forum_link($forum_url['user'], $cur_ban['ban_creator']).'">'.forum_htmlencode($cur_ban['ban_creator_username']).'</a>' :
			__('Unknown', 'admin_common');

		if ($cur_ban['username'] != '')
			$forum_page['ban_info']['username'] = '<li><span>'.
				__('Username', 'admin_bans') . '</span> <strong>'.forum_htmlencode($cur_ban['username']).'</strong></li>';

		if ($cur_ban['email'] != '')
			$forum_page['ban_info']['email'] = '<li><span>'.
			__('E-mail', 'admin_bans') . '</span> <strong>'.forum_htmlencode($cur_ban['email']).'</strong></li>';

		if ($cur_ban['ip'] != '')
			$forum_page['ban_info']['ip'] = '<li><span>'.
			__('IP-ranges', 'admin_bans') . '</span> <strong>'.$cur_ban['ip'].'</strong></li>';

		if ($cur_ban['expire'] != '')
			$forum_page['ban_info']['expire'] = '<li><span>'.
			__('Expires', 'admin_bans') . '</span> <strong>'.format_time($cur_ban['expire'], 1).'</strong></li>';

		if ($cur_ban['message'] != '')
			$forum_page['ban_info']['message'] ='<li><span>'.
			__('Message', 'admin_bans') . '</span> <strong>'.forum_htmlencode($cur_ban['message']).'</strong></li>';

		($hook = get_hook('aba_view_ban_pre_display')) ? eval($hook) : null;

?>
			<div class="ct-set set<?php echo ++$forum_page['item_num'] ?>">
				<div class="ct-box">
					<div class="ct-legend">
						<h3><span><?php printf(__('Current ban head', 'admin_bans'), $forum_page['ban_creator']) ?></span></h3>
						<p><?php printf(__('Edit or remove', 'admin_bans'), '<a href="'.forum_link($forum_url['admin_bans']).'&amp;edit_ban='.$cur_ban['id'].'">'.
							__('Edit ban', 'admin_bans') . '</a>', '<a href="'.forum_link($forum_url['admin_bans']).'&amp;del_ban='.$cur_ban['id'].'&amp;csrf_token='.generate_form_token('del_ban'.$cur_ban['id']).'">'.
							__('Remove ban', 'admin_bans') . '</a>') ?></p>
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
			<p><?= __('No bans', 'admin_bans') ?></p>
		</div>
<?php

}

?>
	</div>
<?php

($hook = get_hook('aba_end')) ? eval($hook) : null;
