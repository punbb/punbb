<div id="brd-main" class="main basic">

	<div class="main-head">
		<h1 class="hn"><span><?php echo $message.$lang_common['Redirecting'] ?></span></h1>
	</div>

	<div class="main-content main-message">
		<p><?php printf($lang_common['Forwarding info'], $forum_config['o_redirect_delay'], intval($forum_config['o_redirect_delay']) == 1 ? $lang_common['second'] : $lang_common['seconds']) ?><span> <a href="<?php echo $destination_url ?>"><?php echo $lang_common['Click redirect'] ?></a></span></p>
	</div>

</div>