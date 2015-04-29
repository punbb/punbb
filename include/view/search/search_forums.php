<?php
namespace punbb;

($hook = get_hook('se_results_output_start')) ? eval($hook) : null;

	if ($show_as == 'topics')
	{
		$forum_page['item_header'] = array();
		$forum_page['item_header']['subject']['title'] = '<strong class="subject-title">'.
			__('Topics', 'forum') . '</strong>';
		$forum_page['item_header']['info']['forum'] = '<strong class="info-forum">'.
			__('Forum', 'forum') . '</strong>';
		$forum_page['item_header']['info']['replies'] = '<strong class="info-replies">'.
			__('replies', 'forum') . '</strong>';
		$forum_page['item_header']['info']['lastpost'] = '<strong class="info-lastpost">'.
			__('last post', 'forum') . '</strong>';

		($hook = get_hook('se_results_topics_pre_item_header_output')) ? eval($hook) : null;

?>

	<div class="main-head">
<?php

	if (!empty($forum_page['main_head_options']))
		echo "\n\t\t".'<p class="options">'.implode(' ', $forum_page['main_head_options']).'</p>';

?>
		<h2 class="hn"><span><?php echo $forum_page['items_info'] ?></span></h2>
	</div>
	<div class="main-subhead">
		<p class="item-summary forum-noview"><span><?php printf(__('Search subtitle', 'forum'), implode(' ', $forum_page['item_header']['subject']), implode(', ', $forum_page['item_header']['info'])) ?></span></p>
	</div>
	<div class="main-content main-forum forum-forums">
<?php

	}
	else if ($show_as == 'posts')
	{
		// Load parser
		if (!defined('FORUM_PARSER_LOADED'))
			require FORUM_ROOT.'include/parser.php';
?>
	<div class="main-head">
<?php

	if (!empty($forum_page['main_head_options']))
		echo "\n\t\t".'<p class="options">'.implode(' ', $forum_page['main_head_options']).'</p>';

?>
		<h2 class="hn"><span><?php echo $forum_page['items_info'] ?></span></h2>
	</div>
	<div class="main-content main-topic">
<?php
	}
	else if ($show_as == 'forums')
	{
		$forum_page['cur_category'] = $forum_page['cat_count'] = $forum_page['item_count'] = 0;
	}

	$forum_page['item_count'] = 0;

	// Finally, lets loop through the results and output them
	foreach ($search_set as $cur_set)
	{
		($hook = get_hook('se_results_loop_start')) ? eval($hook) : null;

		++$forum_page['item_count'];

		if (config()->o_censoring == '1')
			$cur_set['subject'] = censor_words($cur_set['subject']);

		if ($show_as == 'posts')
		{
			// Generate the result heading
			$forum_page['post_ident'] = array();
			$forum_page['post_ident']['num'] = '<span class="post-num">'.forum_number_format($forum_page['start_from'] + $forum_page['item_count']).'</span>';
			$forum_page['post_ident']['byline'] = '<span class="post-byline">'.sprintf((($cur_set['pid'] == $cur_set['first_post_id']) ?
				__('Topic byline', 'topic') : __('Reply byline', 'topic')), '<strong>'.forum_htmlencode($cur_set['pposter']).'</strong>').'</span>';
			$forum_page['post_ident']['link'] = '<span class="post-link"><a class="permalink" rel="bookmark" title="'.
				__('Permalink post', 'topic') . '" href="'.link('post', $cur_set['pid']).'">'.format_time($cur_set['pposted']).'</a></span>';

			($hook = get_hook('se_results_posts_row_pre_item_ident_merge')) ? eval($hook) : null;

			// Generate the topic title
			$forum_page['item_subject'] = '<a class="permalink" rel="bookmark" title="'.
				__('Permalink topic', 'topic') . '" href="'.link('topic', array($cur_set['tid'], sef_friendly($cur_set['subject']))).'">'.sprintf((($cur_set['pid'] == $cur_set['first_post_id']) ?
				__('Topic title', 'topic') : __('Reply title', 'topic')), forum_htmlencode($cur_set['subject'])).'</a> <small>'.
				sprintf(__('Search replies', 'topic'), forum_number_format($cur_set['num_replies']), '<a href="'.link('forum', array($cur_set['forum_id'], sef_friendly($cur_set['forum_name']))).'">'.forum_htmlencode($cur_set['forum_name']).'</a>').'</small>';

			// Generate author identification
			$forum_page['user_ident'] = ($cur_set['poster_id'] > 1 && user()->g_view_users == '1') ? '<strong class="username"><a title="'.
				sprintf(__('Go to profile', 'search'), forum_htmlencode($cur_set['pposter'])).'" href="'.link('user', $cur_set['poster_id']).'">'.forum_htmlencode($cur_set['pposter']).'</a></strong>' : '<strong class="username">'.forum_htmlencode($cur_set['pposter']).'</strong>';

			// Generate the post actions links
			$forum_page['post_actions'] = array();
			$forum_page['post_actions']['forum'] = '<span><a href="'.link('forum', array($cur_set['forum_id'], sef_friendly($cur_set['forum_name']))).'">'.
				__('Go to forum', 'search') . '<span>: '.forum_htmlencode($cur_set['forum_name']).'</span></a></span>';

			if ($cur_set['pid'] != $cur_set['first_post_id'])
				$forum_page['post_actions']['topic'] = '<span><a class="permalink" rel="bookmark" title="'.
				__('Permalink topic', 'topic') . '" href="'.link('topic', array($cur_set['tid'], sef_friendly($cur_set['subject']))).'">'.
					__('Go to topic', 'search') . '<span>: '.forum_htmlencode($cur_set['subject']).'</span></a></span>';

			$forum_page['post_actions']['post'] = '<span><a class="permalink" rel="bookmark" title="'.
				__('Permalink post', 'topic') . '" href="'.link('post', $cur_set['pid']).'">'.
					__('Go to post', 'search') . '<span> '.forum_number_format($forum_page['start_from'] + $forum_page['item_count']).'</span></a></span>';

			$forum_page['message'] = parse_message($cur_set['message'], $cur_set['hide_smilies']);

			// Give the post some class
			$forum_page['item_status'] = array(
				'post',
				(($forum_page['item_count'] % 2 != 0) ? 'odd' : 'even' )
			);

			if ($forum_page['item_count'] == 1)
				$forum_page['item_status']['firstpost'] = 'firstpost';

			if (($forum_page['start_from'] + $forum_page['item_count']) == $forum_page['finish_at'])
				$forum_page['item_status']['lastpost'] = 'lastpost';

			if ($cur_set['pid'] == $cur_set['first_post_id'])
				$forum_page['item_status']['topicpost'] = 'topicpost';


			($hook = get_hook('se_results_posts_row_pre_display')) ? eval($hook) : null;

?>
	<div class="<?php echo implode(' ', $forum_page['item_status']) ?> resultpost">
		<div class="posthead">
			<h3 class="hn post-ident"><?php echo implode(' ', $forum_page['post_ident']) ?></h3>
			<h4 class="hn post-title"><span><?php echo $forum_page['item_subject'] ?></span></h4>
		</div>
		<div class="postbody">
			<div class="post-entry">
				<div class="entry-content">
					<?php echo $forum_page['message'] ?>
				</div>
<?php ($hook = get_hook('se_results_posts_row_new_post_entry_data')) ? eval($hook) : null; ?>
			</div>
		</div>
		<div class="postfoot">
			<div class="post-options">
				<p class="post-actions"><?php echo implode(' ', $forum_page['post_actions']) ?></p>
			</div>
		</div>
	</div>
<?php

		}
		else if ($show_as == 'topics')
		{
			// Start from scratch
			$forum_page['item_subject'] = $forum_page['item_body'] = $forum_page['item_status'] = $forum_page['item_nav'] = $forum_page['item_title'] = $forum_page['item_title_status'] = array();

			// Assemble the Topic heading

			// Should we display the dot or not? :)
			if (!user()->is_guest && config()->o_show_dot == '1' && $cur_set['has_posted'] == user()->id)
			{
				$forum_page['item_title']['posted'] = '<span class="posted-mark">'.
					__('You posted indicator', 'forum') . '</span>';
				$forum_page['item_status']['posted'] = 'posted';
			}

			if ($cur_set['sticky'] == '1')
			{
				$forum_page['item_title_status']['sticky'] = '<em class="sticky">'.
					__('Sticky', 'forum').'</em>';
				$forum_page['item_status']['sticky'] = 'sticky';
			}

			if ($cur_set['closed'] != '0')
			{
				$forum_page['item_title_status']['closed'] = '<em class="closed">'.
					__('Closed', 'forum').'</em>';
				$forum_page['item_status']['closed'] = 'closed';
			}

			($hook = get_hook('se_results_topics_row_pre_item_subject_status_merge')) ? eval($hook) : null;

			if (!empty($forum_page['item_title_status']))
				$forum_page['item_title']['status'] = '<span class="item-status">'.sprintf(__('Item status', 'forum'), implode(', ', $forum_page['item_title_status'])).'</span>';

			$forum_page['item_title']['link'] = '<a href="'.link('topic', array($cur_set['tid'], sef_friendly($cur_set['subject']))).'">'.forum_htmlencode($cur_set['subject']).'</a>';

			($hook = get_hook('se_results_topics_row_pre_item_title_merge')) ? eval($hook) : null;

			$forum_page['item_body']['subject']['title'] = '<h3 class="hn"><span class="item-num">'.forum_number_format($forum_page['start_from'] + $forum_page['item_count']).'</span> '.implode(' ', $forum_page['item_title']).'</h3>';

			$forum_page['item_pages'] = ceil(($cur_set['num_replies'] + 1) / user()->disp_posts);

			if ($forum_page['item_pages'] > 1)
				$forum_page['item_nav']['pages'] = '<span>'.
				__('Pages', 'forum') . '&#160;</span>'.paginate($forum_page['item_pages'], -1, $forum_url['topic'], __('Page separator'), array($cur_set['tid'], sef_friendly($cur_set['subject'])));

			// Does this topic contain posts we haven't read? If so, tag it accordingly.
			if (!user()->is_guest && $cur_set['last_post'] > user()->last_visit && (!isset($tracked_topics['topics'][$cur_set['tid']]) || $tracked_topics['topics'][$cur_set['tid']] < $cur_set['last_post']) && (!isset($tracked_topics['forums'][$cur_set['forum_id']]) || $tracked_topics['forums'][$cur_set['forum_id']] < $cur_set['last_post']))
			{
				$forum_page['item_nav']['new'] = '<em class="item-newposts"><a href="'.link('topic_new_posts', array($cur_set['tid'], sef_friendly($cur_set['subject']))).'" title="'.
					__('New posts info', 'forum') . '">'.
					__('New posts', 'forum') . '</a></em>';
				$forum_page['item_status']['new'] = 'new';
			}

			($hook = get_hook('se_results_topics_row_pre_item_nav_merge')) ? eval($hook) : null;

			$forum_page['item_subject']['starter'] = '<span class="item-starter">'.sprintf(__('Topic starter', 'forum'), forum_htmlencode($cur_set['poster'])).'</span>';

			if (!empty($forum_page['item_nav']))
				$forum_page['item_subject']['nav'] = '<span class="item-nav">'.sprintf(__('Topic navigation', 'forum'), implode('&#160;&#160;', $forum_page['item_nav'])).'</span>';

			($hook = get_hook('se_results_topics_row_pre_item_subject_merge')) ? eval($hook) : null;

			$forum_page['item_body']['subject']['desc'] = '<p>'.implode(' ', $forum_page['item_subject']).'</p>';

			if (empty($forum_page['item_status']))
				$forum_page['item_status']['normal'] = 'normal';

			($hook = get_hook('se_results_topics_pre_item_status_merge')) ? eval($hook) : null;

			$forum_page['item_style'] = (($forum_page['item_count'] % 2 != 0) ? ' odd' : ' even').(($forum_page['item_count'] == 1) ? ' main-first-item' : '').((!empty($forum_page['item_status'])) ? ' '.implode(' ', $forum_page['item_status']) : '');

			$forum_page['item_body']['info']['forum'] = '<li class="info-forum"><span class="label">'.
				__('Posted in', 'search') . '</span><a href="'.link('forum', array($cur_set['forum_id'], sef_friendly($cur_set['forum_name']))).'">'.$cur_set['forum_name'].'</a></li>';
			$forum_page['item_body']['info']['replies'] = '<li class="info-replies"><strong>'.forum_number_format($cur_set['num_replies']).'</strong> <span class="label">'.(($cur_set['num_replies'] == 1) ?
					__('Reply', 'forum') : __('Replies', 'forum')).'</span></li>';
			$forum_page['item_body']['info']['lastpost'] = '<li class="info-lastpost"><span class="label">'.
				__('Last post', 'forum') . '</span> <strong><a href="'.link('post', $cur_set['last_post_id']).'">'.format_time($cur_set['last_post']).'</a></strong> <cite>'.
				sprintf(__('by poster', 'forum'), forum_htmlencode($cur_set['last_poster'])).'</cite></li>';

			($hook = get_hook('se_results_topics_row_pre_display')) ? eval($hook) : null;

?>
		<div class="main-item<?php echo $forum_page['item_style'] ?>">
			<span class="icon <?php echo implode(' ', $forum_page['item_status']) ?>"><!-- --></span>
			<div class="item-subject">
				<?php echo implode("\n\t\t\t\t", $forum_page['item_body']['subject'])."\n" ?>
			</div>
			<ul class="item-info">
				<?php echo implode("\n\t\t\t\t", $forum_page['item_body']['info'])."\n" ?>
			</ul>
		</div>
<?php

		}
		else if ($show_as == 'forums')
		{
			if ($cur_set['cid'] != $forum_page['cur_category'])	// A new category since last iteration?
			{
				if ($forum_page['cur_category'] != 0)
					echo "\t".'</div>'."\n";

				++$forum_page['cat_count'];
				$forum_page['item_count'] = 1;

				$forum_page['item_header'] = array();
				$forum_page['item_header']['subject']['title'] =
					'<strong class="subject-title">' . __('Forums', 'index') . '</strong>';
				$forum_page['item_header']['info']['topics'] =
					'<strong class="info-topics">' . __('topics', 'index') . '</strong>';
				$forum_page['item_header']['info']['post'] =
					'<strong class="info-posts">' . __('posts', 'index') . '</strong>';
				$forum_page['item_header']['info']['lastpost'] =
					'<strong class="info-lastpost">' . __('last post', 'index') . '</strong>';

				($hook = get_hook('se_results_forums_row_pre_cat_head')) ? eval($hook) : null;

				$forum_page['cur_category'] = $cur_set['cid'];

?>
				<div class="main-head">
					<h2 class="hn"><span><?php echo forum_htmlencode($cur_set['cat_name']) ?></span></h2>
				</div>
				<div class="main-subhead">
					<p class="item-summary"><span><?php printf(__('Category subtitle', 'index'),
					implode(' ', $forum_page['item_header']['subject']), implode(', ', $forum_page['item_header']['info'])) ?></span></p>
				</div>
				<div id="category<?php echo $forum_page['cat_count'] ?>" class="main-content main-category">
<?php
			}

			// Reset arrays and globals for each forum
			$forum_page['item_status'] = $forum_page['item_subject'] = $forum_page['item_body'] = $forum_page['item_title'] = array();

			// Is this a redirect forum?
			if ($cur_set['redirect_url'] != '')
			{
				$forum_page['item_body']['subject']['title'] = '<h3 class="hn"><a class="external" href="'.forum_htmlencode($cur_forum['redirect_url']).'" title="'.
					sprintf(__('Link to', 'index'), forum_htmlencode($cur_forum['redirect_url'])).'"><span>'.forum_htmlencode($cur_set['forum_name']).'</span></a></h3>';
				$forum_page['item_status']['redirect'] = 'redirect';

				if ($cur_set['forum_desc'] != '')
					$forum_page['item_subject']['desc'] = $cur_set['forum_desc'];

				$forum_page['item_subject']['redirect'] =
					'<span>' . __('External forum', 'index') . '</span>';

				($hook = get_hook('se_results_forums_row_redirect_pre_item_subject_merge')) ? eval($hook) : null;

				if (!empty($forum_page['item_subject']))
					$forum_page['item_body']['subject']['desc'] = '<p>'.implode(' ', $forum_page['item_subject']).'</p>';

				// Forum topic and post count
				$forum_page['item_body']['info']['topics'] =
					'<li class="info-topics"><span class="label">' . __('No topic info', 'index') . '</span></li>';
				$forum_page['item_body']['info']['posts'] =
					'<li class="info-posts"><span class="label">' . __('No post info', 'index') . '</span></li>';
				$forum_page['item_body']['info']['lastpost'] =
					'<li class="info-lastpost"><span class="label">' . __('No lastpost info', 'index') . '</span></li>';

				($hook = get_hook('se_results_forums_row_redirect_pre_display')) ? eval($hook) : null;
			}
			else
			{
				// Setup the title and link to the forum
				$forum_page['item_title']['title'] = '<a href="'.link('forum', array($cur_set['fid'], sef_friendly($cur_set['forum_name']))).'"><span>'.forum_htmlencode($cur_set['forum_name']).'</span></a>';

				($hook = get_hook('se_results_forums_row_redirect_pre_item_title_merge')) ? eval($hook) : null;

				$forum_page['item_body']['subject']['title'] = '<h3 class="hn">'.implode(' ', $forum_page['item_title']).'</h3>';

				// Setup the forum description and mod list
				if ($cur_set['forum_desc'] != '')
					$forum_page['item_subject']['desc'] = $cur_set['forum_desc'];

				($hook = get_hook('se_results_forums_row_normal_pre_item_subject_merge')) ? eval($hook) : null;

				if (!empty($forum_page['item_subject']))
					$forum_page['item_body']['subject']['desc'] = '<p>'.implode(' ', $forum_page['item_subject']).'</p>';

				// Setup forum topics, post count and last post
				$forum_page['item_body']['info']['topics'] = '<li class="info-topics"><strong>'.forum_number_format($cur_set['num_topics']).'</strong> <span class="label">'.(($cur_set['num_topics'] == 1) ?
					__('topic', 'index') : __('topics', 'index')) . '</span></li>';
				$forum_page['item_body']['info']['posts'] = '<li class="info-posts"><strong>'.forum_number_format($cur_set['num_posts']).'</strong> <span class="label">'.(($cur_set['num_posts'] == 1) ?
					__('post', 'index') : __('posts', 'index')) . '</span></li>';

				if ($cur_set['last_post'] != '')
					$forum_page['item_body']['info']['lastpost'] =
					'<li class="info-lastpost"><span class="label">' . __('Last post', 'index') . '</span> <strong><a href="'.link('post', $cur_set['last_post_id']).'">'.format_time($cur_set['last_post']).'</a></strong> <cite>'.
					sprintf(__('Last poster', 'index'), forum_htmlencode($cur_set['last_poster'])).'</cite></li>';
				else
					$forum_page['item_body']['info']['lastpost'] =
					'<li class="info-lastpost"><strong>' . __('Never') . '</strong></li>';

				($hook = get_hook('se_results_forums_row_normal_pre_display')) ? eval($hook) : null;
			}

			// Generate classes for this forum depending on its status
			$forum_page['item_style'] = (($forum_page['item_count'] % 2 != 0) ? ' odd' : ' even').(($forum_page['item_count'] == 1) ? ' main-first-item' : '').((!empty($forum_page['item_status'])) ? ' '.implode(' ', $forum_page['item_status']) : '');

			($hook = get_hook('se_results_forums_row_pre_display')) ? eval($hook) : null;

?>
			<div id="forum<?php echo $cur_set['fid'] ?>" class="main-item<?php echo $forum_page['item_style'] ?>">
				<span class="icon <?php echo implode(' ', $forum_page['item_status']) ?>"><!-- --></span>
				<div class="item-subject">
					<?php echo implode("\n\t\t\t\t", $forum_page['item_body']['subject'])."\n" ?>
				</div>
				<ul class="item-info">
					<?php echo implode("\n\t\t\t\t", $forum_page['item_body']['info'])."\n" ?>
				</ul>
			</div>
<?php
		}
	}
?>
	</div>

	<div class="main-foot">
<?php

	if (!empty($forum_page['main_foot_options']))
		echo "\n\t\t\t".'<p class="options">'.implode(' ', $forum_page['main_foot_options']).'</p>';

?>
		<h2 class="hn"><span><?php echo $forum_page['items_info'] ?></span></h2>
	</div>
<?php

($hook = get_hook('se_results_end')) ? eval($hook) : null;
