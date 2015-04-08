<?php

($hook = get_hook('aop_maintenance_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo __('Maintenance head', 'admin_settings') ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo forum_link($forum_url['admin_settings_maintenance']) ?>">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token(forum_link($forum_url['admin_settings_maintenance'])) ?>" />
				<input type="hidden" name="form_sent" value="1" />
			</div>
			<div class="ct-box warn-box">
				<p class="important"><?php echo __('Maintenance mode info', 'admin_settings') ?></p>
				<p class="warn"><?php echo __('Maintenance mode warn', 'admin_settings') ?></p>
			</div>
<?php ($hook = get_hook('aop_maintenance_pre_maintenance_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo __('Maintenance legend', 'admin_settings') ?></strong></legend>
<?php ($hook = get_hook('aop_maintenance_pre_maintenance_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="form[maintenance]" value="1"<?php if ($forum_config['o_maintenance'] == '1') echo ' checked="checked"' ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo __('Maintenance mode label', 'admin_settings') ?></label>
					</div>
				</div>
<?php ($hook = get_hook('aop_maintenance_pre_maintenance_message')) ? eval($hook) : null; ?>
				<div class="txt-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="txt-box textarea">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Maintenance message label', 'admin_settings') ?></span><small><?php echo __('Maintenance message help', 'admin_settings') ?></small></label>
						<div class="txt-input"><span class="fld-input"><textarea id="fld<?php echo $forum_page['fld_count'] ?>" name="form[maintenance_message]" rows="5" cols="55"><?php echo forum_htmlencode($forum_config['o_maintenance_message']) ?></textarea></span></div>
					</div>
				</div>
<?php ($hook = get_hook('aop_maintenance_pre_maintenance_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('aop_maintenance_maintenance_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="save" value="<?php echo $lang_admin_common['Save changes'] ?>" /></span>
			</div>
		</form>
	</div>
