<?php
namespace punbb;

$cur_category = $cat_count = $item_count = 0;

while ($cur_forum = db()->fetch_assoc($result)) {
	$item_count++;

	if ($cur_forum['cid'] != $cur_category)	{
		// A new category since last iteration?
		if ($cur_category != 0) {
			include template()->view('index/category_end');
		}
		$cat_count++;
		$item_count = 1;

		include template()->view('index/category_start');
	}

	include template()->view('index/topic');
}

// Did we output any categories and forums?
if ($cur_category > 0) {
	include template()->view('index/category_end');
}
else {
	include template()->view('index/empty');
}
