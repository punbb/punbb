<?php

($hook = get_hook('ari_main_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo $lang_admin_reindex['Reindex heading'] ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<div class="ct-box warn-box">
			<p><?php echo $lang_admin_reindex['Reindex info'] ?></p>
			<p class="important"><?php echo $lang_admin_reindex['Reindex warning'] ?></p>
			<p class="warn"><?php echo $lang_admin_reindex['Empty index warning'] ?></p>
		</div>
		<form class="frm-form" method="get" accept-charset="utf-8" action="<?php echo forum_link($forum_url['admin_reindex']) ?>">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token('reindex'.$forum_user['id']) ?>" />
			</div>
<?php ($hook = get_hook('ari_pre_rebuild_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><span><?php echo $lang_admin_reindex['Rebuild index legend'] ?></span></legend>
<?php ($hook = get_hook('ari_pre_rebuild_per_page')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_admin_reindex['Posts per cycle'] ?></span> <small><?php echo $lang_admin_reindex['Posts per cycle info'] ?></small></label><br />
						<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="i_per_page" size="7" maxlength="7" value="100" /></span>
					</div>
				</div>
<?php ($hook = get_hook('ari_pre_rebuild_start_post')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span class="fld-label"><?php echo $lang_admin_reindex['Starting post'] ?></span> <small><?php echo $lang_admin_reindex['Starting post info'] ?></small></label><br />
						<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="i_start_at" size="7" maxlength="7" value="<?php echo (isset($first_id)) ? $first_id : 0 ?>" /></span>
					</div>
				</div>
<?php ($hook = get_hook('ari_pre_rebuild_empty_index')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="i_empty_index" value="1" checked="checked" /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo $lang_admin_reindex['Empty index'] ?></label>
					</div>
				</div>
<?php ($hook = get_hook('ari_pre_rebuild_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('ari_rebuild_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="rebuild_index" value="<?php echo $lang_admin_reindex['Rebuild index'] ?>" /></span>
			</div>
		</form>
	</div>
<?php

($hook = get_hook('ari_end')) ? eval($hook) : null;
