
<div id="smilies" class="main-subhead">
	<h2 class="hn"><span><?php printf(__('Help with', 'help'), __('Smilies')) ?></span></h2>
</div>

<div class="main-content main-frm">
	<div class="ct-box help-box">
		<p class="hn"><?= __('Smilies info', 'help') ?></p>
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
	echo "\t\t\t\t".'<p>'.implode(' '.__('and').' ', $smiley_texts).' <span>'.
		__('produces', 'help') . '</span> <img src="'.$base_url.'/img/smilies/'.$smiley_img.'" width="15" height="15" alt="'.$smiley_texts[0].'" /></p>'."\n";

?>
		</div>
	</div>
</div>