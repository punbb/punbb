
<div class="post<?php if ($forum_page['item_count'] == 1) echo ' firstpost'; ?><?php if ($forum_page['item_total'] == $forum_page['item_count']) echo ' lastpost'; ?>">
	<div class="posthead">
		<h3 class="hn post-ident"><?php echo implode(' ', $forum_page['post_ident']) ?></h3>
<?php ($hook = get_hook('po_topic_review_new_post_head_option')) ? eval($hook) : null; ?>
	</div>
	<div class="postbody">
		<div class="post-entry">
			<div class="entry-content">
				<?php echo $forum_page['message']."\n" ?>
<?php ($hook = get_hook('po_topic_review_new_post_entry_data')) ? eval($hook) : null; ?>
			</div>
		</div>
	</div>
</div>