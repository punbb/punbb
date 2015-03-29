<?php

($hook = get_hook('arp_main_output_start')) ? eval($hook) : null;

// Fetch any unread reports
$query = array(
	'SELECT'	=> 'r.id, r.topic_id, r.forum_id, r.reported_by, r.created, r.message, p.id AS pid, t.subject, f.forum_name, u.username AS reporter',
	'FROM'		=> 'reports AS r',
	'JOINS'		=> array(
		array(
			'LEFT JOIN'		=> 'posts AS p',
			'ON'			=> 'r.post_id=p.id'
		),
		array(
			'LEFT JOIN'		=> 'topics AS t',
			'ON'			=> 'r.topic_id=t.id'
		),
		array(
			'LEFT JOIN'		=> 'forums AS f',
			'ON'			=> 'r.forum_id=f.id'
		),
		array(
			'LEFT JOIN'		=> 'users AS u',
			'ON'			=> 'r.reported_by=u.id'
		)
	),
	'WHERE'		=> 'r.zapped IS NULL',
	'ORDER BY'	=> 'r.created DESC'
);

($hook = get_hook('arp_qr_get_new_reports')) ? eval($hook) : null;

$forum_page['new_reports'] = false;
$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

$unread_reports = array();
while ($cur_report = $forum_db->fetch_assoc($result))
{
	$unread_reports[] = $cur_report;
}

if (!empty($unread_reports))
{
	$forum_page['new_reports'] = true;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo $lang_admin_reports['New reports heading'] ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<form id="arp-new-report-form" class="frm-form" method="post" accept-charset="utf-8" action="<?php echo forum_link($forum_url['admin_reports']) ?>?action=zap">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token(forum_link($forum_url['admin_reports']).'?action=zap') ?>" />
			</div>
<?php

	$forum_page['item_num'] = 0;

	foreach ($unread_reports as $cur_report)
	{
		$reporter = ($cur_report['reporter'] != '') ? '<a href="'.forum_link($forum_url['user'], $cur_report['reported_by']).'">'.forum_htmlencode($cur_report['reporter']).'</a>' : $lang_admin_reports['Deleted user'];
		$forum = ($cur_report['forum_name'] != '') ? '<a href="'.forum_link($forum_url['forum'], array($cur_report['forum_id'], sef_friendly($cur_report['forum_name']))).'">'.forum_htmlencode($cur_report['forum_name']).'</a>' : $lang_admin_reports['Deleted forum'];
		$topic = ($cur_report['subject'] != '') ? '<a href="'.forum_link($forum_url['topic'], array($cur_report['topic_id'], sef_friendly($cur_report['subject']))).'">'.forum_htmlencode($cur_report['subject']).'</a>' : $lang_admin_reports['Deleted topic'];
		$message = str_replace("\n", '<br />', forum_htmlencode($cur_report['message']));
		$post_id = ($cur_report['pid'] != '') ? '<a href="'.forum_link($forum_url['post'], $cur_report['pid']).'">'.sprintf($lang_admin_reports['Post'], $cur_report['pid']).'</a>' : $lang_admin_reports['Deleted post'];

		($hook = get_hook('arp_new_report_pre_display')) ? eval($hook) : null;

?>
			<div class="ct-set warn-set report set<?php echo ++$forum_page['item_count'] ?>">
				<div class="ct-box warn-box">
					<h3 class="ct-legend hn"><strong><?php echo ++$forum_page['item_num'] ?></strong> <cite class="username"><?php printf($lang_admin_reports['Reported by'], $reporter) ?></cite> <span><?php echo format_time($cur_report['created']) ?></span></h3>
					<h4 class="hn"><?php echo $forum ?> &rarr; <?php echo $topic ?> &rarr; <?php echo $post_id ?></h4>
					<p><?php echo $message ?></p>
					<p class="item-select"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="reports[<?php echo $cur_report['id'] ?>]" value="1" /> <label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo $lang_admin_reports['Select report'] ?></label></p>
<?php ($hook = get_hook('arp_new_report_new_block')) ? eval($hook) : null; ?>
				</div>
			</div>
<?php

	}

?>
			<div class="frm-buttons">
				<span class="select-all js_link" data-check-form="arp-new-report-form"><?php echo $lang_admin_common['Select all'] ?></span>
				<span class="submit primary"><input type="submit" name="mark_as_read" value="<?php echo $lang_admin_reports['Mark read'] ?>" /></span>
			</div>
		</form>
	</div>
<?php

}

// Fetch the last 10 reports marked as read
$query = array(
	'SELECT'	=> 'r.id, r.topic_id, r.forum_id, r.reported_by, r.created, r.message, r.zapped, r.zapped_by AS zapped_by_id, p.id AS pid, t.subject, f.forum_name, u.username AS reporter, u2.username AS zapped_by',
	'FROM'		=> 'reports AS r',
	'JOINS'		=> array(
		array(
			'LEFT JOIN'		=> 'posts AS p',
			'ON'			=> 'r.post_id=p.id'
		),
		array(
			'LEFT JOIN'		=> 'topics AS t',
			'ON'			=> 'r.topic_id=t.id'
		),
		array(
			'LEFT JOIN'		=> 'forums AS f',
			'ON'			=> 'r.forum_id=f.id'
		),
		array(
			'LEFT JOIN'		=> 'users AS u',
			'ON'			=> 'r.reported_by=u.id'
		),
		array(
			'LEFT JOIN'		=> 'users AS u2',
			'ON'			=> 'r.zapped_by=u2.id'
		)
	),
	'WHERE'		=> 'r.zapped IS NOT NULL',
	'ORDER BY'	=> 'r.zapped DESC',
	'LIMIT'		=> '10'
);

($hook = get_hook('arp_qr_get_last_zapped_reports')) ? eval($hook) : null;

$forum_page['old_reports'] = false;
$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

$zapped_reports = array();
while ($cur_report = $forum_db->fetch_assoc($result))
{
	$zapped_reports[] = $cur_report;
}

if (!empty($zapped_reports))
{
	$i = 1;
	$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['item_num'] = 0;
	$forum_page['old_reports'] = true;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo $lang_admin_reports['Read reports heading'] ?><?php echo (count($zapped_reports)) ? '' : ' '.$lang_admin_reports['No new reports'] ?></span></h2>
	</div>
	<div class="main-content main-frm">
<?php

	foreach ($zapped_reports as $cur_report)
	{
		$reporter = ($cur_report['reporter'] != '') ? '<a href="'.forum_link($forum_url['user'], $cur_report['reported_by']).'">'.forum_htmlencode($cur_report['reporter']).'</a>' : $lang_admin_reports['Deleted user'];
		$forum = ($cur_report['forum_name'] != '') ? '<a href="'.forum_link($forum_url['forum'], array($cur_report['forum_id'], sef_friendly($cur_report['forum_name']))).'">'.forum_htmlencode($cur_report['forum_name']).'</a>' : $lang_admin_reports['Deleted forum'];
		$topic = ($cur_report['subject'] != '') ? '<a href="'.forum_link($forum_url['topic'], array($cur_report['topic_id'], sef_friendly($cur_report['subject']))).'">'.forum_htmlencode($cur_report['subject']).'</a>' : $lang_admin_reports['Deleted topic'];
		$message = str_replace("\n", '<br />', forum_htmlencode($cur_report['message']));
		$post_id = ($cur_report['pid'] != '') ? '<a href="'.forum_link($forum_url['post'], $cur_report['pid']).'">'.sprintf($lang_admin_reports['Post'], $cur_report['pid']).'</a>' : $lang_admin_reports['Deleted post'];
		$zapped_by = ($cur_report['zapped_by'] != '') ? '<a href="'.forum_link($forum_url['user'], $cur_report['zapped_by_id']).'">'.forum_htmlencode($cur_report['zapped_by']).'</a>' : $lang_admin_reports['Deleted user'];

		($hook = get_hook('arp_report_pre_display')) ? eval($hook) : null;

?>
			<div class="ct-set report data-set set<?php echo ++$forum_page['item_count'] ?>">
				<div class="ct-box data-box">
					<h3 class="ct-legend hn"><strong><?php echo ++$forum_page['item_num'] ?></strong> <cite class="username"><?php printf($lang_admin_reports['Reported by'], $reporter) ?></cite> <span><?php echo format_time($cur_report['created']) ?></span></h3>
					<h4 class="hn"><?php echo $forum ?> &rarr; <?php echo $topic ?> &rarr; <?php echo $post_id ?></h4>
					<p><?php echo $message ?> <strong><?php printf($lang_admin_reports['Marked read by'], format_time($cur_report['zapped']), $zapped_by) ?></strong></p>
<?php ($hook = get_hook('arp_report_new_block')) ? eval($hook) : null; ?>
				</div>
			</div>
<?php

	}

?>
	</div>
<?php

}

if (!$forum_page['new_reports'] && !$forum_page['old_reports'])
{

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo $lang_admin_reports['Empty reports heading'] ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<div class="ct-box">
			<p><?php echo $lang_admin_reports['No reports'] ?></p>
		</div>
	</div>
<?php

}

// Init JS helper for select-all
$forum_loader->add_js('PUNBB.common.addDOMReadyEvent(PUNBB.common.initToggleCheckboxes);', array('type' => 'inline'));

($hook = get_hook('arp_end')) ? eval($hook) : null;
