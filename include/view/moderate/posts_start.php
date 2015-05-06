<?php
namespace punbb;
?>

<div class="main-head">
	<?php
		if (!empty($forum_page['main_head_options'])) {
			echo "\n\t\t".'<p class="options">'.implode(' ', $forum_page['main_head_options']).'</p>';
		}
	?>
	<h2 class="hn"><span><?php echo $forum_page['items_info'] ?></span></h2>
</div>

<form id="mr-post-actions-form" class="newform" method="post" accept-charset="utf-8" action="<?= $form_action ?>">
	<div class="main-content main-topic">
		<div class="hidden">
			<input type="hidden" name="csrf_token" value="<?= generate_form_token($form_action) ?>" />
		</div>
