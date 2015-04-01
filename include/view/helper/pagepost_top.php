<?php

global $forum_page;

return (!empty($forum_page['page_post'])) ?
	'<div id="brd-pagepost-top" class="main-pagepost gen-content">'."\n\t".
		implode("\n\t", $forum_page['page_post'])."\n".'</div>' : '';
