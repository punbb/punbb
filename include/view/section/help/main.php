<?php

($hook = get_hook('he_main_output_start')) ? eval($hook) : null;

?>
<div id="brd-main" class="main">

<div class="main-head">
	<h1 class="hn"><span><?php echo $lang_help['Help'] ?></span></h1>
</div>
<?php

if (!$section || $section == 'bbcode') {
	include view('section/help/bbcode');
}
else if ($section == 'img') {
	include view('section/help/img');
}
else if ($section == 'smilies') {
	include view('section/help/smilies');
}

($hook = get_hook('he_new_section')) ? eval($hook) : null;

?>

</div>
<?php

($hook = get_hook('he_end')) ? eval($hook) : null;
