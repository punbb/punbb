<?php

($hook = get_hook('ain_main_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo $lang_admin_index['Information head'] ?></span></h2>
	</div>
	<div class="main-content main-frm">
<?php if (!empty($alert_items)): ?>
		<div id="admin-alerts" class="ct-set warn-set">
			<div class="ct-box warn-box">
				<h3 class="ct-legend hn warn"><span><?php echo $lang_admin_index['Alerts'] ?></span></h3>
				<?php echo implode(' ', $alert_items)."\n" ?>
			</div>
		</div>
<?php endif; ?>
		<div class="ct-group">
<?php ($hook = get_hook('ain_pre_version')) ? eval($hook) : null; ?>
			<div class="ct-set group-item<?php echo ++$forum_page['item_count'] ?>">
				<div class="ct-box">
					<h3 class="ct-legend hn"><span><?php echo $lang_admin_index['PunBB version'] ?></span></h3>
					<ul class="data-list">
						<li><span>PunBB <?php echo $forum_config['o_cur_version'] ?></span></li>
						<li><span><?php echo $lang_admin_index['Copyright message'] ?></span></li>
<?php if (isset($punbb_updates)): ?>
						<li><span><?php echo $punbb_updates ?></span></li>
<?php endif; ?>
					</ul>
				</div>
			</div>
<?php ($hook = get_hook('ain_pre_community')) ? eval($hook) : null; ?>
			<div class="ct-set group-item<?php echo ++$forum_page['item_count'] ?>">
				<div class="ct-box">
					<h3 class="ct-legend hn"><span><?php echo $lang_admin_index['PunBB community'] ?></span></h3>
					<ul class="data-list">
						<li><span><?php echo $lang_admin_index['Forums'] ?>: <a href="http://punbb.informer.com/forums/">Forums</a></span></li>
						<li><span><?php echo $lang_admin_index['Twitter'] ?>: <a href="https://twitter.com/punbb_forum">@punbb_forum</a></span></li>
						<li><span><?php echo $lang_admin_index['Development'] ?>: <a href="https://github.com/punbb/punbb">https://github.com/punbb</a></span></li>
					</ul>
				</div>
			</div>
<?php ($hook = get_hook('ain_pre_server_load')) ? eval($hook) : null; ?>
			<div class="ct-set group-item<?php echo ++$forum_page['item_count'] ?>">
				<div class="ct-box">
					<h3 class="ct-legend hn"><span><?php echo $lang_admin_index['Server load'] ?></span></h3>
					<p><span><?php echo $server_load ?> (<?php echo $num_online.' '.$lang_admin_index['users online']?>)</span></p>
				</div>
			</div>
<?php ($hook = get_hook('ain_pre_environment')) ? eval($hook) : null; if ($forum_user['g_id'] == FORUM_ADMIN): ?>
			<div class="ct-set group-item<?php echo ++$forum_page['item_count'] ?>">
				<div class="ct-box">
					<h3 class="ct-legend hn"><span><?php echo $lang_admin_index['Environment'] ?></span></h3>
					<ul class="data-list">
						<li><span><?php echo $lang_admin_index['Operating system'] ?>: <?php echo PHP_OS ?></span></li>
						<li><span>PHP: <?php echo PHP_VERSION ?> - <a href="<?php echo forum_link($forum_url['admin_index']) ?>?action=phpinfo"><?php echo $lang_admin_index['Show info'] ?></a></span></li>
						<li><span><?php echo $lang_admin_index['Accelerator'] ?>: <?php echo $php_accelerator ?></span></li>
					</ul>
				</div>
			</div>
<?php ($hook = get_hook('ain_pre_database')) ? eval($hook) : null; ?>
			<div class="ct-set group-item<?php echo ++$forum_page['item_count'] ?>">
				<div class="ct-box">
					<h3 class="ct-legend hn"><span><?php echo $lang_admin_index['Database'] ?></span></h3>
					<ul class="data-list">
						<li><span><?php echo implode(' ', $forum_db->get_version()) ?></span></li>
<?php if (isset($total_records) && isset($total_size)): ?>
						<li><span><?php echo $lang_admin_index['Rows'] ?>: <?php echo forum_number_format($total_records) ?></span></li>
						<li><span><?php echo $lang_admin_index['Size'] ?>: <?php echo $total_size ?></span></li>
<?php endif; ?>
					</ul>
				</div>
			</div>
<?php endif; ($hook = get_hook('ain_items_end')) ? eval($hook) : null; ?>
		</div>
	</div>
<?php

($hook = get_hook('ain_end')) ? eval($hook) : null;
