<?php
namespace punbb;


	// Reset arrays and globals for each forum
	$item_status = $item_subject = $item_body = $item_title = array();

	// Is this a redirect forum?
	if ($cur_forum['redirect_url'] != '') {
		$item_body['subject']['title'] =
			'<h3 class="hn"><a class="external" href="'.forum_htmlencode($cur_forum['redirect_url']).'" title="'.
				sprintf(__('Link to', 'index'), forum_htmlencode($cur_forum['redirect_url'])).'"><span>'.forum_htmlencode($cur_forum['forum_name']).'</span></a></h3>';
		$item_status['redirect'] = 'redirect';

		if ($cur_forum['forum_desc'] != '') {
			$item_subject['desc'] = $cur_forum['forum_desc'];
		}

		$item_subject['redirect'] =
			'<span>' . __('External forum', 'index') . '</span>';

		($hook = get_hook('in_redirect_row_pre_item_subject_merge')) ? eval($hook) : null;

		if (!empty($item_subject)) {
			$item_body['subject']['desc'] = '<p>' . implode(' ', $item_subject) . '</p>';
		}

		// Forum topic and post count
		$item_body['info']['topics'] =
			'<li class="info-topics"><span class="label">' . __('No topic info', 'index') . '</span></li>';
		$item_body['info']['posts'] =
			'<li class="info-posts"><span class="label">' . __('No post info', 'index') . '</span></li>';
		$item_body['info']['lastpost'] =
			'<li class="info-lastpost"><span class="label">' . __('No lastpost info', 'index') . '</span></li>';

		($hook = get_hook('in_redirect_row_pre_display')) ? eval($hook) : null;
	}
	else {
		// Setup the title and link to the forum
		$item_title['title'] = '<a href="' . link('forum', array($cur_forum['fid'], sef_friendly($cur_forum['forum_name']))).'"><span>'.forum_htmlencode($cur_forum['forum_name']).'</span></a>';

		// Are there new posts since our last visit?
		if (!user()->is_guest && $cur_forum['last_post'] > user()->last_visit && (empty($tracked_topics['forums'][$cur_forum['fid']]) || $cur_forum['last_post'] > $tracked_topics['forums'][$cur_forum['fid']]))
		{
			// There are new posts in this forum, but have we read all of them already?
			foreach ($new_topics[$cur_forum['fid']] as $check_topic_id => $check_last_post)
			{
				if ((empty($tracked_topics['topics'][$check_topic_id]) || $tracked_topics['topics'][$check_topic_id] < $check_last_post) && (empty($tracked_topics['forums'][$cur_forum['fid']]) || $tracked_topics['forums'][$cur_forum['fid']] < $check_last_post))
				{
					$item_status['new'] = 'new';
					$item_title['status'] = '<small>' . sprintf(__('Forum has new', 'index'),
							'<a href="'.link('search_new_results', $cur_forum['fid']).
							'" title="' . __('New posts title', 'index') . '">' .
							__('Forum new posts', 'index').'</a>').'</small>';
					break;
				}
			}
		}

		($hook = get_hook('in_normal_row_pre_item_title_merge')) ? eval($hook) : null;

		$item_body['subject']['title'] = '<h3 class="hn">' .
			implode(' ', $item_title) . '</h3>';

		// Setup the forum description and mod list
		if ($cur_forum['forum_desc'] != '') {
			$item_subject['desc'] = $cur_forum['forum_desc'];
		}

		if (config()->o_show_moderators == '1' && $cur_forum['moderators'] != '') {
			$mods_array = unserialize($cur_forum['moderators']);
			$item_mods = array();
			foreach ($mods_array as $mod_username => $mod_id) {
				$item_mods[] = (user()->g_view_users == '1')?
					'<a href="' . link('user', $mod_id) . '">' . forum_htmlencode($mod_username) . '</a>' :
					forum_htmlencode($mod_username);
			}

			($hook = get_hook('in_row_modify_modlist')) ? eval($hook) : null;

			$item_subject['modlist'] = '<span class="modlist">' .
				sprintf(__('Moderated by', 'index'), implode(', ', $item_mods)).'</span>';
		}

		($hook = get_hook('in_normal_row_pre_item_subject_merge')) ? eval($hook) : null;

		if (!empty($item_subject)) {
			$item_body['subject']['desc'] = '<p>' . implode(' ', $item_subject) . '</p>';
		}

		// Setup forum topics, post count and last post
		$item_body['info']['topics'] =
			'<li class="info-topics"><strong>'.forum_number_format($cur_forum['num_topics']).'</strong> <span class="label">'.(($cur_forum['num_topics'] == 1) ?
				__('topic', 'index') : __('topics', 'index')) . '</span></li>';
		$item_body['info']['posts'] =
			'<li class="info-posts"><strong>'.forum_number_format($cur_forum['num_posts']).'</strong> <span class="label">'.(($cur_forum['num_posts'] == 1) ?
				__('post', 'index') : __('posts', 'index')) . '</span></li>';

		if ($cur_forum['last_post'] != '')
			$item_body['info']['lastpost'] =
				'<li class="info-lastpost"><span class="label">' . __('Last post', 'index') .
				'</span> <strong><a href="'.link('post', $cur_forum['last_post_id']).'">'.format_time($cur_forum['last_post']).
				'</a></strong> <cite>'.sprintf(__('Last poster', 'index'), forum_htmlencode($cur_forum['last_poster'])).'</cite></li>';
		else
			$item_body['info']['lastpost'] =
				'<li class="info-lastpost"><strong>' . __('Never') . '</strong></li>';

		($hook = get_hook('in_normal_row_pre_display')) ? eval($hook) : null;
	}

	// Generate classes for this forum depending on its status
	$item_style = (($item_count % 2 != 0) ? ' odd' : ' even') .
		(($item_count == 1) ? ' main-first-item' : '') .
		((!empty($item_status)) ? ' ' . implode(' ', $item_status) : '');

	($hook = get_hook('in_row_pre_display')) ? eval($hook) : null;

?>

<div id="forum<?php echo $cur_forum['fid'] ?>" class="main-item<?= $item_style ?>">
	<span class="icon <?php echo implode(' ', $item_status) ?>"></span>
	<div class="item-subject">
		<?php echo implode("\n\t\t\t\t", $item_body['subject'])."\n" ?>
	</div>
	<ul class="item-info">
		<?php echo implode("\n\t\t\t\t", $item_body['info'])."\n" ?>
	</ul>
</div>