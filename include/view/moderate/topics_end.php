	</div>

	<?php

	($hook = get_hook('mr_topic_actions_post_topic_list')) ? eval($hook) : null;

	// Setup moderator control buttons
	$forum_page['mod_options'] = array(
		'mod_move'		=> '<span class="submit first-item"><input type="submit" name="move_topics" value="'.
			__('Move', 'misc') . '" /></span>',
		'mod_delete'	=> '<span class="submit"><input type="submit" name="delete_topics" value="'.
			__('Delete') . '" /></span>',
		'mod_merge'		=> '<span class="submit"><input type="submit" name="merge_topics" value="'.
			__('Merge', 'misc') . '" /></span>',
		'mod_open'		=> '<span class="submit"><input type="submit" name="open" value="'.
			__('Open', 'misc') . '" /></span>',
		'mod_close'		=> '<span class="submit"><input type="submit" name="close" value="'.
			__('Close', 'misc') . '" /></span>'
	);

	($hook = get_hook('mr_topic_actions_pre_mod_option_output')) ? eval($hook) : null;

	?>

	<div class="main-options mod-options gen-content">
		<p class="options"><?php echo implode(' ', $forum_page['mod_options']) ?></p>
	</div>

</form>

<div class="main-foot">
	<?php
	if (!empty($forum_page['main_foot_options'])) {
		echo "\n\t\t".'<p class="options">'.implode(' ', $forum_page['main_foot_options']).'</p>';
	}
	?>
	<h2 class="hn"><span><?php echo $forum_page['items_info'] ?></span></h2>
</div>
