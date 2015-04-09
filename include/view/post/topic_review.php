<?php
namespace punbb;

// Check if the topic review is to be displayed
if ($tid && $forum_config['o_topic_review'] != '0') { ?>
	<div class="main-subhead">
		<h2 class="hn"><span><?= __('Topic review', 'post') ?></span></h2>
	</div>
	<div id="topic-review" class="main-content main-frm">
		<?php
			$forum_page['item_count'] = 0;
			$forum_page['item_total'] = count($posts);

			foreach ($posts as $cur_post) {
				++$forum_page['item_count'];

				$forum_page['message'] = parse_message($cur_post['message'], $cur_post['hide_smilies']);

				// Generate the post heading
				$forum_page['post_ident'] = array();
				$forum_page['post_ident']['num'] = '<span class="post-num">'.forum_number_format($forum_page['total_post_count'] - $forum_page['item_count'] + 1).'</span>';
				$forum_page['post_ident']['byline'] = '<span class="post-byline">'.
					sprintf(__('Post byline', 'post'), '<strong>'.forum_htmlencode($cur_post['poster']).'</strong>').'</span>';
				$forum_page['post_ident']['link'] = '<span class="post-link"><a class="permalink" rel="bookmark" title="'.
					__('Permalink post', 'post') . '" href="'.forum_link($forum_url['post'], $cur_post['id']).'">'.format_time($cur_post['posted']).'</a></span>';

				($hook = get_hook('po_topic_review_row_pre_display')) ? eval($hook) : null;

				include view('post/post');
			}
		?>
	</div>
<?php } ?>