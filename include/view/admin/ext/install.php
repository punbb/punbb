<?php
namespace punbb;

($hook = get_hook('aex_install_output_start')) ? eval($hook) : null;
?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo end($forum_page['crumbs']) ?> "<?php echo forum_htmlencode($ext_data['extension']['title']) ?>"</span></h2>
	</div>
	<div class="main-content main-frm">

		<?php helper('errors', array(
			'errors_title' => __('Install ext errors', 'admin_ext')
		)) ?>

		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo $base_url.'/admin/extensions.php'.(isset($_GET['install']) ? '?install=' : '?install_hotfix=').$id ?>">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token($base_url.'/admin/extensions.php'.(isset($_GET['install']) ? '?install=' : '?install_hotfix=').$id) ?>" />
			</div>
			<div class="ct-group data-group">
				<div class="ct-set data-set set1">
					<div class="ct-box data-box">
						<h3 class="ct-legend hn"><span><?php echo forum_htmlencode($ext_data['extension']['title']) ?></span></h3>
						<p><?php echo ((strpos($id, 'hotfix_') !== 0) ?
							sprintf(__('Version', 'admin_ext'), $ext_data['extension']['version']) :
							__('Hotfix', 'admin_ext')) ?></p>
						<p><?php printf(__('Extension by', 'admin_ext'), forum_htmlencode($ext_data['extension']['author'])) ?></p>
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
		$form_warnings[] = '<li>'.__('Maxtestedon warning', 'admin_ext').'</li>';

	if (!empty($form_warnings))
	{

?>			<div class="ct-box warn-box">
				<p class="important"><strong><?php echo __('Install note', 'admin_ext') ?></strong></p>
				<ol class="info-list">
<?php

		echo implode("\n\t\t\t\t\t", $form_warnings)."\n";

?>
				</ol>
			</div>
<?php

	}

?>			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="install_comply" value="<?php echo ((strpos($id, 'hotfix_') !== 0) ?
					__('Install extension', 'admin_ext') :
					__('Install hotfix', 'admin_ext')) ?>" /></span>
				<span class="cancel"><input type="submit" name="install_cancel" value="<?php echo __('Cancel', 'admin_common') ?>" /></span>
			</div>
		</form>
	</div>
<?php

($hook = get_hook('aex_install_end')) ? eval($hook) : null;
