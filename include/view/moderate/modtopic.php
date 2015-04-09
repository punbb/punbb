<?php
namespace punbb;

($hook = get_hook('mr_post_actions_output_start')) ? eval($hook) : null;

include view('moderate/posts_start');

	$forum_page['item_count'] = 0;	// Keep track of post numbers

	while ($cur_post = $forum_db->fetch_assoc($result))
	{
		($hook = get_hook('mr_post_actions_loop_start')) ? eval($hook) : null;

		++$forum_page['item_count'];

		$forum_page['post_ident'] = array();
		$forum_page['message'] = array();
		$forum_page['user_ident'] = array();
		$cur_post['username'] = $cur_post['poster'];

		// Generate the post heading
		$forum_page['post_ident']['num'] = '<span class="post-num">'.forum_number_format($forum_page['start_from'] + $forum_page['item_count']).'</span>';

		if ($cur_post['poster_id'] > 1)
			$forum_page['post_ident']['byline'] = '<span class="post-byline">'.sprintf((($cur_post['id'] == $cur_topic['first_post_id']) ?
				__('Topic byline', 'topic') : __('Reply byline', 'topic')), (($forum_user['g_view_users'] == '1') ? '<a title="'.
					sprintf(__('Go to profile', 'topic'), forum_htmlencode($cur_post['username'])).'" href="'.forum_link($forum_url['user'], $cur_post['poster_id']).'">'.forum_htmlencode($cur_post['username']).'</a>' : '<strong>'.forum_htmlencode($cur_post['username']).'</strong>')).'</span>';
		else
			$forum_page['post_ident']['byline'] = '<span class="post-byline">'.sprintf((($cur_post['id'] == $cur_topic['first_post_id']) ?
				__('Topic byline', 'topic') : __('Reply byline', 'topic')), '<strong>'.forum_htmlencode($cur_post['username']).'</strong>').'</span>';

		$forum_page['post_ident']['link'] = '<span class="post-link"><a class="permalink" rel="bookmark" title="'.
			__('Permalink post', 'topic') . '" href="'.forum_link($forum_url['post'], $cur_post['id']).'">'.format_time($cur_post['posted']).'</a></span>';

		if ($cur_post['edited'] != '')
			$forum_page['post_ident']['edited'] = '<span class="post-edit">'.
			sprintf(__('Last edited', 'topic'), forum_htmlencode($cur_post['edited_by']), format_time($cur_post['edited'])).'</span>';

		($hook = get_hook('mr_row_pre_item_ident_merge')) ? eval($hook) : null;

		// Generate the checkbox field
		if ($cur_post['id'] != $cur_topic['first_post_id'])
			$forum_page['item_select'] = '<p class="item-select"><input type="checkbox" id="fld'.$cur_post['id'].'" name="posts[]" value="'.$cur_post['id'].'" /> <label for="fld'.$cur_post['id'].'">'.
			__('Select post', 'misc') . ' ' . forum_number_format($forum_page['start_from'] + $forum_page['item_count']).'</label></p>';

		// Generate author identification
		$forum_page['author_ident']['username'] = '<li class="username">'.(($cur_post['poster_id'] > '1') ? '<a title="'.
			sprintf(__('Go to profile', 'topic'), forum_htmlencode($cur_post['username'])).'" href="'.forum_link($forum_url['user'], $cur_post['poster_id']).'">'.forum_htmlencode($cur_post['username']).'</a>' : '<strong>'.forum_htmlencode($cur_post['username']).'</strong>').'</li>';
		$forum_page['author_ident']['usertitle'] = '<li class="usertitle"><span>'.get_title($cur_post).'</span></li>';

		// Give the post some class
		$forum_page['item_status'] = array(
			'post',
			($forum_page['item_count'] % 2 != 0) ? 'odd' : 'even'
		);

		if ($forum_page['item_count'] == 1)
			$forum_page['item_status']['firstpost'] = 'firstpost';

		if (($forum_page['start_from'] + $forum_page['item_count']) == $forum_page['finish_at'])
			$forum_page['item_status']['lastpost'] = 'lastpost';

		if ($cur_post['id'] == $cur_topic['first_post_id'])
			$forum_page['item_status']['topicpost'] = 'topicpost';
		else
			$forum_page['item_status']['replypost'] = 'replypost';

		// Generate the post title
		if ($cur_post['id'] == $cur_topic['first_post_id'])
			$forum_page['item_subject'] = sprintf(__('Topic title', 'topic'), $cur_topic['subject']);
		else
			$forum_page['item_subject'] = sprintf(__('Reply title', 'topic'), $cur_topic['subject']);

		$forum_page['item_subject'] = forum_htmlencode($forum_page['item_subject']);

		// Perform the main parsing of the message (BBCode, smilies, censor words etc)
		$forum_page['message']['message'] = parse_message($cur_post['message'], $cur_post['hide_smilies']);

		($hook = get_hook('mr_post_actions_row_pre_display')) ? eval($hook) : null;

		include view('moderate/post');
	}

	include view('moderate/posts_end');

	$forum_id = $fid;

	// Init JS helper for select-all
	$forum_loader->add_js('PUNBB.common.addDOMReadyEvent(PUNBB.common.initToggleCheckboxes);', array('type' => 'inline'));

($hook = get_hook('mr_post_actions_end')) ? eval($hook) : null;
