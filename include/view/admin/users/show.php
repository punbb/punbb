<?php
namespace punbb;

	// Set up table headers
	$forum_page['table_header'] = array();
	$forum_page['table_header']['username'] = '<th class="tc'.count($forum_page['table_header']).'" scope="col">'.
		__('User information', 'admin_users').'</th>';
	$forum_page['table_header']['title'] = '<th class="tc'.count($forum_page['table_header']).'" scope="col">'.
		__('Title column', 'admin_users').'</th>';
	$forum_page['table_header']['posts'] = '<th class="tc'.count($forum_page['table_header']).'" scope="col">'.
		__('Posts', 'admin_users').'</th>';
	$forum_page['table_header']['actions'] = '<th class="tc'.count($forum_page['table_header']).'" scope="col">'.
		__('Actions', 'admin_users').'</th>';
	$forum_page['table_header']['select'] = '<th class="tc'.count($forum_page['table_header']).'" scope="col">'.
		__('Select', 'misc') . '</th>';

	if ($forum_page['num_users'] > 0)
		$forum_page['main_head_options']['select'] = $forum_page['main_foot_options']['select'] = '<span class="select-all js_link" data-check-form="aus-show-users-results-form">'.
		__('Select all', 'admin_common').'</span>';

	($hook = get_hook('aus_show_users_output_start')) ? eval($hook) : null;

?>
	<div class="main-head">
<?php

	if (!empty($forum_page['main_head_options']))
		echo "\n\t\t".'<p class="options">'.implode(' ', $forum_page['main_head_options']).'</p>';

?>
		<h2 class="hn"><span><?php printf(__('Users found', 'admin_users'), $forum_page['num_users']) ?></span></h2>
	</div>
	<form id="aus-show-users-results-form" class="frm-form" method="post" accept-charset="utf-8" action="<?php echo forum_link($forum_url['admin_users']) ?>?action=modify_users">
	<div class="main-content main-frm">
		<div class="hidden">
			<input type="hidden" name="csrf_token" value="<?php echo generate_form_token(forum_link($forum_url['admin_users']).'?action=modify_users') ?>" />
		</div>
		<table>
			<thead>
				<tr>
					<?php echo implode("\n\t\t\t\t", $forum_page['table_header'])."\n" ?>
				</tr>
			</thead>
			<tbody>
<?php

	if ($forum_page['num_users'] > 0)
	{
		$forum_page['item_count'] = 0;

		// Loop through users and print out some info
		foreach ($users_data_list as $user_data)
		{
			++$forum_page['item_count'];

			$forum_page['item_style'] = (($forum_page['item_count'] % 2 != 0) ? 'odd' : 'even');
			if ($forum_page['item_count'] == 1)
				$forum_page['item_style'] .= ' row1';

			($hook = get_hook('aus_show_users_pre_row_generation')) ? eval($hook) : null;

			if ($user_data) {
				$forum_page['table_row'] = array();
				$forum_page['table_row']['username'] = '<td class="tc'.count($forum_page['table_row']).'"><span><a href="'.forum_link($forum_url['user'], $user_data['id']).'">'.forum_htmlencode($user_data['username']).'</a></span><span class="usermail"><a href="mailto:'.forum_htmlencode($user_data['email']).'">'.forum_htmlencode($user_data['email']).'</a></span>'.(($user_data['admin_note'] != '') ? '<span class="usernote">'.
					__('Admin note', 'admin_users').' '.forum_htmlencode($user_data['admin_note']).'</span>' : '').'</td>';
				$forum_page['table_row']['title'] = '<td class="tc'.count($forum_page['table_row']).'">'.get_title($user_data).'</td>';
				$forum_page['table_row']['posts'] = '<td class="tc'.count($forum_page['table_row']).'">'.forum_number_format($user_data['num_posts']).'</td>';
				$forum_page['table_row']['actions'] = '<td class="tc'.count($forum_page['table_row']).'"><span><a href="'.forum_link($forum_url['admin_users']).'?ip_stats='.$user_data['id'].'">'.
					__('View IP stats', 'admin_users').'</a></span> <span><a href="'.forum_link($forum_url['search_user_posts'], $user_data['id']).'">'.__('Show posts', 'admin_users').'</a></span></td>';
				$forum_page['table_row']['select'] = '<td class="tc'.count($forum_page['table_row']).'"><input type="checkbox" name="users['.$user_data['id'].']" value="1" /></td>';
			}
			else
			{
				$forum_page['table_row'] = array();
				$forum_page['table_row']['username'] = '<td class="tc'.count($forum_page['table_row']).'">'.forum_htmlencode($user['poster']).'</td>';
				$forum_page['table_row']['title'] = '<td class="tc'.count($forum_page['table_row']).'">'.__('Guest', 'admin_users').'</td>';
				$forum_page['table_row']['posts'] = '<td class="tc'.count($forum_page['table_row']).'"> - </td>';
				$forum_page['table_row']['actions'] = '<td class="tc'.count($forum_page['table_row']).'"> - </td>';
				$forum_page['table_row']['select'] = '<td class="tc'.count($forum_page['table_row']).'"> - </td>';
			}

			($hook = get_hook('aus_show_users_pre_row_output')) ? eval($hook) : null;

?>
				<tr class="<?php echo $forum_page['item_style'] ?>">
					<?php echo implode("\n\t\t\t\t", $forum_page['table_row'])."\n" ?>
				</tr>
<?php

		}
	}
	else
	{
			($hook = get_hook('aus_show_users_pre_no_results_row_generation')) ? eval($hook) : null;

			$forum_page['table_row'] = array();
			$forum_page['table_row']['username'] = '<td class="tc'.count($forum_page['table_row']).'">'.__('Cannot find IP', 'admin_users').'</td>';
			$forum_page['table_row']['title'] = '<td class="tc'.count($forum_page['table_row']).'"> - </td>';
			$forum_page['table_row']['posts'] = '<td class="tc'.count($forum_page['table_row']).'"> - </td>';
			$forum_page['table_row']['actions'] = '<td class="tc'.count($forum_page['table_row']).'"> - </td>';
			$forum_page['table_row']['select'] = '<td class="tc'.count($forum_page['table_row']).'"> - </td>';

			($hook = get_hook('aus_show_users_pre_no_results_row_output')) ? eval($hook) : null;

?>
				<tr class="odd row1">
					<?php echo implode("\n\t\t\t\t", $forum_page['table_row'])."\n" ?>
				</tr>
<?php

	}

?>
			</tbody>
		</table>
	</div>
<?php

	// Setup control buttons
	$forum_page['mod_options'] = array();

	if ($forum_page['num_users'] > 0)
	{
		if ($forum_user['g_id'] == FORUM_ADMIN || ($forum_user['g_moderator'] == '1' && $forum_user['g_mod_ban_users'] == '1'))
			$forum_page['mod_options']['ban'] = '<span class="submit'.((empty($forum_page['mod_options'])) ? ' first-item' : '').'"><input type="submit" name="ban_users" value="'.
				__('Ban', 'admin_users').'" /></span>';

		if ($forum_user['g_id'] == FORUM_ADMIN)
		{
			$forum_page['mod_options']['delete'] = '<span class="submit'.((empty($forum_page['mod_options'])) ? ' first-item' : '').'"><input type="submit" name="delete_users" value="'.
				__('Delete', 'admin_common').'" /></span>';
			$forum_page['mod_options']['change_group'] = '<span class="submit'.((empty($forum_page['mod_options'])) ? ' first-item' : '').'"><input type="submit" name="change_group" value="'.
				__('Change group', 'admin_users').'" /></span>';
		}
	}

	($hook = get_hook('aus_show_users_pre_moderation_buttons')) ? eval($hook) : null;

	if (!empty($forum_page['mod_options']))
	{
?>
	<div class="main-options gen-content">
		<p class="options"><?php echo implode(' ', $forum_page['mod_options']) ?></p>
	</div>
<?php

	}

?>
	</form>
	<div class="main-foot">
<?php

	if (!empty($forum_page['main_foot_options']))
		echo "\n\t\t".'<p class="options">'.implode(' ', $forum_page['main_foot_options']).'</p>';

?>
		<h2 class="hn"><span><?php printf(__('Users found', 'admin_users'), $forum_page['num_users']) ?></span></h2>
	</div>
<?php

	// Init JS helper for select-all
	$forum_loader->add_js('PUNBB.common.addDOMReadyEvent(PUNBB.common.initToggleCheckboxes);', array('type' => 'inline'));

($hook = get_hook('aus_show_users_end')) ? eval($hook) : null;
