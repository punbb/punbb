<?php
namespace punbb;

$forum_page['item_header'] = array();
$forum_page['item_header']['subject']['title'] = '<strong class="subject-title">'.
	__('Topics', 'forum') . '</strong>';

if ($forum_config['o_topic_views'] == '1')
	$forum_page['item_header']['info']['views'] = '<strong class="info-views">'.
	__('views', 'forum') . '</strong>';

$forum_page['item_header']['info']['replies'] = '<strong class="info-replies">'.
	__('replies', 'forum') . '</strong>';
$forum_page['item_header']['info']['lastpost'] = '<strong class="info-lastpost">'.
	__('last post', 'forum') . '</strong>';

($hook = get_hook('mr_topic_actions_output_start')) ? eval($hook) : null;

	include view('moderate/topics_start');

	$forum_page['item_count'] = 0;

	while ($cur_topic = db()->fetch_assoc($result))
	{
		($hook = get_hook('mr_topic_actions_row_loop_start')) ? eval($hook) : null;

		++$forum_page['item_count'];

		// Start from scratch
		$forum_page['item_subject'] = $forum_page['item_body'] = $forum_page['item_status'] = $forum_page['item_nav'] = $forum_page['item_title'] = $forum_page['item_title_status'] = array();

		if ($forum_config['o_censoring'] == '1')
			$cur_topic['subject'] = censor_words($cur_topic['subject']);

		$forum_page['item_subject']['starter'] = '<span class="item-starter">'.
			sprintf(__('Topic starter', 'forum'), forum_htmlencode($cur_topic['poster'])).'</span>';

		if ($cur_topic['moved_to'] != null)
		{
			$forum_page['item_status']['moved'] = 'moved';
			$forum_page['item_title']['link'] = '<span class="item-status"><em class="moved">'.
				sprintf(__('Item status', 'forum'), __('Moved', 'forum')).'</em></span> <a href="'.forum_link($forum_url['topic'], array($cur_topic['moved_to'], sef_friendly($cur_topic['subject']))).'">'.forum_htmlencode($cur_topic['subject']).'</a>';

			// Combine everything to produce the Topic heading
			$forum_page['item_body']['subject']['title'] = '<h3 class="hn"><span class="item-num">'.forum_number_format($forum_page['start_from'] + $forum_page['item_count']).'</span> <strong>'.$forum_page['item_title']['link'].'</strong></h3>';

			($hook = get_hook('mr_topic_actions_moved_row_pre_item_subject_merge')) ? eval($hook) : null;

			if ($forum_config['o_topic_views'] == '1')
				$forum_page['item_body']['info']['views'] = '<li class="info-views"><span class="label">'.
					__('No views info', 'forum') . '</span></li>';

			$forum_page['item_body']['info']['replies'] = '<li class="info-replies"><span class="label">'.
				__('No replies info', 'forum') . '</span></li>';
			$forum_page['item_body']['info']['lastpost'] = '<li class="info-lastpost"><span class="label">'.
				__('No lastpost info', 'forum') . '</span></li>';
			$forum_page['item_body']['info']['select'] = '<li class="info-select"><input id="fld'.++$forum_page['fld_count'].'" type="checkbox" name="topics[]" value="'.$cur_topic['id'].'" /> <label for="fld'.$forum_page['fld_count'].'">'.
				sprintf(__('Select topic', 'forum'), forum_htmlencode($cur_topic['subject'])).'</label></li>';

			($hook = get_hook('mr_topic_actions_moved_row_pre_output')) ? eval($hook) : null;
		}
		else
		{
			$forum_page['ghost_topic'] = false;

			// First assemble the Topic heading

			// Should we display the dot or not? :)
			if (!$forum_user['is_guest'] && $forum_config['o_show_dot'] == '1' && $cur_topic['has_posted'] == $forum_user['id'])
			{
				$forum_page['item_title']['posted'] = '<span class="posted-mark">'.
					__('You posted indicator', 'forum') . '</span>';
				$forum_page['item_status']['posted'] = 'posted';
			}

			if ($cur_topic['sticky'] == '1')
			{
				$forum_page['item_title_status']['sticky'] = '<em class="sticky">'.
					__('Sticky', 'forum') . '</em>';
				$forum_page['item_status']['sticky'] = 'sticky';
			}

			if ($cur_topic['closed'] == '1')
			{
				$forum_page['item_title_status']['closed'] = '<em class="closed">'.
					__('Closed', 'forum') . '</em>';
				$forum_page['item_status']['closed'] = 'closed';
			}

			($hook = get_hook('mr_topic_loop_normal_topic_pre_item_title_status_merge')) ? eval($hook) : null;

			if (!empty($forum_page['item_title_status']))
				$forum_page['item_title']['status'] = '<span class="item-status">'.
					sprintf(__('Item status', 'forum'), implode(', ', $forum_page['item_title_status'])).'</span>';

			$forum_page['item_title']['link'] = '<a href="'.forum_link($forum_url['topic'], array($cur_topic['id'], sef_friendly($cur_topic['subject']))).'">'.forum_htmlencode($cur_topic['subject']).'</a>';

			($hook = get_hook('mr_topic_loop_normal_topic_pre_item_title_merge')) ? eval($hook) : null;

			$forum_page['item_body']['subject']['title'] = '<h3 class="hn"><span class="item-num">'.forum_number_format($forum_page['start_from'] + $forum_page['item_count']).'</span> '.implode(' ', $forum_page['item_title']).'</h3>';


			if (empty($forum_page['item_status']))
				$forum_page['item_status']['normal'] = 'normal';

			$forum_page['item_pages'] = ceil(($cur_topic['num_replies'] + 1) / $forum_user['disp_posts']);

			if ($forum_page['item_pages'] > 1)
				$forum_page['item_nav']['pages'] = '<span>'.
					__('Pages', 'forum') . '&#160;</span>'.paginate($forum_page['item_pages'], -1, $forum_url['topic'],
					__('Page separator'), array($cur_topic['id'], sef_friendly($cur_topic['subject'])));

			// Does this topic contain posts we haven't read? If so, tag it accordingly.
			if (!$forum_user['is_guest'] && $cur_topic['last_post'] > $forum_user['last_visit'] && (!isset($tracked_topics['topics'][$cur_topic['id']]) || $tracked_topics['topics'][$cur_topic['id']] < $cur_topic['last_post']) && (!isset($tracked_topics['forums'][$fid]) || $tracked_topics['forums'][$fid] < $cur_topic['last_post']))
			{
				$forum_page['item_nav']['new'] = '<em class="item-newposts"><a href="'.forum_link($forum_url['topic_new_posts'], array($cur_topic['id'], sef_friendly($cur_topic['subject']))).'">'.
					__('New posts', 'forum') . '</a></em>';
				$forum_page['item_status']['new'] = 'new';
			}

			($hook = get_hook('mr_topic_loop_normal_topic_pre_item_nav_merge')) ? eval($hook) : null;

			if (!empty($forum_page['item_nav']))
				$forum_page['item_subject']['nav'] = '<span class="item-nav">'.
					sprintf(__('Topic navigation', 'forum'), implode('&#160;&#160;', $forum_page['item_nav'])).'</span>';

			// Assemble the Topic subject

			$forum_page['item_body']['info']['replies'] = '<li class="info-replies"><strong>'.forum_number_format($cur_topic['num_replies']).'</strong> <span class="label">'.(($cur_topic['num_replies'] == 1) ?
					__('Reply', 'forum') : __('Replies', 'forum')).'</span></li>';

			if ($forum_config['o_topic_views'] == '1')
				$forum_page['item_body']['info']['views'] = '<li class="info-views"><strong>'.forum_number_format($cur_topic['num_views']).'</strong> <span class="label">'.(($cur_topic['num_views'] == 1) ?
						__('View', 'forum') : __('Views', 'forum')).'</span></li>';

			$forum_page['item_body']['info']['lastpost'] = '<li class="info-lastpost"><span class="label">'.
				__('Last post', 'forum') . '</span> <strong><a href="'.forum_link($forum_url['post'], $cur_topic['last_post_id']).'">'.format_time($cur_topic['last_post']).'</a></strong> <cite>'.
					sprintf(__('by poster', 'forum'), forum_htmlencode($cur_topic['last_poster'])).'</cite></li>';
			$forum_page['item_body']['info']['select'] = '<li class="info-select"><input id="fld'.++$forum_page['fld_count'].'" type="checkbox" name="topics[]" value="'.$cur_topic['id'].'" /> <label for="fld'.$forum_page['fld_count'].'">'.
				sprintf(__('Select topic', 'forum'), forum_htmlencode($cur_topic['subject'])).'</label></li>';

			($hook = get_hook('mr_topic_actions_normal_row_pre_output')) ? eval($hook) : null;
		}

		$forum_page['item_body']['subject']['desc'] = '<p>'.implode(' ', $forum_page['item_subject']).'</p>';

		($hook = get_hook('mr_topic_actions_row_pre_item_status_merge')) ? eval($hook) : null;

		$forum_page['item_style'] = (($forum_page['item_count'] % 2 != 0) ? ' odd' : ' even').(($forum_page['item_count'] == 1) ? ' main-first-item' : '').((!empty($forum_page['item_status'])) ? ' '.implode(' ', $forum_page['item_status']) : '');

		($hook = get_hook('mr_topic_actions_row_pre_display')) ? eval($hook) : null;

		include view('moderate/topic');
	}

	include view('moderate/topics_end');

$forum_id = $fid;

// Init JS helper for select-all
$forum_loader->add_js('PUNBB.common.addDOMReadyEvent(PUNBB.common.initToggleCheckboxes);', array('type' => 'inline'));

($hook = get_hook('mr_end')) ? eval($hook) : null;
