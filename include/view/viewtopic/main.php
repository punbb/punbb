<?php
namespace punbb;

($hook = get_hook('vt_main_output_start')) ? eval($hook) : null;

?>
	<div class="main-head">
<?php

	if (!empty($main_head_options))
		echo "\t\t".'<p class="options">'.implode(' ', $main_head_options).'</p>'."\n";

?>
		<h2 class="hn"><span><?php echo $forum_page['items_info'] ?></span></h2>
	</div>
	<div id="forum<?php echo $cur_topic['forum_id'] ?>" class="main-content main-topic">
<?php

$forum_page['item_count'] = 0;	// Keep track of post numbers


if (!empty($posts_id))
{
	$user_data_cache = array();
	while ($cur_post = db()->fetch_assoc($result))
	{
		($hook = get_hook('vt_post_loop_start')) ? eval($hook) : null;

		++$forum_page['item_count'];

		$forum_page['post_ident'] = array();
		$forum_page['author_ident'] = array();
		$forum_page['author_info'] = array();
		$forum_page['post_options'] = array();
		$forum_page['post_contacts'] = array();
		$forum_page['post_actions'] = array();
		$forum_page['message'] = array();

		// Generate the post heading
		$forum_page['post_ident']['num'] = '<span class="post-num">'.forum_number_format($forum_page['start_from'] + $forum_page['item_count']).'</span>';

		if ($cur_post['poster_id'] > 1)
			$forum_page['post_ident']['byline'] = '<span class="post-byline">'.sprintf((($cur_post['id'] == $cur_topic['first_post_id']) ?
				__('Topic byline', 'topic') : __('Reply byline', 'topic')), ((user()->g_view_users == '1') ? '<a title="'.
				sprintf(__('Go to profile', 'topic'), forum_htmlencode($cur_post['username'])).'" href="'.link('user', $cur_post['poster_id']).'">'.forum_htmlencode($cur_post['username']).'</a>' : '<strong>'.forum_htmlencode($cur_post['username']).'</strong>')).'</span>';
		else
			$forum_page['post_ident']['byline'] = '<span class="post-byline">'.sprintf((($cur_post['id'] == $cur_topic['first_post_id']) ?
				__('Topic byline', 'topic') : __('Reply byline', 'topic')), '<strong>'.forum_htmlencode($cur_post['username']).'</strong>').'</span>';

		$forum_page['post_ident']['link'] = '<span class="post-link"><a class="permalink" rel="bookmark" title="'.
			__('Permalink post', 'topic') . '" href="'.link('post', $cur_post['id']).'">'.format_time($cur_post['posted']).'</a></span>';

		if ($cur_post['edited'] != '')
			$forum_page['post_ident']['edited'] = '<span class="post-edit">'.
			sprintf(__('Last edited', 'topic'), forum_htmlencode($cur_post['edited_by']), format_time($cur_post['edited'])).'</span>';


		($hook = get_hook('vt_row_pre_post_ident_merge')) ? eval($hook) : null;

		if (isset($user_data_cache[$cur_post['poster_id']]['author_ident']))
			$forum_page['author_ident'] = $user_data_cache[$cur_post['poster_id']]['author_ident'];
		else
		{
			// Generate author identification
			if ($cur_post['poster_id'] > 1)
			{
				if (config()->o_avatars == '1' && user()->show_avatars != '0')
				{
					$forum_page['avatar_markup'] = generate_avatar_markup($cur_post['poster_id'], $cur_post['avatar'], $cur_post['avatar_width'], $cur_post['avatar_height'], $cur_post['username']);

					if (!empty($forum_page['avatar_markup']))
						$forum_page['author_ident']['avatar'] = '<li class="useravatar">'.$forum_page['avatar_markup'].'</li>';
				}

				$forum_page['author_ident']['username'] = '<li class="username">'.((user()->g_view_users == '1') ? '<a title="'.
					sprintf(__('Go to profile', 'topic'), forum_htmlencode($cur_post['username'])).'" href="'.link('user', $cur_post['poster_id']).'">'.forum_htmlencode($cur_post['username']).'</a>' : '<strong>'.forum_htmlencode($cur_post['username']).'</strong>').'</li>';
				$forum_page['author_ident']['usertitle'] = '<li class="usertitle"><span>'.get_title($cur_post).'</span></li>';

				if ($cur_post['is_online'] == $cur_post['poster_id'])
					$forum_page['author_ident']['status'] = '<li class="userstatus"><span>'.
					__('Online', 'topic') . '</span></li>';
				else
					$forum_page['author_ident']['status'] = '<li class="userstatus"><span>'.
					__('Offline', 'topic') . '</span></li>';
			}
			else
			{
				$forum_page['author_ident']['username'] = '<li class="username"><strong>'.forum_htmlencode($cur_post['username']).'</strong></li>';
				$forum_page['author_ident']['usertitle'] = '<li class="usertitle"><span>'.get_title($cur_post).'</span></li>';
			}
		}

		if (isset($user_data_cache[$cur_post['poster_id']]['author_info']))
			$forum_page['author_info'] = $user_data_cache[$cur_post['poster_id']]['author_info'];
		else
		{
			// Generate author information
			if ($cur_post['poster_id'] > 1)
			{
				if (config()->o_show_user_info == '1')
				{
					if ($cur_post['location'] != '')
					{
						if (config()->o_censoring == '1')
							$cur_post['location'] = censor_words($cur_post['location']);

						$forum_page['author_info']['from'] = '<li><span>'.
							__('From', 'topic') . ' <strong>'.forum_htmlencode($cur_post['location']).'</strong></span></li>';
					}

					$forum_page['author_info']['registered'] = '<li><span>'.
						__('Registered', 'topic') . ' <strong>'.format_time($cur_post['registered'], 1).'</strong></span></li>';

					if (config()->o_show_post_count == '1' || user()->is_admmod)
						$forum_page['author_info']['posts'] = '<li><span>'.
							__('Posts info', 'topic') . ' <strong>'.forum_number_format($cur_post['num_posts']).'</strong></span></li>';
				}

				if (user()->is_admmod)
				{
					if ($cur_post['admin_note'] != '')
						$forum_page['author_info']['note'] = '<li><span>'.
							__('Note', 'topic') . ' <strong>'.forum_htmlencode($cur_post['admin_note']).'</strong></span></li>';
				}
			}
		}

		// Generate IP information for moderators/administrators
		if (user()->is_admmod)
			$forum_page['author_info']['ip'] = '<li><span>'.
				__('IP', 'topic') . ' <a href="'.link('get_host', $cur_post['id']).'">'.$cur_post['poster_ip'].'</a></span></li>';

		// Generate author contact details
		if (config()->o_show_user_info == '1')
		{
			if (isset($user_data_cache[$cur_post['poster_id']]['post_contacts']))
				$forum_page['post_contacts'] = $user_data_cache[$cur_post['poster_id']]['post_contacts'];
			else
			{
				if ($cur_post['poster_id'] > 1)
				{
					if ($cur_post['url'] != '')
						$forum_page['post_contacts']['url'] = '<span class="user-url'.(empty($forum_page['post_contacts']) ? ' first-item' : '').'"><a class="external" href="'.
							forum_htmlencode((config()->o_censoring == '1') ? censor_words($cur_post['url']) : $cur_post['url']).'">'.
							sprintf(__('Visit website', 'topic'), '<span>'.
								sprintf(__('User possessive', 'topic'), forum_htmlencode($cur_post['username'])).'</span>').'</a></span>';
					if ((($cur_post['email_setting'] == '0' && !user()->is_guest) || user()->is_admmod) && user()->g_send_email == '1')
						$forum_page['post_contacts']['email'] = '<span class="user-email'.(empty($forum_page['post_contacts']) ? ' first-item' : '').'"><a href="mailto:'.forum_htmlencode($cur_post['email']).'">'.
						__('E-mail', 'topic') . '<span>&#160;'.forum_htmlencode($cur_post['username']).'</span></a></span>';
					else if ($cur_post['email_setting'] == '1' && !user()->is_guest && user()->g_send_email == '1')
						$forum_page['post_contacts']['email'] = '<span class="user-email'.(empty($forum_page['post_contacts']) ? ' first-item' : '').'"><a href="'.link('email', $cur_post['poster_id']).'">'.
						__('E-mail', 'topic') . '<span>&#160;'.forum_htmlencode($cur_post['username']).'</span></a></span>';
				}
				else
				{
					if ($cur_post['poster_email'] != '' && user()->is_admmod && user()->g_send_email == '1')
						$forum_page['post_contacts']['email'] = '<span class="user-email'.(empty($forum_page['post_contacts']) ? ' first-item' : '').'"><a href="mailto:'.forum_htmlencode($cur_post['poster_email']).'">'.
						__('E-mail', 'topic') . '<span>&#160;'.forum_htmlencode($cur_post['username']).'</span></a></span>';
				}
			}

			($hook = get_hook('vt_row_pre_post_contacts_merge')) ? eval($hook) : null;

			if (!empty($forum_page['post_contacts']))
				$forum_page['post_options']['contacts'] = '<p class="post-contacts">'.implode(' ', $forum_page['post_contacts']).'</p>';
		}

		// Generate the post options links
		if (!user()->is_guest)
		{
			$forum_page['post_actions']['report'] = '<span class="report-post'.(empty($forum_page['post_actions']) ? ' first-item' : '').'"><a href="'.link('report', $cur_post['id']).'">'.
				__('Report', 'topic') . '<span> '.
					__('Post', 'topic') . ' ' . forum_number_format($forum_page['start_from'] + $forum_page['item_count']).'</span></a></span>';

			if (!$forum_page['is_admmod'])
			{
				if ($cur_topic['closed'] == '0')
				{
					if ($cur_post['poster_id'] == user()->id)
					{
						if (($forum_page['start_from'] + $forum_page['item_count']) == 1 && user()->g_delete_topics == '1')
							$forum_page['post_actions']['delete'] = '<span class="delete-topic'.(empty($forum_page['post_actions']) ? ' first-item' : '').'"><a href="'.link('delete', $cur_topic['first_post_id']).'">'.
								__('Delete topic', 'topic') . '</a></span>';
						if (($forum_page['start_from'] + $forum_page['item_count']) > 1 && user()->g_delete_posts == '1')
							$forum_page['post_actions']['delete'] = '<span class="delete-post'.(empty($forum_page['post_actions']) ? ' first-item' : '').'"><a href="'.link('delete', $cur_post['id']).'">'.
								__('Delete', 'topic') . '<span> ' . __('Post', 'topic') . ' '.forum_number_format($forum_page['start_from'] + $forum_page['item_count']).'</span></a></span>';
						if (user()->g_edit_posts == '1')
							$forum_page['post_actions']['edit'] = '<span class="edit-post'.(empty($forum_page['post_actions']) ? ' first-item' : '').'"><a href="'.link('edit', $cur_post['id']).'">'.
								__('Edit', 'topic') . '<span> ' . __('Post', 'topic') . ' '.forum_number_format($forum_page['start_from'] + $forum_page['item_count']).'</span></a></span>';
					}

					if (($cur_topic['post_replies'] == '' && user()->g_post_replies == '1') || $cur_topic['post_replies'] == '1')
						$forum_page['post_actions']['quote'] = '<span class="quote-post'.(empty($forum_page['post_actions']) ? ' first-item' : '').'"><a href="'.link('quote', array($id, $cur_post['id'])).'">'.
							__('Quote', 'topic') . '<span> ' . __('Post', 'topic') . ' '.forum_number_format($forum_page['start_from'] + $forum_page['item_count']).'</span></a></span>';
				}
			}
			else
			{
				if (($forum_page['start_from'] + $forum_page['item_count']) == 1)
					$forum_page['post_actions']['delete'] = '<span class="delete-topic'.(empty($forum_page['post_actions']) ? ' first-item' : '').'"><a href="'.link('delete', $cur_topic['first_post_id']).'">'.
					__('Delete topic', 'topic') . '</a></span>';
				else
					$forum_page['post_actions']['delete'] = '<span class="delete-post'.(empty($forum_page['post_actions']) ? ' first-item' : '').'"><a href="'.link('delete', $cur_post['id']).'">'.
					__('Delete', 'topic') . '<span> ' . __('Post', 'topic') . ' '.forum_number_format($forum_page['start_from'] + $forum_page['item_count']).'</span></a></span>';

				$forum_page['post_actions']['edit'] = '<span class="edit-post'.(empty($forum_page['post_actions']) ? ' first-item' : '').'"><a href="'.link('edit', $cur_post['id']).'">'.
					__('Edit', 'topic') . '<span> ' . __('Post', 'topic') . ' '.forum_number_format($forum_page['start_from'] + $forum_page['item_count']).'</span></a></span>';
				$forum_page['post_actions']['quote'] = '<span class="quote-post'.(empty($forum_page['post_actions']) ? ' first-item' : '').'"><a href="'.link('quote', array($id, $cur_post['id'])).'">'.
					__('Quote', 'topic') . '<span> ' . __('Post', 'topic') . ' '.forum_number_format($forum_page['start_from'] + $forum_page['item_count']).'</span></a></span>';
			}
		}
		else
		{
			if ($cur_topic['closed'] == '0')
			{
				if (($cur_topic['post_replies'] == '' && user()->g_post_replies == '1') || $cur_topic['post_replies'] == '1')
					$forum_page['post_actions']['quote'] = '<span class="report-post'.(empty($forum_page['post_actions']) ? ' first-item' : '').'"><a href="'.link('quote', array($id, $cur_post['id'])).'">'.
					__('Quote', 'topic') . '<span> ' . __('Post', 'topic') . ' '.forum_number_format($forum_page['start_from'] + $forum_page['item_count']).'</span></a></span>';
			}
		}

		($hook = get_hook('vt_row_pre_post_actions_merge')) ? eval($hook) : null;

		if (!empty($forum_page['post_actions']))
			$forum_page['post_options']['actions'] = '<p class="post-actions">'.implode(' ', $forum_page['post_actions']).'</p>';

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

		// Do signature parsing/caching
		if ($cur_post['signature'] != '' && user()->show_sig != '0' &&
				config()->o_signatures == '1')
		{
			if (!isset($signature_cache[$cur_post['poster_id']]))
				$signature_cache[$cur_post['poster_id']] = parse_signature($cur_post['signature']);

			$forum_page['message']['signature'] = '<div class="sig-content"><span class="sig-line"><!-- --></span>'.$signature_cache[$cur_post['poster_id']].'</div>';
		}

		($hook = get_hook('vt_row_pre_display')) ? eval($hook) : null;

		// Do user data caching for the post
		if ($cur_post['poster_id'] > 1 && !isset($user_data_cache[$cur_post['poster_id']]))
		{
			$user_data_cache[$cur_post['poster_id']] = array(
				'author_ident'	=> $forum_page['author_ident'],
				'author_info'	=> $forum_page['author_info'],
				'post_contacts'	=> $forum_page['post_contacts']
			);

			($hook = get_hook('vt_row_add_user_data_cache')) ? eval($hook) : null;
		}

?>
		<div class="<?php echo implode(' ', $forum_page['item_status']) ?>">
			<div id="p<?php echo $cur_post['id'] ?>" class="posthead">
				<h3 class="hn post-ident"><?php echo implode(' ', $forum_page['post_ident']) ?></h3>
			</div>
			<div class="postbody<?php if ($cur_post['is_online'] == $cur_post['poster_id']) echo ' online'; ?>">
				<div class="post-author">
					<ul class="author-ident">
						<?php echo implode("\n\t\t\t\t\t\t", $forum_page['author_ident'])."\n" ?>
					</ul>
					<ul class="author-info">
						<?php echo implode("\n\t\t\t\t\t\t", $forum_page['author_info'])."\n" ?>
					</ul>
				</div>
				<div class="post-entry">
					<h4 id="pc<?php echo $cur_post['id'] ?>" class="entry-title hn"><?php echo $forum_page['item_subject'] ?></h4>
					<div class="entry-content">
						<?php echo implode("\n\t\t\t\t\t\t", $forum_page['message'])."\n" ?>
					</div>
<?php ($hook = get_hook('vt_row_new_post_entry_data')) ? eval($hook) : null; ?>
				</div>
			</div>
<?php if (!empty($forum_page['post_options'])): ?>
			<div class="postfoot">
				<div class="post-options">
					<?php echo implode("\n\t\t\t\t\t", $forum_page['post_options'])."\n" ?>
				</div>
			</div>
<?php endif; ?>
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

($hook = get_hook('vt_end')) ? eval($hook) : null;
