<?php
namespace punbb;

if (!empty($main_menu)) { ?>
	<div class="main-menu gen-content">
		<ul>
			<?php foreach ($main_menu as $v) { ?>
				<?= $v ?>
			<?php } ?>
		</ul>
	</div>
<?php } ?>
