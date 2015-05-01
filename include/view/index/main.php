<?php
namespace punbb;

($hook = get_hook('in_main_output_start')) ? eval($hook) : null;

$forum_page['cur_category'] = $forum_page['cat_count'] = $forum_page['item_count'] = 0;

while ($cur_forum = db()->fetch_assoc($result)) {
	($hook = get_hook('in_forum_loop_start')) ? eval($hook) : null;

	++$forum_page['item_count'];

	if ($cur_forum['cid'] != $forum_page['cur_category'])	{
		// A new category since last iteration?

		if ($forum_page['cur_category'] != 0) {
			include view('index/category_end');
		}

		++$forum_page['cat_count'];
		$forum_page['item_count'] = 1;

		$forum_page['item_header'] = array();
		$forum_page['item_header']['subject']['title'] =
			'<strong class="subject-title">' . __('Forums', 'index').'</strong>';
		$forum_page['item_header']['info']['topics'] =
			'<strong class="info-topics">' . __('topics', 'index') . '</strong>';
		$forum_page['item_header']['info']['post'] =
			'<strong class="info-posts">' . __('posts', 'index') . '</strong>';
		$forum_page['item_header']['info']['lastpost'] =
			'<strong class="info-lastpost">' . __('last post', 'index') . '</strong>';

		($hook = get_hook('in_forum_pre_cat_head')) ? eval($hook) : null;

		$forum_page['cur_category'] = $cur_forum['cid'];

		include view('index/category_start');
	}

	// Reset arrays and globals for each forum
	$forum_page['item_status'] = $forum_page['item_subject'] = $forum_page['item_body'] = $forum_page['item_title'] = array();

	// Is this a redirect forum?
	if ($cur_forum['redirect_url'] != '') {
		$forum_page['item_body']['subject']['title'] =
			'<h3 class="hn"><a class="external" href="'.forum_htmlencode($cur_forum['redirect_url']).'" title="'.
				sprintf(__('Link to', 'index'), forum_htmlencode($cur_forum['redirect_url'])).'"><span>'.forum_htmlencode($cur_forum['forum_name']).'</span></a></h3>';
		$forum_page['item_status']['redirect'] = 'redirect';

		if ($cur_forum['forum_desc'] != '')
			$forum_page['item_subject']['desc'] = $cur_forum['forum_desc'];

		$forum_page['item_subject']['redirect'] =
			'<span>' . __('External forum', 'index') . '</span>';

		($hook = get_hook('in_redirect_row_pre_item_subject_merge')) ? eval($hook) : null;

		if (!empty($forum_page['item_subject']))
			$forum_page['item_body']['subject']['desc'] = '<p>'.implode(' ', $forum_page['item_subject']).'</p>';

		// Forum topic and post count
		$forum_page['item_body']['info']['topics'] =
			'<li class="info-topics"><span class="label">' . __('No topic info', 'index') . '</span></li>';
		$forum_page['item_body']['info']['posts'] =
			'<li class="info-posts"><span class="label">' . __('No post info', 'index') . '</span></li>';
		$forum_page['item_body']['info']['lastpost'] =
			'<li class="info-lastpost"><span class="label">' . __('No lastpost info', 'index') . '</span></li>';

		($hook = get_hook('in_redirect_row_pre_display')) ? eval($hook) : null;
	}
	else {
		// Setup the title and link to the forum
		$forum_page['item_title']['title'] = '<a href="'.link('forum', array($cur_forum['fid'], sef_friendly($cur_forum['forum_name']))).'"><span>'.forum_htmlencode($cur_forum['forum_name']).'</span></a>';

		// Are there new posts since our last visit?
		if (!user()->is_guest && $cur_forum['last_post'] > user()->last_visit && (empty($tracked_topics['forums'][$cur_forum['fid']]) || $cur_forum['last_post'] > $tracked_topics['forums'][$cur_forum['fid']]))
		{
			// There are new posts in this forum, but have we read all of them already?
			foreach ($new_topics[$cur_forum['fid']] as $check_topic_id => $check_last_post)
			{
				if ((empty($tracked_topics['topics'][$check_topic_id]) || $tracked_topics['topics'][$check_topic_id] < $check_last_post) && (empty($tracked_topics['forums'][$cur_forum['fid']]) || $tracked_topics['forums'][$cur_forum['fid']] < $check_last_post))
				{
					$forum_page['item_status']['new'] = 'new';
					$forum_page['item_title']['status'] =
						'<small>' . sprintf(__('Forum has new', 'index'),
							'<a href="'.link('search_new_results', $cur_forum['fid']).
							'" title="' . __('New posts title', 'index') . '">' .
							__('Forum new posts', 'index').'</a>').'</small>';

					break;
				}
			}
		}

		($hook = get_hook('in_normal_row_pre_item_title_merge')) ? eval($hook) : null;

		$forum_page['item_body']['subject']['title'] = '<h3 class="hn">'.implode(' ', $forum_page['item_title']).'</h3>';


		// Setup the forum description and mod list
		if ($cur_forum['forum_desc'] != '')
			$forum_page['item_subject']['desc'] = $cur_forum['forum_desc'];

		if (config()->o_show_moderators == '1' && $cur_forum['moderators'] != '')
		{
			$forum_page['mods_array'] = unserialize($cur_forum['moderators']);
			$forum_page['item_mods'] = array();

			foreach ($forum_page['mods_array'] as $mod_username => $mod_id)
				$forum_page['item_mods'][] = (user()->g_view_users == '1') ? '<a href="'.link('user', $mod_id).'">'.forum_htmlencode($mod_username).'</a>' : forum_htmlencode($mod_username);

			($hook = get_hook('in_row_modify_modlist')) ? eval($hook) : null;

			$forum_page['item_subject']['modlist'] =
				'<span class="modlist">' .
					sprintf(__('Moderated by', 'index'), implode(', ', $forum_page['item_mods'])).'</span>';
		}

		($hook = get_hook('in_normal_row_pre_item_subject_merge')) ? eval($hook) : null;

		if (!empty($forum_page['item_subject']))
			$forum_page['item_body']['subject']['desc'] = '<p>'.implode(' ', $forum_page['item_subject']).'</p>';


		// Setup forum topics, post count and last post
		$forum_page['item_body']['info']['topics'] =
			'<li class="info-topics"><strong>'.forum_number_format($cur_forum['num_topics']).'</strong> <span class="label">'.(($cur_forum['num_topics'] == 1) ?
				__('topic', 'index') : __('topics', 'index')) . '</span></li>';
		$forum_page['item_body']['info']['posts'] =
			'<li class="info-posts"><strong>'.forum_number_format($cur_forum['num_posts']).'</strong> <span class="label">'.(($cur_forum['num_posts'] == 1) ?
				__('post', 'index') : __('posts', 'index')) . '</span></li>';

		if ($cur_forum['last_post'] != '')
			$forum_page['item_body']['info']['lastpost'] =
				'<li class="info-lastpost"><span class="label">' . __('Last post', 'index') .
				'</span> <strong><a href="'.link('post', $cur_forum['last_post_id']).'">'.format_time($cur_forum['last_post']).
				'</a></strong> <cite>'.sprintf(__('Last poster', 'index'), forum_htmlencode($cur_forum['last_poster'])).'</cite></li>';
		else
			$forum_page['item_body']['info']['lastpost'] =
				'<li class="info-lastpost"><strong>' . __('Never') . '</strong></li>';

		($hook = get_hook('in_normal_row_pre_display')) ? eval($hook) : null;
	}

	// Generate classes for this forum depending on its status
	$forum_page['item_style'] = (($forum_page['item_count'] % 2 != 0) ? ' odd' : ' even').(($forum_page['item_count'] == 1) ? ' main-first-item' : '').((!empty($forum_page['item_status'])) ? ' '.implode(' ', $forum_page['item_status']) : '');

	($hook = get_hook('in_row_pre_display')) ? eval($hook) : null;

	include view('index/topic');
}
// Did we output any categories and forums?
if ($forum_page['cur_category'] > 0) {
	include view('index/category_end');
}
else {
	include view('index/empty');
}

($hook = get_hook('in_end')) ? eval($hook) : null;
