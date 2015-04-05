<?php

($hook = get_hook('aex_install_output_start')) ? eval($hook) : null;
?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo end($forum_page['crumbs']) ?> "<?php echo forum_htmlencode($ext_data['extension']['title']) ?>"</span></h2>
	</div>
	<div class="main-content main-frm">

		<?= helper('errors', array(
			'errors_title' => $lang_admin_ext['Install ext errors']
		)) ?>

		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo $base_url.'/admin/extensions.php'.(isset($_GET['install']) ? '?install=' : '?install_hotfix=').$id ?>">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token($base_url.'/admin/extensions.php'.(isset($_GET['install']) ? '?install=' : '?install_hotfix=').$id) ?>" />
			</div>
			<div class="ct-group data-group">
				<div class="ct-set data-set set1">
					<div class="ct-box data-box">
						<h3 class="ct-legend hn"><span><?php echo forum_htmlencode($ext_data['extension']['title']) ?></span></h3>
						<p><?php echo ((strpos($id, 'hotfix_') !== 0) ? sprintf($lang_admin_ext['Version'], $ext_data['extension']['version']) : $lang_admin_ext['Hotfix']) ?></p>
						<p><?php printf($lang_admin_ext['Extension by'], forum_htmlencode($ext_data['extension']['author'])) ?></p>
						<p><?php echo forum_htmlencode($ext_data['extension']['description']) ?></p>
					</div>
				</div>
			</div>
<?php

	// Setup an array of warnings to display in the form
	$form_warnings = array();
	$forum_page['num_items'] = 0;

	foreach ($ext_data['extension']['note'] as $cur_note)
	{
		if ($cur_note['attributes']['type'] == 'install')
			$form_warnings[] = '<li>'.forum_htmlencode($cur_note['content']).'</li>';
	}

	if (version_compare(clean_version($forum_config['o_cur_version']), clean_version($ext_data['extension']['maxtestedon']), '>'))
		$form_warnings[] = '<li>'.$lang_admin_ext['Maxtestedon warning'].'</li>';

	if (!empty($form_warnings))
	{

?>			<div class="ct-box warn-box">
				<p class="important"><strong><?php echo $lang_admin_ext['Install note'] ?></strong></p>
				<ol class="info-list">
<?php

		echo implode("\n\t\t\t\t\t", $form_warnings)."\n";

?>
				</ol>
			</div>
<?php

	}

?>			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="install_comply" value="<?php echo ((strpos($id, 'hotfix_') !== 0) ? $lang_admin_ext['Install extension'] : $lang_admin_ext['Install hotfix']) ?>" /></span>
				<span class="cancel"><input type="submit" name="install_cancel" value="<?php echo $lang_admin_common['Cancel'] ?>" /></span>
			</div>
		</form>
	</div>
<?php

($hook = get_hook('aex_install_end')) ? eval($hook) : null;
