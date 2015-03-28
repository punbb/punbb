<?php

($hook = get_hook('mi_rules_output_start')) ? eval($hook) : null;

?>
	<div class="main-head">
		<h2 class="hn"><span><?php echo $lang_common['Rules'] ?></span></h2>
	</div>

	<div class="main-content main-frm">
		<div id="rules-content" class="ct-box user-box">
			<?php echo $forum_config['o_rules_message']."\n" ?>
		</div>
	</div>
<?php

($hook = get_hook('mi_rules_end')) ? eval($hook) : null;
