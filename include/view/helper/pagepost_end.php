<?php
namespace punbb;

global $forum_page;

if (!empty($forum_page['page_post'])) { ?>
	<div id="brd-pagepost-end" class="main-pagepost gen-content">
		<?php foreach ($forum_page['page_post'] as $v) { ?>
			<?= $v ?>
		<?php } ?>
	</div>
<?php } ?>
