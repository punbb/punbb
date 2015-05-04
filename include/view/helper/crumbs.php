<?php
namespace punbb;

global $forum_page;

$return = ($hook = get_hook('fn_generate_crumbs_start')) ? eval($hook) : null;
if ($return != null) {
	return $return;
}

if (empty($crumbs)) {
	$crumbs[0] = config()->o_board_title;
}

$crumbs = '';
$num_crumbs = count($crumbs);

if ($reverse) {
	for ($i = ($num_crumbs - 1); $i >= 0; --$i) {
		$crumbs .= (is_array($crumbs[$i])?
			forum_htmlencode($crumbs[$i][0]) :
			forum_htmlencode($crumbs[$i])) .
			((isset($forum_page['page']) && $i == ($num_crumbs - 1))?
				' (' .	__('Page') . ' ' . forum_number_format($forum_page['page']) . ')' : '') .
			($i > 0 ? __('Title separator') : '');
	}
}
else {
	for ($i = 0; $i < $num_crumbs; ++$i) {
		if ($i < ($num_crumbs - 1)) {
			$crumbs .= '<span class="crumb'.(($i == 0)?
				' crumbfirst' : '') . '">' . (($i >= 1)?
					'<span>' . __('Crumb separator') . '</span>' : '') .
				(is_array($crumbs[$i])?
					'<a href="' . $crumbs[$i][1] . '">' .
					forum_htmlencode($crumbs[$i][0]) . '</a>' :
					forum_htmlencode($crumbs[$i])) . '</span> ';
		}
		else {
			$crumbs .= '<span class="crumb crumblast'.(($i == 0)?
				' crumbfirst' : '') . '">' . (($i >= 1)?
				'<span>' .	__('Crumb separator') . '</span>' : '') .
				(is_array($crumbs[$i])?
					'<a href="' . $crumbs[$i][1] . '">' .
					forum_htmlencode($crumbs[$i][0]) . '</a>' :
					forum_htmlencode($crumbs[$i])) . '</span> ';
		}
	}
}

($hook = get_hook('fn_generate_crumbs_end')) ? eval($hook) : null;

echo $crumbs;
