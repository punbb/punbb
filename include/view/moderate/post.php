<?php
namespace punbb;
?>

<div class="<?php echo implode(' ', $forum_page['item_status']) ?>">
	<div id="p<?php echo $cur_post['id'] ?>" class="posthead">
		<h3 class="hn post-ident"><?php echo implode(' ', $forum_page['post_ident']) ?></h3>
<?php ($hook = get_hook('mr_post_actions_pre_item_select')) ? eval($hook) : null; ?>
<?php if (isset($forum_page['item_select'])) echo "\t\t\t\t".$forum_page['item_select']."\n" ?>
<?php ($hook = get_hook('mr_post_actions_new_post_head_option')) ? eval($hook) : null; ?>
	</div>
	<div class="postbody">
		<div class="post-author">
			<ul class="author-ident">
				<?php echo implode("\n\t\t\t\t\t\t", $forum_page['author_ident'])."\n" ?>
			</ul>
<?php ($hook = get_hook('mr_post_actions_new_user_ident_data')) ? eval($hook) : null; ?>
		</div>
		<div class="post-entry">
			<h4 class="entry-title"><?php echo $forum_page['item_subject'] ?></h4>
			<div class="entry-content">
				<?php echo implode("\n\t\t\t\t\t\t\t", $forum_page['message'])."\n" ?>
			</div>
<?php ($hook = get_hook('mr_post_actions_new_post_entry_data')) ? eval($hook) : null; ?>
		</div>
	</div>
</div>