<?php
namespace punbb;
?>

<div id="brd-online" class="gen-content">
	<h3 class="hn"><span><?php printf(__('Currently online', 'index'),
		implode(__('Online stats separator', 'index'), $forum_page['online_info'])) ?></span></h3>
<?php if (!empty($users)): ?>
	<p><?= implode(__('Online list separator', 'index'), $users) ?></p>
<?php endif; ($hook = get_hook('in_new_online_data')) ? eval($hook) : null; ?>
</div>