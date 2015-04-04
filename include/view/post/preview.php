<?php

// If preview selected and there are no errors
if (isset($_POST['preview']) && empty($errors)) {
	if (!defined('FORUM_PARSER_LOADED')) {
		require FORUM_ROOT.'include/parser.php';
	}

	$forum_page['preview_message'] = parse_message(forum_trim($message), $hide_smilies);

	// Generate the post heading
	$forum_page['post_ident'] = array();
	$forum_page['post_ident']['num'] = '<span class="post-num">#</span>';
	$forum_page['post_ident']['byline'] = '<span class="post-byline">'.sprintf((($tid) ? $lang_post['Reply byline'] : $lang_post['Topic byline']), '<strong>'.forum_htmlencode($forum_user['username']).'</strong>').'</span>';
	$forum_page['post_ident']['link'] = '<span class="post-link">'.format_time(time()).'</span>';

	($hook = get_hook('po_preview_pre_display')) ? eval($hook) : null;
?>
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
<?php } ?>