<?php
// If there were any errors, show them
if (isset($forum_page['errors'])) { ?>
		<div class="ct-box error-box">
			<h2 class="warn hn"><span><?php echo $lang_post['Post errors'] ?></span></h2>
			<ul class="error-list">
				<?php echo implode("\n\t\t\t\t", $forum_page['errors'])."\n" ?>
			</ul>
		</div>
<?php } ?>
