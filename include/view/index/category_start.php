<?php
namespace punbb;

$item_header = array();
$item_header['subject']['title'] = '<strong class="subject-title">' . __('Forums', 'index') . '</strong>';
$item_header['info']['topics'] = '<strong class="info-topics">' . __('topics', 'index') . '</strong>';
$item_header['info']['post'] = '<strong class="info-posts">' . __('posts', 'index') . '</strong>';
$item_header['info']['lastpost'] = '<strong class="info-lastpost">' . __('last post', 'index') . '</strong>';

($hook = get_hook('in_forum_pre_cat_head')) ? eval($hook) : null;

$cur_category = $cur_forum['cid'];

?>

<div class="main-head">
	<h2 class="hn"><span><?= forum_htmlencode($cur_forum['cat_name']) ?></span></h2>
</div>
<div class="main-subhead">
	<p class="item-summary"><span><?= printf(__('Category subtitle', 'index'),
		implode(' ', $item_header['subject']),
		implode(', ', $item_header['info'])) ?></span></p>
</div>
<div id="category<?= $cat_count ?>" class="main-content main-category">
