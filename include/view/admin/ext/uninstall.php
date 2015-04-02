<?php

($hook = get_hook('aex_uninstall_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo end($forum_page['crumbs']) ?> "<?php echo forum_htmlencode($ext_data['title']) ?>"</span></h2>
	</div>
	<div class="main-content main-frm">
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo $base_url ?>/admin/extensions.php?section=manage&amp;uninstall=<?php echo $id ?>">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token($base_url.'/admin/extensions.php?section=manage&amp;uninstall='.$id) ?>" />
			</div>
			<div class="ct-group data-group">
				<div class="ct-set data-set set1">
					<div class="ct-box data-box">
						<h3 class="ct-legend hn"><span><?php echo forum_htmlencode($ext_data['title']) ?></span></h3>
						<p><?php echo ((strpos($id, 'hotfix_') !== 0) ? sprintf($lang_admin_ext['Version'], $ext_data['version']) : $lang_admin_ext['Hotfix']) ?></p>
						<p><?php printf($lang_admin_ext['Extension by'], forum_htmlencode($ext_data['author'])) ?></p>
						<p><?php echo forum_htmlencode($ext_data['description']) ?></p>
					</div>
				</div>
			</div>
<?php if ($ext_data['uninstall_note'] != ''): ?>			<div class="ct-box warn-box">
				<p class="important"><strong><?php echo $lang_admin_ext['Uninstall note'] ?></strong></p>
				<p><?php echo forum_htmlencode($ext_data['uninstall_note']) ?></p>
			</div>
<?php endif; ?>
<?php if (strpos($id, 'hotfix_') !== 0): ?>			<div class="ct-box warn-box">
				<p class="warn"><?php echo $lang_admin_ext['Installed extensions warn'] ?></p>
			</div>
<?php endif; ?>				<div class="frm-buttons">
				<span class="submit primary caution"><input type="submit" name="uninstall_comply" value="<?php echo $lang_admin_ext['Uninstall'] ?>" /></span>
				<span class="cancel"><input type="submit" name="uninstall_cancel" value="<?php echo $lang_admin_common['Cancel'] ?>" /></span>
			</div>
		</form>
	</div>
<?php

($hook = get_hook('aex_uninstall_end')) ? eval($hook) : null;
