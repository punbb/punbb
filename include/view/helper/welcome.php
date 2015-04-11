<?php
namespace punbb;

if (user()->is_guest) { ?>
	<p id="welcome">
		<span><?= __('Not logged in') ?></span>
		<span><?= __('Login nag') ?></span>
	</p>
<?php } else { ?>
	<p id="welcome">
		<span><?= sprintf(__('Logged in as'),
			'<strong>' . forum_htmlencode(user()->username).'</strong>') ?></span>
	</p>
<?php } ?>
