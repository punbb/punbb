<?php
namespace punbb;

($hook = get_hook('aex_uninstall_notices_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?= end($forum_page['crumbs']) ?> "<?= forum_htmlencode($ext_data['title']) ?>"</span></h2>
	</div>
	<div class="main-content main-frm">
		<div class="ct-box info-box">
			<p><?= __('Extension uninstalled info', 'admin_ext') ?></p>
			<ul class="info-list">
			<?php foreach ($notices as $cur_notice) { ?>
				<li><span><?= $cur_notice ?></span></li>
			<?php } ?>
			</ul>
			<p><a href="<?= forum_link($forum_url['admin_extensions_manage']) ?>"><?= __('Manage extensions', 'admin_common') ?></a></p>
		</div>
	</div>
<?php

($hook = get_hook('aex_uninstall_notices_end')) ? eval($hook) : null;
