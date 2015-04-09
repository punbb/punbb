<?php
namespace punbb;

global $forum_user;

if ($forum_user['is_guest']) { ?>
	<p id="welcome">
		<span><?= __('Not logged in') ?></span>
		<span><?= __('Login nag') ?></span>
	</p>
<?php } else { ?>
	<p id="welcome">
		<span><?= sprintf(__('Logged in as'),
			'<strong>' . forum_htmlencode($forum_user['username']).'</strong>') ?></span>
	</p>
<?php } ?>
