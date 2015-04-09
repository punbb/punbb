<?php
namespace punbb;

global $forum_page;

if (!empty($forum_page['main_menu'])) { ?>
	<div class="main-menu gen-content">
		<ul>
			<?php foreach ($forum_page['main_menu'] as $v) { ?>
				<?= $v ?>
			<?php } ?>
		</ul>
	</div>
<?php } ?>
