<?php

($hook = get_hook('apr_prune_comply_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php printf(__('Prune details head', 'admin_prune'), ($forum == 'all forums') ? __('All forums', 'admin_prune') : $forum ) ?></span></h2>
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
				<p class="warn"><span><?php printf(__('Prune topics info 1', 'admin_prune'), $num_topics, isset($_POST['prune_sticky']) ? ' ('.__('Include sticky', 'admin_prune').')' : '') ?></span></p>
				<p class="warn"><span><?php printf(__('Prune topics info 2', 'admin_prune'), $prune_days) ?></span></p>
			</div>
<?php ($hook = get_hook('apr_prune_comply_pre_buttons')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="prune_comply" value="<?php echo __('Prune topics', 'admin_prune') ?>" /></span>
			</div>
		</form>
	</div>
<?php

($hook = get_hook('apr_prune_comply_end')) ? eval($hook) : null;
