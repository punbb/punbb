<?php
namespace punbb;

($hook = get_hook('acg_del_cat_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php printf(__('Confirm delete cat', 'admin_categories'), forum_htmlencode($cat_name)) ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<div class="ct-box warn-box">
			<p class="warn"><?= __('Delete category warning', 'admin_categories') ?></p>
		</div>
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?= $form_action ?>">
			<div class="hidden">
				<?= implode("\n", $hidden_fields) ?>
			</div>
			<div class="frm-buttons">
				<span class="submit primary caution"><input type="submit" name="del_cat_comply" value="<?= __('Delete category', 'admin_categories') ?>" /></span>
				<span class="cancel"><input type="submit" name="del_cat_cancel" value="<?= __('Cancel', 'admin_categories') ?>" /></span>
			</div>
		</form>
	</div>
<?php

($hook = get_hook('acg_del_cat_end')) ? eval($hook) : null;
