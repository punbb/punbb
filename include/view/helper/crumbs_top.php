<?php

return (FORUM_PAGE != 'index') ?
	'<div id="brd-crumbs-top" class="crumbs">'."\n\t".'<p>'.
		generate_crumbs(false).'</p>'."\n".'</div>' : '';
