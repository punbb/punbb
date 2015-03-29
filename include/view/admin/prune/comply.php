<?php

($hook = get_hook('apr_prune_comply_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php printf($lang_admin_prune['Prune details head'], ($forum == 'all forums') ? $lang_admin_prune['All forums'] : $forum ) ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo forum_link($forum_url['admin_prune']) ?>?action=foo">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token(forum_link($forum_url['admin_prune']).'?action=foo') ?>" />
				<input type="hidden" name="prune_days" value="<?php echo $prune_days ?>" />
				<input type="hidden" name="prune_sticky" value="<?php echo intval($_POST['prune_sticky']) ?>" />
				<input type="hidden" name="prune_from" value="<?php echo $prune_from ?>" />
			</div>
			<div class="ct-box">
				<p class="warn"><span><?php printf($lang_admin_prune['Prune topics info 1'], $num_topics, isset($_POST['prune_sticky']) ? ' ('.$lang_admin_prune['Include sticky'].')' : '') ?></span></p>
				<p class="warn"><span><?php printf($lang_admin_prune['Prune topics info 2'], $prune_days) ?></span></p>
			</div>
<?php ($hook = get_hook('apr_prune_comply_pre_buttons')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="prune_comply" value="<?php echo $lang_admin_prune['Prune topics'] ?>" /></span>
			</div>
		</form>
	</div>
<?php

($hook = get_hook('apr_prune_comply_end')) ? eval($hook) : null;
