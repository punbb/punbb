<?php

($hook = get_hook('aus_delete_users_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo __('Confirm delete', 'admin_users') ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<div class="ct-box warn-box">
			<p class="warn"><?php echo __('Delete warning', 'admin_users') ?></p>
		</div>
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo forum_link($forum_url['admin_users']) ?>?action=modify_users">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token(forum_link($forum_url['admin_users']).'?action=modify_users') ?>" />
				<input type="hidden" name="users" value="<?php echo implode(',', $users) ?>" />
			</div>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><span><?php echo __('Delete posts legend', 'admin_users') ?></span></legend>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="delete_posts" value="1" checked="checked" /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo __('Delete posts', 'admin_users') ?></span> <?php echo __('Delete posts label', 'admin_users') ?></label>
					</div>
				</div>
			</fieldset>
			<div class="frm-buttons">
				<span class="submit primary caution"><input type="submit" name="delete_users_comply" value="<?php echo __('Delete users', 'admin_users') ?>" /></span>
				<span class="cancel"><input type="submit" name="delete_users_cancel" value="<?php echo $lang_admin_common['Cancel'] ?>" /></span>
			</div>
		</form>
	</div>
<?php

($hook = get_hook('aus_delete_users_end')) ? eval($hook) : null;
