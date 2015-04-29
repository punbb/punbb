<?php
namespace punbb;

global $forum_url;

if (user()->g_id == FORUM_ADMIN && config()->o_maintenance == '1') { ?>
	<p id="maint-alert" class="warn">
		<?= sprintf(__('Maintenance warning'),
			'<a href="'.link('admin_settings_maintenance').'">'.
				__('Maintenance mode') . '</a>') ?>
	</p>
<?php } ?>
