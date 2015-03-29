<?php

($hook = get_hook('afo_del_forum_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php printf($lang_admin_forums['Confirm delete forum'], forum_htmlencode($forum_name)) ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo forum_link($forum_url['admin_forums']) ?>?del_forum=<?php echo $forum_to_delete ?>">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token(forum_link($forum_url['admin_forums']).'?del_forum='.$forum_to_delete) ?>" />
			</div>
			<div class="ct-box warn-box">
				<p class="warn"><?php echo $lang_admin_forums['Delete forum warning'] ?></p>
			</div>
			<div class="frm-buttons">
				<span class="submit primary caution"><input type="submit" name="del_forum_comply" value="<?php echo $lang_admin_forums['Delete forum'] ?>" /></span>
				<span class="cancel"><input type="submit" name="del_forum_cancel" value="<?php echo $lang_admin_common['Cancel'] ?>" /></span>
			</div>
		</form>
	</div>

<?php

($hook = get_hook('afo_del_forum_end')) ? eval($hook) : null;
