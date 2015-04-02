<?php
global $forum_user, $forum_config, $forum_url, $lang_common;

if ($forum_user['g_id'] == FORUM_ADMIN && $forum_config['o_maintenance'] == '1') { ?>
	<p id="maint-alert" class="warn">
		<?= sprintf($lang_common['Maintenance warning'],
			'<a href="'.forum_link($forum_url['admin_settings_maintenance']).'">'.
				$lang_common['Maintenance mode'].'</a>') ?>
	</p>
<?php } ?>
