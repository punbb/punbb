<?php

($hook = get_hook('mr_post_actions_output_start')) ? eval($hook) : null;

include view('moderate/topic_start');

	if (!defined('FORUM_PARSER_LOADED'))
		require FORUM_ROOT.'include/parser.php';

	$forum_page['item_count'] = 0;	// Keep track of post numbers

	// Retrieve the posts (and their respective poster)
	$query = array(
		'SELECT'	=> 'u.title, u.num_posts, g.g_id, g.g_user_title, p.id, p.poster, p.poster_id, p.message, p.hide_smilies, p.posted, p.edited, p.edited_by',
		'FROM'		=> 'posts AS p',
		'JOINS'		=> array(
			array(
				'INNER JOIN'	=> 'users AS u',
				'ON'			=> 'u.id=p.poster_id'
			),
			array(
				'INNER JOIN'	=> 'groups AS g',
				'ON'			=> 'g.g_id=u.group_id'
			)
		),
		'WHERE'		=> 'p.topic_id='.$tid,
		'ORDER BY'	=> 'p.id',
		'LIMIT'		=> $forum_page['start_from'].','.$forum_user['disp_posts']
	);

	($hook = get_hook('mr_post_actions_qr_get_posts')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
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
			$forum_page['post_ident']['byline'] = '<span class="post-byline">'.sprintf((($cur_post['id'] == $cur_topic['first_post_id']) ? $lang_topic['Topic byline'] : $lang_topic['Reply byline']), (($forum_user['g_view_users'] == '1') ? '<a title="'.sprintf($lang_topic['Go to profile'], forum_htmlencode($cur_post['username'])).'" href="'.forum_link($forum_url['user'], $cur_post['poster_id']).'">'.forum_htmlencode($cur_post['username']).'</a>' : '<strong>'.forum_htmlencode($cur_post['username']).'</strong>')).'</span>';
		else
			$forum_page['post_ident']['byline'] = '<span class="post-byline">'.sprintf((($cur_post['id'] == $cur_topic['first_post_id']) ? $lang_topic['Topic byline'] : $lang_topic['Reply byline']), '<strong>'.forum_htmlencode($cur_post['username']).'</strong>').'</span>';

		$forum_page['post_ident']['link'] = '<span class="post-link"><a class="permalink" rel="bookmark" title="'.$lang_topic['Permalink post'].'" href="'.forum_link($forum_url['post'], $cur_post['id']).'">'.format_time($cur_post['posted']).'</a></span>';

		if ($cur_post['edited'] != '')
			$forum_page['post_ident']['edited'] = '<span class="post-edit">'.sprintf($lang_topic['Last edited'], forum_htmlencode($cur_post['edited_by']), format_time($cur_post['edited'])).'</span>';

		($hook = get_hook('mr_row_pre_item_ident_merge')) ? eval($hook) : null;

		// Generate the checkbox field
		if ($cur_post['id'] != $cur_topic['first_post_id'])
			$forum_page['item_select'] = '<p class="item-select"><input type="checkbox" id="fld'.$cur_post['id'].'" name="posts[]" value="'.$cur_post['id'].'" /> <label for="fld'.$cur_post['id'].'">'.$lang_misc['Select post'].' '.forum_number_format($forum_page['start_from'] + $forum_page['item_count']).'</label></p>';

		// Generate author identification
		$forum_page['author_ident']['username'] = '<li class="username">'.(($cur_post['poster_id'] > '1') ? '<a title="'.sprintf($lang_topic['Go to profile'], forum_htmlencode($cur_post['username'])).'" href="'.forum_link($forum_url['user'], $cur_post['poster_id']).'">'.forum_htmlencode($cur_post['username']).'</a>' : '<strong>'.forum_htmlencode($cur_post['username']).'</strong>').'</li>';
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
			$forum_page['item_subject'] = sprintf($lang_topic['Topic title'], $cur_topic['subject']);
		else
			$forum_page['item_subject'] = sprintf($lang_topic['Reply title'], $cur_topic['subject']);

		$forum_page['item_subject'] = forum_htmlencode($forum_page['item_subject']);

		// Perform the main parsing of the message (BBCode, smilies, censor words etc)
		$forum_page['message']['message'] = parse_message($cur_post['message'], $cur_post['hide_smilies']);

		($hook = get_hook('mr_post_actions_row_pre_display')) ? eval($hook) : null;

		include view('moderate/post');
	}

	include view('moderate/topic_end');

	$forum_id = $fid;

	// Init JS helper for select-all
	$forum_loader->add_js('PUNBB.common.addDOMReadyEvent(PUNBB.common.initToggleCheckboxes);', array('type' => 'inline'));

($hook = get_hook('mr_post_actions_end')) ? eval($hook) : null;
