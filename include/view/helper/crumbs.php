<?php
namespace punbb;

if (empty($crumbs)) {
	$crumbs[0] = config()->o_board_title;
}

$result = '';
$num_crumbs = count($crumbs);

if ($reverse) {
	for ($i = ($num_crumbs - 1); $i >= 0; --$i) {
		$result .= (is_array($crumbs[$i])?
			forum_htmlencode($crumbs[$i][0]) :
			forum_htmlencode($crumbs[$i])) .
			((isset($page) && $i == ($num_crumbs - 1))?
				' (' .	__('Page') . ' ' . forum_number_format($page) . ')' : '') .
			($i > 0 ? __('Title separator') : '');
	}
}
else {
	for ($i = 0; $i < $num_crumbs; ++$i) {
		if ($i < ($num_crumbs - 1)) {
			$result .= '<span class="crumb'.(($i == 0)?
				' crumbfirst' : '') . '">' . (($i >= 1)?
					'<span>' . __('Crumb separator') . '</span>' : '') .
				(is_array($crumbs[$i])?
					'<a href="' . $crumbs[$i][1] . '">' .
					forum_htmlencode($crumbs[$i][0]) . '</a>' :
					forum_htmlencode($crumbs[$i])) . '</span> ';
		}
		else {
			$result .= '<span class="crumb crumblast'.(($i == 0)?
				' crumbfirst' : '') . '">' . (($i >= 1)?
				'<span>' .	__('Crumb separator') . '</span>' : '') .
				(is_array($crumbs[$i])?
					'<a href="' . $crumbs[$i][1] . '">' .
					forum_htmlencode($crumbs[$i][0]) . '</a>' :
					forum_htmlencode($crumbs[$i])) . '</span> ';
		}
	}
}

echo $result;
