<?php
global $forum_user, $forum_url, $lang_common;

$visit_links = array();
if ($forum_user['g_read_board'] == '1' && $forum_user['g_search'] == '1') {
	if (!$forum_user['is_guest']) {
		$visit_links['newposts'] = '<span id="visit-new"'.(empty($visit_links) ?
			' class="first-item"' : '').'><a href="'.forum_link($forum_url['search_new']).
			'" title="'.$lang_common['New posts title'].'">'.
			$lang_common['New posts'].'</a></span>';
	}
	$visit_links['recent'] = '<span id="visit-recent"'.(empty($visit_links) ?
		' class="first-item"' : '').'><a href="'.forum_link($forum_url['search_recent']).
		'" title="'.$lang_common['Active topics title'].'">'.
		$lang_common['Active topics'].'</a></span>';
	$visit_links['unanswered'] = '<span id="visit-unanswered"'.(empty($visit_links) ?
		' class="first-item"' : '').'><a href="'.
		forum_link($forum_url['search_unanswered']).'" title="'.
		$lang_common['Unanswered topics title'].'">'.
		$lang_common['Unanswered topics'].'</a></span>';
}

if (!empty($visit_links)) { ?>
	<p id="visit-links" class="options">
		<?php foreach ($visit_links as $v) { ?>
			<?= $v ?>
		<?php } ?>
	</p>
<?php } ?>
