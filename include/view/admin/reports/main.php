<?php

($hook = get_hook('arp_main_output_start')) ? eval($hook) : null;

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
