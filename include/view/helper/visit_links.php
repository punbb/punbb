<?php
namespace punbb;

?>

<?php if (user()->g_read_board == '1' && user()->g_search == '1') { ?>
	<p id="visit-links" class="options">
		<?php if (!user()->is_guest) { ?>
			<span id="visit-new"><a href="<?= link('search_new') ?>"
				title="<?= __('New posts title') ?>"><?= __('New posts') ?></a></span>
		<?php } ?>
		<span id="visit-recent"><a href="<?= link('search_recent') ?>"
			title="<?= __('Active topics title') ?>"><?= __('Active topics') ?></a></span>
		<span id="visit-unanswered"><a href="<?= link('search_unanswered') ?>"
			title="<?= __('Unanswered topics title') ?>"><?= __('Unanswered topics') ?></a></span>
	</p>
<?php } ?>
