<?php

($hook = get_hook('aex_uninstall_notices_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo end($forum_page['crumbs']) ?> "<?php echo forum_htmlencode($ext_data['title']) ?>"</span></h2>
	</div>
	<div class="main-content main-frm">
		<div class="ct-box info-box">
			<p><?php echo $lang_admin_ext['Extension uninstalled info'] ?></p>
			<ul class="info-list">
<?php

			foreach ($notices as $cur_notice)
				echo "\t\t\t\t".'<li><span>'.$cur_notice.'</span></li>'."\n";

?>
			</ul>
			<p><a href="<?php echo forum_link($forum_url['admin_extensions_manage']) ?>"><?php echo $lang_admin_common['Manage extensions'] ?></a></p>
		</div>
	</div>
<?php

($hook = get_hook('aex_uninstall_notices_end')) ? eval($hook) : null;
