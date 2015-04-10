<?php
namespace punbb;

($hook = get_hook('ain_main_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo __('Information head', 'admin_index') ?></span></h2>
	</div>
	<div class="main-content main-frm">
<?php if (!empty($alert_items)): ?>
		<div id="admin-alerts" class="ct-set warn-set">
			<div class="ct-box warn-box">
				<h3 class="ct-legend hn warn"><span><?php echo __('Alerts', 'admin_index') ?></span></h3>
				<?php echo implode(' ', $alert_items)."\n" ?>
			</div>
		</div>
<?php endif; ?>
		<div class="ct-group">
<?php ($hook = get_hook('ain_pre_version')) ? eval($hook) : null; ?>
			<div class="ct-set group-item<?php echo ++$forum_page['item_count'] ?>">
				<div class="ct-box">
					<h3 class="ct-legend hn"><span><?php echo __('PunBB version', 'admin_index') ?></span></h3>
					<ul class="data-list">
						<li><span>PunBB <?php echo config()['o_cur_version'] ?></span></li>
						<li><span><?php echo __('Copyright message', 'admin_index') ?></span></li>
<?php if (isset($punbb_updates)): ?>
						<li><span><?php echo $punbb_updates ?></span></li>
<?php endif; ?>
					</ul>
				</div>
			</div>
<?php ($hook = get_hook('ain_pre_community')) ? eval($hook) : null; ?>
			<div class="ct-set group-item<?php echo ++$forum_page['item_count'] ?>">
				<div class="ct-box">
					<h3 class="ct-legend hn"><span><?php echo __('PunBB community', 'admin_index') ?></span></h3>
					<ul class="data-list">
						<li><span><?php echo __('Forums', 'admin_index') ?>: <a href="http://punbb.informer.com/forums/">Forums</a></span></li>
						<li><span><?php echo __('Twitter', 'admin_index') ?>: <a href="https://twitter.com/punbb_forum">@punbb_forum</a></span></li>
						<li><span><?php echo __('Development', 'admin_index') ?>: <a href="https://github.com/punbb/punbb">https://github.com/punbb</a></span></li>
					</ul>
				</div>
			</div>
<?php ($hook = get_hook('ain_pre_server_load')) ? eval($hook) : null; ?>
			<div class="ct-set group-item<?php echo ++$forum_page['item_count'] ?>">
				<div class="ct-box">
					<h3 class="ct-legend hn"><span><?php echo __('Server load', 'admin_index') ?></span></h3>
					<p><span><?php echo $server_load ?> (<?php echo $num_online.' '.__('users online', 'admin_index')?>)</span></p>
				</div>
			</div>
<?php ($hook = get_hook('ain_pre_environment')) ? eval($hook) : null; if ($forum_user['g_id'] == FORUM_ADMIN): ?>
			<div class="ct-set group-item<?php echo ++$forum_page['item_count'] ?>">
				<div class="ct-box">
					<h3 class="ct-legend hn"><span><?php echo __('Environment', 'admin_index') ?></span></h3>
					<ul class="data-list">
						<li><span><?php echo __('Operating system', 'admin_index') ?>: <?php echo PHP_OS ?></span></li>
						<li><span>PHP: <?php echo PHP_VERSION ?> - <a href="<?php echo forum_link($forum_url['admin_index']) ?>?action=phpinfo"><?php echo __('Show info', 'admin_index') ?></a></span></li>
						<li><span><?php echo __('Accelerator', 'admin_index') ?>: <?php echo $php_accelerator ?></span></li>
					</ul>
				</div>
			</div>
<?php ($hook = get_hook('ain_pre_database')) ? eval($hook) : null; ?>
			<div class="ct-set group-item<?php echo ++$forum_page['item_count'] ?>">
				<div class="ct-box">
					<h3 class="ct-legend hn"><span><?php echo __('Database', 'admin_index') ?></span></h3>
					<ul class="data-list">
						<li><span><?php echo implode(' ', db()->get_version()) ?></span></li>
<?php if (isset($total_records) && isset($total_size)): ?>
						<li><span><?php echo __('Rows', 'admin_index') ?>: <?php echo forum_number_format($total_records) ?></span></li>
						<li><span><?php echo __('Size', 'admin_index') ?>: <?php echo $total_size ?></span></li>
<?php endif; ?>
					</ul>
				</div>
			</div>
<?php endif; ($hook = get_hook('ain_items_end')) ? eval($hook) : null; ?>
		</div>
	</div>
<?php

($hook = get_hook('ain_end')) ? eval($hook) : null;
