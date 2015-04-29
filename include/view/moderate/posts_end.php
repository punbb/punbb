<?php
namespace punbb;
?>

	</div>

<?php

$forum_page['mod_options'] = array(
	'del_posts'		=> '<span class="submit first-item"><input type="submit" name="delete_posts" value="'.
		__('Delete posts', 'misc') . '" /></span>',
	'split_posts'	=> '<span class="submit"><input type="submit" name="split_posts" value="'.
		__('Split posts', 'misc') . '" /></span>',
	'del_topic'		=> '<span><a href="'.link('delete', $cur_topic['first_post_id']).'">'.
		__('Delete whole topic', 'misc') . '</a></span>'
);

($hook = get_hook('mr_post_actions_pre_mod_options')) ? eval($hook) : null;

?>
	<div class="main-options mod-options gen-content">
		<p class="options"><?php echo implode(' ', $forum_page['mod_options']) ?></p>
	</div>
</form>

<div class="main-foot">
<?php

if (!empty($forum_page['main_foot_options']))
	echo "\n\t\t".'<p class="options">'.implode(' ', $forum_page['main_foot_options']).'</p>';

?>
	<h2 class="hn"><span><?php echo $forum_page['items_info'] ?></span></h2>
</div>