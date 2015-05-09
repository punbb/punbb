<?php
namespace punbb;

if (!empty($errors)) { ?>
	<div class="ct-box error-box">
		<h2 class="warn hn"><?= $errors_title ?></h2>
		<ul class="error-list">
			<?php foreach ($errors as $v) { ?>
				<li class="warn">
					<span><?= $v ?></span>
				</li>
			<?php } ?>
		</ul>
	</div>
<?php } ?>