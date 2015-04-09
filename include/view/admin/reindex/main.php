<?php
namespace punbb;

($hook = get_hook('ari_main_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo __('Reindex heading', 'admin_reindex') ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<div class="ct-box warn-box">
			<p><?php echo __('Reindex info', 'admin_reindex') ?></p>
			<p class="important"><?php echo __('Reindex warning', 'admin_reindex') ?></p>
			<p class="warn"><?php echo __('Empty index warning', 'admin_reindex') ?></p>
		</div>
		<form class="frm-form" method="get" accept-charset="utf-8" action="<?php echo forum_link($forum_url['admin_reindex']) ?>">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token('reindex'.$forum_user['id']) ?>" />
			</div>
<?php ($hook = get_hook('ari_pre_rebuild_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><span><?php echo __('Rebuild index legend', 'admin_reindex') ?></span></legend>
<?php ($hook = get_hook('ari_pre_rebuild_per_page')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Posts per cycle', 'admin_reindex') ?></span> <small><?php echo __('Posts per cycle info', 'admin_reindex') ?></small></label><br />
						<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="i_per_page" size="7" maxlength="7" value="100" /></span>
					</div>
				</div>
<?php ($hook = get_hook('ari_pre_rebuild_start_post')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span class="fld-label"><?php echo __('Starting post', 'admin_reindex') ?></span> <small><?php echo __('Starting post info', 'admin_reindex') ?></small></label><br />
						<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="i_start_at" size="7" maxlength="7" value="<?php echo (isset($first_id)) ? $first_id : 0 ?>" /></span>
					</div>
				</div>
<?php ($hook = get_hook('ari_pre_rebuild_empty_index')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="i_empty_index" value="1" checked="checked" /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo __('Empty index', 'admin_reindex') ?></label>
					</div>
				</div>
<?php ($hook = get_hook('ari_pre_rebuild_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('ari_rebuild_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="rebuild_index" value="<?php echo __('Rebuild index', 'admin_reindex') ?>" /></span>
			</div>
		</form>
	</div>
<?php

($hook = get_hook('ari_end')) ? eval($hook) : null;
