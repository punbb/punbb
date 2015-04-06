<?php
// If there were any errors, show them
if (!empty($errors)) {
	$forum_page['errors'] = array();
	foreach ($errors as $cur_error) {
		$forum_page['errors'][] = '<li class="warn"><span>'.$cur_error.'</span></li>';
	}

	($hook = get_hook('mi_pre_email_errors')) ? eval($hook) : null;

?>
	<div class="ct-box error-box">
		<h2 class="warn hn"><?= __('Form e-mail errors', 'misc') ?></h2>
		<ul class="error-list">
			<?php echo implode("\n\t\t\t\t", $forum_page['errors'])."\n" ?>
		</ul>
	</div>
<?php } ?>
111