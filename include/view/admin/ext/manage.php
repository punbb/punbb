<?php
namespace punbb;

($hook = get_hook('aex_section_install_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?= __('Extensions available', 'admin_ext') ?></span></h2>
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
				$forum_page['ext_error'][] = '<div class="ext-error databox db'.++$forum_page['item_num'].'">'."\n\t\t\t\t".'<h3 class="legend"><span>'.
					sprintf(__('Extension loading error', 'admin_ext'), forum_htmlencode($entry)).'</span></h3>'."\n\t\t\t\t".'<p>'.
					__('Illegal ID', 'admin_ext') . '</p>'."\n\t\t\t".'</div>';
				++$num_failed;
				continue;
			}
			else if (!file_exists(FORUM_ROOT.'extensions/'.$entry.'/manifest.xml'))
			{
				$forum_page['ext_error'][] = '<div class="ext-error databox db'.++$forum_page['item_num'].'">'."\n\t\t\t\t".'<h3 class="legend"><span>'.
					sprintf(__('Extension loading error', 'admin_ext'), forum_htmlencode($entry)).'<span></h3>'."\n\t\t\t\t".'<p>'.
					__('Missing manifest', 'admin_ext') . '</p>'."\n\t\t\t".'</div>';
				++$num_failed;
				continue;
			}

			// Parse manifest.xml into an array
			$ext_data = is_readable(FORUM_ROOT.'extensions/'.$entry.'/manifest.xml') ? xml_to_array(file_get_contents(FORUM_ROOT.'extensions/'.$entry.'/manifest.xml')) : '';
			if (empty($ext_data))
			{
				$forum_page['ext_error'][] = '<div class="ext-error databox db'.++$forum_page['item_num'].'">'."\n\t\t\t\t".'<h3 class="legend"><span>'.
					sprintf(__('Extension loading error', 'admin_ext'), forum_htmlencode($entry)).'<span></h3>'."\n\t\t\t\t".'<p>'.
					__('Failed parse manifest', 'admin_ext') . '</p>'."\n\t\t\t".'</div>';
				++$num_failed;
				continue;
			}

			// Validate manifest
			$errors = validate_manifest($ext_data, $entry);
			if (!empty($errors))
			{
				$forum_page['ext_error'][] = '<div class="ext-error databox db'.++$forum_page['item_num'].'">'."\n\t\t\t\t".'<h3 class="legend"><span>'.
				sprintf(__('Extension loading error', 'admin_ext'), forum_htmlencode($entry)).'</span></h3>'."\n\t\t\t\t".'<p>'.implode(' ', $errors).'</p>'."\n\t\t\t".'</div>';
				++$num_failed;
			}
			else
			{
				if (!array_key_exists($entry, $inst_exts) || version_compare($inst_exts[$entry]['version'], $ext_data['extension']['version'], '!='))
				{
					$forum_page['ext_item'][] = '<div class="ct-box info-box extension available">'."\n\t\t\t".'<h3 class="ct-legend hn">'.forum_htmlencode($ext_data['extension']['title']).' <em>'.$ext_data['extension']['version'].'</em></h3>'."\n\t\t\t".'<ul class="data-list">'."\n\t\t\t\t".'<li><span>'.
					sprintf(__('Extension by', 'admin_ext'), forum_htmlencode($ext_data['extension']['author'])).'</span></li>'.(($ext_data['extension']['description'] != '') ? "\n\t\t\t\t".'<li><span>'.forum_htmlencode($ext_data['extension']['description']).'</span></li>' : '')."\n\t\t\t".'</ul>'."\n\t\t\t".'<p class="options"><span class="first-item"><a href="'.$base_url.'/admin/extensions.php?install='.urlencode($entry).'">'.(isset($inst_exts[$entry]['version']) ?
						__('Upgrade extension', 'admin_ext') :
						__('Install extension', 'admin_ext')).'</a></span></p>'."\n\t\t".'</div>';
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
			<p><?php echo __('No available extensions', 'admin_ext') ?></p>
		</div>
<?php

	}

	// If any of the extensions had errors
	if ($num_failed)
	{

?>
		<div class="ct-box data-box">
			<p class="important"><?php echo __('Invalid extensions', 'admin_ext') ?></p>
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
		<h2 class="hn"><span><?php echo __('Installed extensions', 'admin_ext') ?></span></h2>
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
			'flip'		=> '<span class="first-item"><a href="'.$base_url.'/admin/extensions.php?section=manage&amp;flip='.$id.'&amp;csrf_token='.generate_form_token('flip'.$id).'">'.($ext['disabled'] != '1' ?
				__('Disable', 'admin_ext') : __('Enable', 'admin_ext')).'</a></span>',
			'uninstall'	=> '<span><a href="'.$base_url.'/admin/extensions.php?section=manage&amp;uninstall='.$id.'">'.
				__('Uninstall', 'admin_ext').'</a></span>'
		);

		if (config()->o_check_for_versions == 1 && isset($forum_ext_last_versions[$id]) && version_compare($ext['version'], $forum_ext_last_versions[$id]['version'], '<'))
			$forum_page['ext_actions']['latest_ver'] = '<span><a href="'.$forum_ext_last_versions[$id]['repo_url'].'/'.$id.'/'.$id.'.zip">'.
			__('Download latest version', 'admin_ext').'</a></span>';

		($hook = get_hook('aex_section_manage_pre_ext_actions')) ? eval($hook) : null;

		if ($ext['disabled'] == '1')
			$forum_page['ext_item'][] = '<div class="ct-box info-box extension disabled">'."\n\t\t".'<h3 class="ct-legend hn">'.forum_htmlencode($ext['title']).' <em>'.$ext['version'].'</em> ('.
				__('Extension disabled', 'admin_ext') . ')</h3>'."\n\t\t".'<ul class="data-list">'."\n\t\t\t".'<li><span>'.
				sprintf(__('Extension by', 'admin_ext'), forum_htmlencode($ext['author'])).'</span></li>'."\n\t\t\t".(($ext['description'] != '') ? '<li><span>'.forum_htmlencode($ext['description']).'</span></li>' : '')."\n\t\t\t".'</ul>'."\n\t\t".'<p class="options">'.implode(' ', $forum_page['ext_actions']).'</p>'."\n\t".'</div>';
		else
			$forum_page['ext_item'][] = '<div class="ct-box info-box extension enabled">'."\n\t\t".'<h3 class="ct-legend hn">'.forum_htmlencode($ext['title']).' <em>'.$ext['version'].'</em></h3>'."\n\t\t".'<ul class="data-list">'."\n\t\t\t".'<li><span>'.
			sprintf(__('Extension by', 'admin_ext'), forum_htmlencode($ext['author'])).'</span></li>'."\n\t\t\t".(($ext['description'] != '') ? '<li><span>'.forum_htmlencode($ext['description']).'</span></li>' : '')."\n\t\t".'</ul>'."\n\t\t".'<p class="options">'.implode(' ', $forum_page['ext_actions']).'</p>'."\n\t".'</div>';

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
			<p><?php echo __('No installed extensions', 'admin_ext') ?></p>
		</div>
<?php

	}

?>
	</div>
<?php

($hook = get_hook('aex_section_manage_end')) ? eval($hook) : null;
