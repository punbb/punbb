<?php

($hook = get_hook('arp_main_output_start')) ? eval($hook) : null;

if (!empty($unread_reports))
{
	$forum_page['new_reports'] = true;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo __('New reports heading', 'admin_reports') ?></span></h2>
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
		$reporter = ($cur_report['reporter'] != '') ? '<a href="'.forum_link($forum_url['user'], $cur_report['reported_by']).'">'.forum_htmlencode($cur_report['reporter']).'</a>' : __('Deleted user', 'admin_reports');
		$forum = ($cur_report['forum_name'] != '') ? '<a href="'.forum_link($forum_url['forum'], array($cur_report['forum_id'], sef_friendly($cur_report['forum_name']))).'">'.forum_htmlencode($cur_report['forum_name']).'</a>' : __('Deleted forum', 'admin_reports');
		$topic = ($cur_report['subject'] != '') ? '<a href="'.forum_link($forum_url['topic'], array($cur_report['topic_id'], sef_friendly($cur_report['subject']))).'">'.forum_htmlencode($cur_report['subject']).'</a>' : __('Deleted topic', 'admin_reports');
		$message = str_replace("\n", '<br />', forum_htmlencode($cur_report['message']));
		$post_id = ($cur_report['pid'] != '') ? '<a href="'.forum_link($forum_url['post'], $cur_report['pid']).'">'.sprintf(__('Post', 'admin_reports'), $cur_report['pid']).'</a>' : __('Deleted post', 'admin_reports');

		($hook = get_hook('arp_new_report_pre_display')) ? eval($hook) : null;

?>
			<div class="ct-set warn-set report set<?php echo ++$forum_page['item_count'] ?>">
				<div class="ct-box warn-box">
					<h3 class="ct-legend hn"><strong><?php echo ++$forum_page['item_num'] ?></strong> <cite class="username"><?php printf(__('Reported by', 'admin_reports'), $reporter) ?></cite> <span><?php echo format_time($cur_report['created']) ?></span></h3>
					<h4 class="hn"><?php echo $forum ?> &rarr; <?php echo $topic ?> &rarr; <?php echo $post_id ?></h4>
					<p><?php echo $message ?></p>
					<p class="item-select"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="reports[<?php echo $cur_report['id'] ?>]" value="1" /> <label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo __('Select report', 'admin_reports') ?></label></p>
<?php ($hook = get_hook('arp_new_report_new_block')) ? eval($hook) : null; ?>
				</div>
			</div>
<?php

	}

?>
			<div class="frm-buttons">
				<span class="select-all js_link" data-check-form="arp-new-report-form"><?php echo __('Select all', 'admin_common') ?></span>
				<span class="submit primary"><input type="submit" name="mark_as_read" value="<?php echo __('Mark read', 'admin_reports') ?>" /></span>
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
		<h2 class="hn"><span><?php echo __('Read reports heading', 'admin_reports') ?><?php echo (count($zapped_reports)) ? '' : ' '.__('No new reports', 'admin_reports') ?></span></h2>
	</div>
	<div class="main-content main-frm">
<?php

	foreach ($zapped_reports as $cur_report)
	{
		$reporter = ($cur_report['reporter'] != '') ? '<a href="'.forum_link($forum_url['user'], $cur_report['reported_by']).'">'.forum_htmlencode($cur_report['reporter']).'</a>' : __('Deleted user', 'admin_reports');
		$forum = ($cur_report['forum_name'] != '') ? '<a href="'.forum_link($forum_url['forum'], array($cur_report['forum_id'], sef_friendly($cur_report['forum_name']))).'">'.forum_htmlencode($cur_report['forum_name']).'</a>' : __('Deleted forum', 'admin_reports');
		$topic = ($cur_report['subject'] != '') ? '<a href="'.forum_link($forum_url['topic'], array($cur_report['topic_id'], sef_friendly($cur_report['subject']))).'">'.forum_htmlencode($cur_report['subject']).'</a>' : __('Deleted topic', 'admin_reports');
		$message = str_replace("\n", '<br />', forum_htmlencode($cur_report['message']));
		$post_id = ($cur_report['pid'] != '') ? '<a href="'.forum_link($forum_url['post'], $cur_report['pid']).'">'.sprintf(__('Post', 'admin_reports'), $cur_report['pid']).'</a>' : __('Deleted post', 'admin_reports');
		$zapped_by = ($cur_report['zapped_by'] != '') ? '<a href="'.forum_link($forum_url['user'], $cur_report['zapped_by_id']).'">'.forum_htmlencode($cur_report['zapped_by']).'</a>' : __('Deleted user', 'admin_reports');

		($hook = get_hook('arp_report_pre_display')) ? eval($hook) : null;

?>
			<div class="ct-set report data-set set<?php echo ++$forum_page['item_count'] ?>">
				<div class="ct-box data-box">
					<h3 class="ct-legend hn"><strong><?php echo ++$forum_page['item_num'] ?></strong> <cite class="username"><?php printf(__('Reported by', 'admin_reports'), $reporter) ?></cite> <span><?php echo format_time($cur_report['created']) ?></span></h3>
					<h4 class="hn"><?php echo $forum ?> &rarr; <?php echo $topic ?> &rarr; <?php echo $post_id ?></h4>
					<p><?php echo $message ?> <strong><?php printf(__('Marked read by', 'admin_reports'), format_time($cur_report['zapped']), $zapped_by) ?></strong></p>
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
		<h2 class="hn"><span><?php echo __('Empty reports heading', 'admin_reports') ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<div class="ct-box">
			<p><?php echo __('No reports', 'admin_reports') ?></p>
		</div>
	</div>
<?php

}

// Init JS helper for select-all
$forum_loader->add_js('PUNBB.common.addDOMReadyEvent(PUNBB.common.initToggleCheckboxes);', array('type' => 'inline'));

($hook = get_hook('arp_end')) ? eval($hook) : null;
