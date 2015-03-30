<div class="main-subhead">
	<h2 class="hn"><span><?php echo $tid ? $lang_post['Preview reply'] : $lang_post['Preview new topic'] ?></span></h2>
</div>
<div id="post-preview" class="main-content main-frm">
	<div class="post singlepost">
		<div class="posthead">
			<h3 class="hn"><?php echo implode(' ', $forum_page['post_ident']) ?></h3>
<?php ($hook = get_hook('po_preview_new_post_head_option')) ? eval($hook) : null; ?>
		</div>
		<div class="postbody">
			<div class="post-entry">
				<div class="entry-content">
					<?php echo $forum_page['preview_message']."\n" ?>
				</div>
<?php ($hook = get_hook('po_preview_new_post_entry_data')) ? eval($hook) : null; ?>
			</div>
		</div>
	</div>
</div>