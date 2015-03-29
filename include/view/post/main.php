<?php

($hook = get_hook('po_main_output_start')) ? eval($hook) : null;

?>
	<div class="main-head">
		<h2 class="hn"><span><?php echo $tid ? $lang_post['Post reply'] : $lang_post['Post new topic'] ?></span></h2>
	</div>
<?php

// If preview selected and there are no errors
if (isset($_POST['preview']) && empty($errors))
{
	if (!defined('FORUM_PARSER_LOADED'))
		require FORUM_ROOT.'include/parser.php';

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
<?php

}

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo ($tid) ? $lang_post['Compose your reply'] : $lang_post['Compose your topic'] ?></span></h2>
	</div>
	<div id="post-form" class="main-content main-frm">
<?php

	if (!empty($forum_page['text_options']))
		echo "\t\t".'<p class="ct-options options">'.sprintf($lang_common['You may use'], implode(' ', $forum_page['text_options'])).'</p>'."\n";

	// If there were any errors, show them
	if (!empty($errors))
	{
		$forum_page['errors'] = array();
		foreach ($errors as $cur_error)
			$forum_page['errors'][] = '<li class="warn"><span>'.$cur_error.'</span></li>';

		($hook = get_hook('po_pre_post_errors')) ? eval($hook) : null;

?>
		<div class="ct-box error-box">
			<h2 class="warn hn"><?php echo $lang_post['Post errors'] ?></h2>
			<ul class="error-list">
				<?php echo implode("\n\t\t\t\t", $forum_page['errors'])."\n" ?>
			</ul>
		</div>
<?php

	}

?>
		<div id="req-msg" class="req-warn ct-box error-box">
			<p class="important"><?php echo $lang_common['Required warn'] ?></p>
		</div>
		<form id="afocus" class="frm-form frm-ctrl-submit" method="post" accept-charset="utf-8" action="<?php echo $forum_page['form_action'] ?>"<?php if (!empty($forum_page['form_attributes'])) echo ' '.implode(' ', $forum_page['form_attributes']) ?>>
			<div class="hidden">
				<?php echo implode("\n\t\t\t\t", $forum_page['hidden_fields'])."\n" ?>
			</div>
<?php

if ($forum_user['is_guest'])
{
	$forum_page['email_form_name'] = ($forum_config['p_force_guest_email'] == '1') ? 'req_email' : 'email';

	($hook = get_hook('po_pre_guest_info_fieldset')) ? eval($hook) : null;

?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo $lang_post['Guest post legend'] ?></strong></legend>
<?php ($hook = get_hook('po_pre_guest_username')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text required">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_post['Guest name'] ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="req_username" value="<?php if (isset($_POST['req_username'])) echo forum_htmlencode($username); ?>" size="35" maxlength="25" /></span>
					</div>
				</div>
<?php ($hook = get_hook('po_pre_guest_email')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text<?php if ($forum_config['p_force_guest_email'] == '1') echo ' required' ?>">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_post['Guest e-mail'] ?></span></label><br />
						<span class="fld-input"><input type="email" id="fld<?php echo $forum_page['fld_count'] ?>" name="<?php echo $forum_page['email_form_name'] ?>" value="<?php if (isset($_POST[$forum_page['email_form_name']])) echo forum_htmlencode($email); ?>" size="35" maxlength="80" <?php if ($forum_config['p_force_guest_email'] == '1') echo 'required' ?> /></span>
					</div>
				</div>
<?php ($hook = get_hook('po_pre_guest_info_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php

	($hook = get_hook('po_guest_info_fieldset_end')) ? eval($hook) : null;

	// Reset counters
	$forum_page['group_count'] = $forum_page['item_count'] = 0;
}

($hook = get_hook('po_pre_req_info_fieldset')) ? eval($hook) : null;

?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo $lang_common['Required information'] ?></strong></legend>
<?php

if ($fid)
{
	($hook = get_hook('po_pre_req_subject')) ? eval($hook) : null;

?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text required longtext">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_post['Topic subject'] ?></span></label><br />
						<span class="fld-input"><input id="fld<?php echo $forum_page['fld_count'] ?>" type="text" name="req_subject" value="<?php if (isset($_POST['req_subject'])) echo forum_htmlencode($subject); ?>" size="<?php echo FORUM_SUBJECT_MAXIMUM_LENGTH ?>" maxlength="<?php echo FORUM_SUBJECT_MAXIMUM_LENGTH ?>" required /></span>
					</div>
				</div>
<?php

}

($hook = get_hook('po_pre_post_contents')) ? eval($hook) : null;

?>
				<div class="txt-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="txt-box textarea required">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_post['Write message'] ?></span></label>
						<div class="txt-input"><span class="fld-input"><textarea id="fld<?php echo $forum_page['fld_count'] ?>" name="req_message" rows="15" cols="95" required spellcheck="true"><?php echo isset($_POST['req_message']) ? forum_htmlencode($message) : (isset($forum_page['quote']) ? forum_htmlencode($forum_page['quote']) : '') ?></textarea></span></div>
					</div>
				</div>
<?php

$forum_page['checkboxes'] = array();
if ($forum_config['o_smilies'] == '1')
	$forum_page['checkboxes']['hide_smilies'] = '<div class="mf-item"><span class="fld-input"><input type="checkbox" id="fld'.(++$forum_page['fld_count']).'" name="hide_smilies" value="1"'.(isset($_POST['hide_smilies']) ? ' checked="checked"' : '').' /></span> <label for="fld'.$forum_page['fld_count'].'">'.$lang_post['Hide smilies'].'</label></div>';

// Check/uncheck the checkbox for subscriptions depending on scenario
if (!$forum_user['is_guest'] && $forum_config['o_subscriptions'] == '1')
{
	$subscr_checked = false;

	// If it's a preview
	if (isset($_POST['preview']))
		$subscr_checked = isset($_POST['subscribe']) ? true : false;
	// If auto subscribed
	else if ($forum_user['auto_notify'])
		$subscr_checked = true;
	// If already subscribed to the topic
	else if ($is_subscribed)
		$subscr_checked = true;

	$forum_page['checkboxes']['subscribe'] = '<div class="mf-item"><span class="fld-input"><input type="checkbox" id="fld'.(++$forum_page['fld_count']).'" name="subscribe" value="1"'.($subscr_checked ? ' checked="checked"' : '').' /></span> <label for="fld'.$forum_page['fld_count'].'">'.($is_subscribed ? $lang_post['Stay subscribed'] : $lang_post['Subscribe']).'</label></div>';
}

($hook = get_hook('po_pre_optional_fieldset')) ? eval($hook) : null;

if (!empty($forum_page['checkboxes']))
{

?>
				<fieldset class="mf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="mf-box checkbox">
						<?php echo implode("\n\t\t\t\t\t", $forum_page['checkboxes'])."\n" ?>
					</div>
<?php ($hook = get_hook('po_pre_checkbox_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php

}

($hook = get_hook('po_pre_req_info_fieldset_end')) ? eval($hook) : null;

?>
			</fieldset>
<?php

($hook = get_hook('po_req_info_fieldset_end')) ? eval($hook) : null;

?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="submit_button" value="<?php echo ($tid) ? $lang_post['Submit reply'] : $lang_post['Submit topic'] ?>" /></span>
				<span class="submit"><input type="submit" name="preview" value="<?php echo ($tid) ? $lang_post['Preview reply'] : $lang_post['Preview topic'] ?>" /></span>
			</div>
		</form>
	</div>
<?php


// Check if the topic review is to be displayed
if ($tid && $forum_config['o_topic_review'] != '0')
{
	if (!defined('FORUM_PARSER_LOADED'))
		require FORUM_ROOT.'include/parser.php';

	// Get the amount of posts in the topic
	$query = array(
		'SELECT'	=> 'count(p.id)',
		'FROM'		=> 'posts AS p',
		'WHERE'		=> 'topic_id='.$tid
	);

	($hook = get_hook('po_topic_review_qr_get_post_count')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	$forum_page['total_post_count'] = $forum_db->result($result, 0);

	// Get posts to display in topic review
	$query = array(
		'SELECT'	=> 'p.id, p.poster, p.message, p.hide_smilies, p.posted',
		'FROM'		=> 'posts AS p',
		'WHERE'		=> 'topic_id='.$tid,
		'ORDER BY'	=> 'id DESC',
		'LIMIT'		=> $forum_config['o_topic_review']
	);

	($hook = get_hook('po_topic_review_qr_get_topic_review_posts')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

	$posts = array();
	while ($cur_post = $forum_db->fetch_assoc($result))
	{
		$posts[] = $cur_post;
	}

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo $lang_post['Topic review'] ?></span></h2>
	</div>
	<div id="topic-review" class="main-content main-frm">
<?php

	$forum_page['item_count'] = 0;
	$forum_page['item_total'] = count($posts);

	foreach ($posts as $cur_post)
	{
		++$forum_page['item_count'];

		$forum_page['message'] = parse_message($cur_post['message'], $cur_post['hide_smilies']);

		// Generate the post heading
		$forum_page['post_ident'] = array();
		$forum_page['post_ident']['num'] = '<span class="post-num">'.forum_number_format($forum_page['total_post_count'] - $forum_page['item_count'] + 1).'</span>';
		$forum_page['post_ident']['byline'] = '<span class="post-byline">'.sprintf($lang_post['Post byline'], '<strong>'.forum_htmlencode($cur_post['poster']).'</strong>').'</span>';
		$forum_page['post_ident']['link'] = '<span class="post-link"><a class="permalink" rel="bookmark" title="'.$lang_post['Permalink post'].'" href="'.forum_link($forum_url['post'], $cur_post['id']).'">'.format_time($cur_post['posted']).'</a></span>';

		($hook = get_hook('po_topic_review_row_pre_display')) ? eval($hook) : null;

?>
		<div class="post<?php if ($forum_page['item_count'] == 1) echo ' firstpost'; ?><?php if ($forum_page['item_total'] == $forum_page['item_count']) echo ' lastpost'; ?>">
			<div class="posthead">
				<h3 class="hn post-ident"><?php echo implode(' ', $forum_page['post_ident']) ?></h3>
<?php ($hook = get_hook('po_topic_review_new_post_head_option')) ? eval($hook) : null; ?>
			</div>
			<div class="postbody">
				<div class="post-entry">
					<div class="entry-content">
						<?php echo $forum_page['message']."\n" ?>
<?php ($hook = get_hook('po_topic_review_new_post_entry_data')) ? eval($hook) : null; ?>
					</div>
				</div>
			</div>
		</div>
<?php

	}

?>
	</div>
<?php

}

$forum_id = $cur_posting['id'];

($hook = get_hook('po_end')) ? eval($hook) : null;
