<?php
namespace punbb;

($hook = get_hook('ul_main_output_start')) ? eval($hook) : null;

?>
	<div class="main-head">
<?php

	if (!empty($forum_page['main_head_options']))
		echo "\t\t".'<p class="options">'.implode(' ', $forum_page['main_head_options']).'</p>'."\n";

?>
		<h2 class="hn"><span><?php echo $forum_page['items_info'] ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<form id="afocus" method="get" accept-charset="utf-8" action="<?php echo $forum_page['form_action'] ?>">
		<div class="frm-form">
<?php ($hook = get_hook('ul_search_fieldset_start')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?= __('User find legend', 'userlist') ?></strong></legend>
<?php ($hook = get_hook('ul_pre_username')) ? eval($hook) : null; ?>
<?php if ($forum_user['g_search_users'] == '1'): ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Search for username', 'userlist') ?></span>
						<small><?= __('Username help', 'userlist') ?></small></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="username" value="<?php echo forum_htmlencode($forum_page['username']) ?>" size="35" maxlength="25" /></span>
					</div>
				</div>
<?php endif; ?>
<?php ($hook = get_hook('ul_pre_group_select')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box select">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('User group', 'userlist') ?></span></label><br />
						<span class="fld-input"><select id="fld<?php echo $forum_page['fld_count'] ?>" name="show_group">
						<option value="-1"<?php if ($forum_page['show_group'] == -1) echo ' selected="selected"' ?>><?= __('All users', 'userlist') ?></option>
<?php

($hook = get_hook('ul_search_new_group_option')) ? eval($hook) : null;

while ($cur_group = db()->fetch_assoc($result_group))
{
	if ($cur_group['g_id'] == $forum_page['show_group'])
		echo "\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'" selected="selected">'.forum_htmlencode($cur_group['g_title']).'</option>'."\n";
	else
		echo "\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'">'.forum_htmlencode($cur_group['g_title']).'</option>'."\n";
}

?>
						</select></span>
					</div>
				</div>
<?php ($hook = get_hook('ul_pre_sort_by')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box select">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Sort users by', 'userlist') ?></span></label><br />
						<span class="fld-input"><select id="fld<?php echo $forum_page['fld_count'] ?>" name="sort_by">
						<option value="username"<?php if ($forum_page['sort_by'] == 'username') echo ' selected="selected"' ?>><?= __('Username', 'userlist') ?></option>
						<option value="registered"<?php if ($forum_page['sort_by'] == 'registered') echo ' selected="selected"' ?>><?= __('Registered', 'userlist') ?></option>
<?php if ($forum_page['show_post_count']): ?>
						<option value="num_posts"<?php if ($forum_page['sort_by'] == 'num_posts') echo ' selected="selected"' ?>><?= __('No of posts', 'userlist') ?></option>
<?php endif; ($hook = get_hook('ul_new_sort_by_option')) ? eval($hook) : null; ?>
						</select></span>
					</div>
				</div>
<?php ($hook = get_hook('ul_pre_sort_order_fieldset')) ? eval($hook) : null; ?>
				<fieldset class="mf-set set<?php echo ++$forum_page['item_count'] ?>">
					<legend><span><?= __('User sort order', 'userlist') ?></span></legend>
<?php ($hook = get_hook('ul_pre_sort_order')) ? eval($hook) : null; ?>
					<div class="mf-box mf-yesno">
						<div class="mf-item">
							<span class="fld-input"><input type="radio" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="sort_dir" value="ASC"<?php if ($forum_page['sort_dir'] == 'ASC') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?= __('Ascending', 'userlist') ?></label>
						</div>
						<div class="mf-item">
							<span class="fld-input"><input type="radio" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="sort_dir" value="DESC"<?php if ($forum_page['sort_dir'] == 'DESC') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?= __('Descending', 'userlist') ?></label>
						</div>
					</div>
<?php ($hook = get_hook('ul_pre_sort_order_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php ($hook = get_hook('ul_pre_search_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('ul_search_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="search" value="<?= __('Submit user search', 'userlist') ?>" /></span>
			</div>
		</div>
		</form>
<?php

$founded_user_datas = array();
while ($user_data = db()->fetch_assoc($result))
{
	$founded_user_datas[] = $user_data;
}

$forum_page['item_count'] = 0;

if (!empty($founded_user_datas))
{
	($hook = get_hook('ul_results_pre_header')) ? eval($hook) : null;

	$forum_page['table_header'] = array();
	$forum_page['table_header']['username'] = '<th class="tc'.count($forum_page['table_header']).'" scope="col">'.
		__('Username', 'userlist') . '</th>';
	$forum_page['table_header']['title'] = '<th class="tc'.count($forum_page['table_header']).'" scope="col">'.
		__('Title', 'userlist') . '</th>';

	if ($forum_page['show_post_count'])
		$forum_page['table_header']['posts'] = '<th class="tc'.count($forum_page['table_header']).'" scope="col">'.
		__('Posts', 'userlist') . '</th>';

	$forum_page['table_header']['registered'] = '<th class="tc'.count($forum_page['table_header']).'" scope="col">'.
		__('Registered', 'userlist') . '</th>';

	($hook = get_hook('ul_results_pre_header_output')) ? eval($hook) : null;

?>
		<div class="ct-group">
			<table>
				<caption><?= __('Table summary', 'userlist') ?></caption>
				<thead>
					<tr>
						<?php echo implode("\n\t\t\t\t\t\t", $forum_page['table_header'])."\n" ?>
					</tr>
				</thead>
				<tbody>
<?php

	foreach ($founded_user_datas as $user_data)
	{
		($hook = get_hook('ul_results_row_pre_data')) ? eval($hook) : null;

		$forum_page['table_row'] = array();
		$forum_page['table_row']['username'] = '<td class="tc'.count($forum_page['table_row']).'"><a href="'.forum_link($forum_url['user'], $user_data['id']).'">'.forum_htmlencode($user_data['username']).'</a></td>';
		$forum_page['table_row']['title'] = '<td class="tc'.count($forum_page['table_row']).'">'.get_title($user_data).'</td>';

		if ($forum_page['show_post_count'])
			$forum_page['table_row']['posts'] = '<td class="tc'.count($forum_page['table_row']).'">'.forum_number_format($user_data['num_posts']).'</td>';

		$forum_page['table_row']['registered'] = '<td class="tc'.count($forum_page['table_row']).'">'.format_time($user_data['registered'], 1).'</td>';

		++$forum_page['item_count'];

		($hook = get_hook('ul_results_row_pre_data_output')) ? eval($hook) : null;

?>
				<tr class="<?php echo ($forum_page['item_count'] % 2 != 0) ? 'odd' : 'even' ?><?php if ($forum_page['item_count'] == 1) echo ' row1'; ?>">
					<?php echo implode("\n\t\t\t\t\t\t", $forum_page['table_row'])."\n" ?>
				</tr>
<?php

	}

?>
				</tbody>
			</table>
		</div>
<?php

}
else
{

?>
		<div class="ct-box">
			<p><strong><?= __('No users found', 'userlist') ?></strong></p>
		</div>
<?php

}

?>
	</div>
	<div class="main-foot">
<?php

	if (!empty($forum_page['main_foot_options']))
		echo "\n\t\t\t".'<p class="options">'.implode(' ', $forum_page['main_foot_options']).'</p>';

?>
		<h2 class="hn"><span><?php echo $forum_page['items_info'] ?></span></h2>
	</div>
<?php

($hook = get_hook('ul_end')) ? eval($hook) : null;
