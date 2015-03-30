<div class="main-head">
	<h2 class="hn"><span><?php echo forum_htmlencode($cur_forum['cat_name']) ?></span></h2>
</div>
<div class="main-subhead">
	<p class="item-summary"><span><?php printf($lang_index['Category subtitle'], implode(' ', $forum_page['item_header']['subject']), implode(', ', $forum_page['item_header']['info'])) ?></span></p>
</div>
<div id="category<?php echo $forum_page['cat_count'] ?>" class="main-content main-category">
