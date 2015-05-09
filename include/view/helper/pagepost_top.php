<?php
namespace punbb;

if (!empty($page_post)) { ?>
	<div id="brd-pagepost-top" class="main-pagepost gen-content">
		<?php foreach ($page_post as $v) { ?>
			<?= $v ?>
		<?php } ?>
	</div>
<?php } ?>
