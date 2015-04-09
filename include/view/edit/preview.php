<?php
namespace punbb;

// If preview selected and there are no errors
if (isset($_POST['preview']) && empty($forum_page['errors'])) {
	if (!defined('FORUM_PARSER_LOADED')) {
		require FORUM_ROOT.'include/parser.php';
	}

	// Generate the post heading
	$forum_page['post_ident'] = array();
	$forum_page['post_ident']['num'] = '<span class="post-num">#</span>';
	$forum_page['post_ident']['byline'] = '<span class="post-byline">'.sprintf((($id == $cur_post['first_post_id']) ?
		__('Topic byline', 'post') : __('Reply byline', 'post')), '<strong>'.forum_htmlencode($cur_post['poster']).'</strong>').'</span>';
	$forum_page['post_ident']['link'] = '<span class="post-link">'.format_time(time()).'</span>';

	$forum_page['preview_message'] = parse_message($message, $hide_smilies);

	($hook = get_hook('ed_preview_pre_display')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo $id == $cur_post['first_post_id'] ?
			__('Preview edited topic', 'post') : ___('Preview edited reply', 'post') ?></span></h2>
	</div>
	<div id="post-preview" class="main-content main-frm">
		<div class="post singlepost">
			<div class="posthead">
				<h3 class="hn"><?php echo implode(' ', $forum_page['post_ident']) ?></h3>
<?php ($hook = get_hook('ed_preview_new_post_head_option')) ? eval($hook) : null; ?>
			</div>
			<div class="postbody">
				<div class="post-entry">
					<div class="entry-content">
						<?php echo $forum_page['preview_message']."\n" ?>
					</div>
<?php ($hook = get_hook('ed_preview_new_post_entry_data')) ? eval($hook) : null; ?>
				</div>
			</div>
		</div>
	</div>
<?php } ?>
