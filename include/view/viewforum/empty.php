<?php
$forum_page['item_body']['subject']['title'] = '<h3 class="hn">'.$lang_forum['No topics'].'</h3>';
$forum_page['item_body']['subject']['desc'] = '<p>'.$lang_forum['First topic nag'].'</p>';

($hook = get_hook('vf_no_results_row_pre_display')) ? eval($hook) : null;

?>
<div class="main-head">
<?php
	if (!empty($forum_page['main_head_options']))
		echo "\n\t\t".'<p class="options">'.implode(' ', $forum_page['main_head_options']).'</p>';
?>
	<h2 class="hn"><span><?php echo $lang_forum['Empty forum'] ?></span></h2>
</div>
<div id="forum<?php echo $id ?>" class="main-content main-forum">
	<div class="main-item empty main-first-item">
		<span class="icon empty"><!-- --></span>
		<div class="item-subject">
			<?php echo implode("\n\t\t\t\t", $forum_page['item_body']['subject'])."\n" ?>
		</div>
	</div>
</div>
<div class="main-foot">
	<h2 class="hn"><span><?php echo $lang_forum['Empty forum'] ?></span></h2>
</div>
