<?php
namespace punbb;
?>

<div class="main-head">
<?php
	if (!empty($main_head_options)) {
		echo "\n\t\t".'<p class="options">'.implode(' ', $main_head_options).'</p>';
	}
?>
	<h2 class="hn"><span><?php echo $forum_page['items_info'] ?></span></h2>
</div>

<form id="mr-topic-actions-form" method="post" accept-charset="utf-8" action="<?= $form_action ?>">
	<div class="main-subhead">
		<p class="item-summary<?php echo (config()->o_topic_views == '1') ? ' forum-views' : ' forum-noview' ?>"><span><?php printf(__('Forum subtitle', 'forum'), implode(' ', $forum_page['item_header']['subject']), implode(', ', $forum_page['item_header']['info'])) ?></span></p>
	</div>
	<div id="forum<?php echo $fid ?>" class="main-content main-forum<?php echo (config()->o_topic_views == '1') ? ' forum-views' : ' forum-noview' ?>">
		<div class="hidden">
			<input type="hidden" name="csrf_token" value="<?= generate_form_token($form_action) ?>" />
		</div>