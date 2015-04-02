<?php

($hook = get_hook('aex_section_hotfixes_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo $lang_admin_ext['Hotfixes available'] ?></span></h2>
	</div>
	<div class="main-content main-hotfixes">
<?php

	$num_exts = 0;
	$num_failed = 0;
	$forum_page['item_num'] = 1;
	$forum_page['ext_item'] = array();
	$forum_page['ext_error'] = array();

	// Loop through any available hotfixes
	if (isset($forum_updates['hotfix']))
	{
		// If there's only one hotfix, add one layer of arrays so we can foreach over it
		if (!is_array(current($forum_updates['hotfix'])))
			$forum_updates['hotfix'] = array($forum_updates['hotfix']);

		foreach ($forum_updates['hotfix'] as $hotfix)
		{
			if (!array_key_exists($hotfix['attributes']['id'], $inst_exts))
			{
				$forum_page['ext_item'][] = '<div class="ct-box info-box hotfix available">'."\n\t\t\t".'<h3 class="ct-legend hn">'.forum_htmlencode($hotfix['content']).'</h3>'."\n\t\t\t".'<ul>'."\n\t\t\t\t".'<li><span>'.sprintf($lang_admin_ext['Extension by'], 'PunBB').'</span></li>'."\n\t\t\t\t".'<li><span>'.$lang_admin_ext['Hotfix description'].'</span></li>'."\n\t\t\t".'</ul>'."\n\t\t\t\t".'<p class="options"><span class="first-item"><a href="'.$base_url.'/admin/extensions.php?install_hotfix='.urlencode($hotfix['attributes']['id']).'">'.$lang_admin_ext['Install hotfix'].'</a></span></p>'."\n\t\t".'</div>';
				++$num_exts;
			}
		}
	}

	($hook = get_hook('aex_section_hotfixes_pre_display_available_ext_list')) ? eval($hook) : null;

	if ($num_exts)
		echo "\t\t".implode("\n\t\t", $forum_page['ext_item'])."\n";
	else
	{

?>
		<div class="ct-box info-box">
			<p><?php echo $lang_admin_ext['No available hotfixes'] ?></p>
		</div>
<?php

	}

?>
	</div>
<?php

	($hook = get_hook('aex_section_hotfixes_pre_display_installed_ext_list')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo $lang_admin_ext['Installed hotfixes'] ?></span></h2>
	</div>
	<div class="main-content main-hotfixes">
<?php

	$installed_count = 0;
	foreach ($inst_exts as $id => $ext)
	{
		if (strpos($id, 'hotfix_') !== 0)
				continue;

		$forum_page['ext_actions'] = array(
			'flip'		=> '<span class="first-item"><a href="'.$base_url.'/admin/extensions.php?section=hotfixes&amp;flip='.$id.'&amp;csrf_token='.generate_form_token('flip'.$id).'">'.($ext['disabled'] != '1' ? $lang_admin_ext['Disable'] : $lang_admin_ext['Enable']).'</a></span>',
			'uninstall'	=> '<span><a href="'.$base_url.'/admin/extensions.php?section=hotfixese&amp;uninstall='.$id.'">'.$lang_admin_ext['Uninstall'].'</a></span>'
		);

		($hook = get_hook('aex_section_hotfixes_pre_ext_actions')) ? eval($hook) : null;

?>
		<div class="ct-box info-box hotfix <?php echo $ext['disabled'] == '1' ? 'disabled' : 'enabled' ?>">
			<h3 class="ct-legend hn"><span><?php echo forum_htmlencode($ext['title']) ?><?php if ($ext['disabled'] == '1') echo ' ( <span>'.$lang_admin_ext['Extension disabled'].'</span> )' ?></span></h3>
			<ul class="data-list">
				<li><span><?php echo ((strpos($id, 'hotfix_') !== 0) ? sprintf($lang_admin_ext['Version'], $ext['version']) : $lang_admin_ext['Hotfix']) ?></span></li>
				<li><span><?php printf($lang_admin_ext['Extension by'], forum_htmlencode($ext['author'])) ?></span></li>
				<?php if ($ext['description'] != ''): ?>
					<li><span><?php echo forum_htmlencode($ext['description']) ?></span></li>
				<?php endif; ?>
			</ul>
			<p class="options"><?php echo implode(' ', $forum_page['ext_actions']) ?></p>
		</div>
<?php
		$installed_count++;
	}

	if ($installed_count == 0)
	{

?>
		<div class="ct-box info-box">
			<p><?php echo $lang_admin_ext['No installed hotfixes'] ?></p>
		</div>
<?php

	}

?>
	</div>
<?php

($hook = get_hook('aex_section_hotfixes_end')) ? eval($hook) : null;
