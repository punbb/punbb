<?php
namespace punbb;

($hook = get_hook('rg_rules_output_start')) ? eval($hook) : null;

	$forum_page['set_count'] = $forum_page['fld_count'] = 0;

?>
	<div class="main-head">
		<h2 class="hn"><span><?= sprintf(__('Register at', 'profile'), $forum_config['o_board_title']) ?></span></h2>
	</div>
	<div class="main-subhead">
		<h2 class="hn"><span><?= __('Reg rules head', 'profile') ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<div id="rules-content" class="ct-box user-box">
			<?php echo $forum_config['o_rules_message'] ?>
		</div>
		<form class="frm-form" method="get" accept-charset="utf-8" action="<?php echo forum_link($forum_url['register']) ?>">
<?php ($hook = get_hook('rg_rules_pre_group')) ? eval($hook) : null; ?>
			<div class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
<?php ($hook = get_hook('rg_rules_pre_agree_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="req_agreement" value="1" required /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?= __('Agreement', 'profile') ?></span> <?= __('Agreement label', 'profile') ?></label>
					</div>
				</div>
<?php ($hook = get_hook('rg_rules_pre_group_end')) ? eval($hook) : null; ?>
			</div>
<?php ($hook = get_hook('rg_rules_group_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="agree" value="<?= __('Agree', 'profile') ?>" /></span>
				<span class="cancel"><input type="submit" name="cancel" value="<?= __('Cancel') ?>" formnovalidate /></span>
			</div>
		</form>
	</div>
<?php

($hook = get_hook('rg_rules_end')) ? eval($hook) : null;
