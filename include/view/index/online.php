<div id="brd-online" class="gen-content">
	<h3 class="hn"><span><?php printf($lang_index['Currently online'], implode($lang_index['Online stats separator'], $forum_page['online_info'])) ?></span></h3>
<?php if (!empty($users)): ?>
	<p><?php echo implode($lang_index['Online list separator'], $users) ?></p>
<?php endif; ($hook = get_hook('in_new_online_data')) ? eval($hook) : null; ?>
</div>