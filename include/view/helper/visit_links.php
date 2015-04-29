<?php
namespace punbb;

global $forum_url;

$visit_links = array();
if (user()->g_read_board == '1' && user()->g_search == '1') {
	if (!user()->is_guest) {
		$visit_links['newposts'] = '<span id="visit-new"'.(empty($visit_links) ?
			' class="first-item"' : '').'><a href="'.link('search_new').
			'" title="' . __('New posts title') . '">'.
			__('New posts') . '</a></span>';
	}
	$visit_links['recent'] = '<span id="visit-recent"'.(empty($visit_links) ?
		' class="first-item"' : '').'><a href="'.link('search_recent').
		'" title="' . __('Active topics title') .  '">' .
		__('Active topics') . '</a></span>';
	$visit_links['unanswered'] = '<span id="visit-unanswered"'.(empty($visit_links) ?
		' class="first-item"' : '').'><a href="'.
		link('search_unanswered').'" title="'.
		__('Unanswered topics title') . '">' .
		__('Unanswered topics') . '</a></span>';
}

if (!empty($visit_links)) { ?>
	<p id="visit-links" class="options">
		<?php foreach ($visit_links as $v) { ?>
			<?= $v ?>
		<?php } ?>
	</p>
<?php } ?>
