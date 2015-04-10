<?php
namespace punbb;

global $forum_user, $forum_url;

if ($forum_user['g_id'] == FORUM_ADMIN && config()['o_maintenance'] == '1') { ?>
	<p id="maint-alert" class="warn">
		<?= sprintf(__('Maintenance warning'),
			'<a href="'.forum_link($forum_url['admin_settings_maintenance']).'">'.
				__('Maintenance mode') . '</a>') ?>
	</p>
<?php } ?>
