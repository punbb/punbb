
<div id="smilies" class="main-subhead">
	<h2 class="hn"><span><?php printf($lang_help['Help with'], $lang_common['Smilies']) ?></span></h2>
</div>

<div class="main-content main-frm">
	<div class="ct-box help-box">
		<p class="hn"><?php echo $lang_help['Smilies info'] ?></p>
		<div class="entry-content">
<?php

// Display the smiley set
if (!defined('FORUM_PARSER_LOADED'))
	require FORUM_ROOT.'include/parser.php';

$smiley_groups = array();

($hook = get_hook('he_pre_smile_display')) ? eval($hook) : null;

foreach ($smilies as $smiley_text => $smiley_img)
	$smiley_groups[$smiley_img][] = $smiley_text;

foreach ($smiley_groups as $smiley_img => $smiley_texts)
	echo "\t\t\t\t".'<p>'.implode(' '.$lang_common['and'].' ', $smiley_texts).' <span>'.$lang_help['produces'].'</span> <img src="'.$base_url.'/img/smilies/'.$smiley_img.'" width="15" height="15" alt="'.$smiley_texts[0].'" /></p>'."\n";

?>
		</div>
	</div>
</div>