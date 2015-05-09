<?php
namespace punbb;

($hook = get_hook('pf_view_details_output_start')) ? eval($hook) : null;

?>
	<div class="main-head">
		<h2 class="hn"><span><?php printf((user()->id == $id) ?
			__('Profile welcome', 'profile') : __('Profile welcome user', 'profile'), forum_htmlencode($user['username'])) ?></span></h2>
	</div>
	<div class="main-content main-frm">
<?php ($hook = get_hook('pf_view_details_pre_user_info')) ? eval($hook) : null; ?>
		<div class="profile ct-group data-group vcard">
<?php ($hook = get_hook('pf_view_details_pre_user_ident_info')) ? eval($hook) : null; ?>
			<div class="ct-set data-set set<?php echo ++$forum_page['item_count'] ?>">
				<div class="ct-box data-box">
					<ul class="user-ident ct-legend">
						<?php echo implode("\n\t\t\t\t\t\t", $forum_page['user_ident']) ?>
					</ul>
					<ul class="data-list">
						<?php echo implode("\n\t\t\t\t\t\t", $forum_page['user_info'])."\n" ?>
					</ul>
				</div>
			</div>
<?php ($hook = get_hook('pf_view_details_pre_user_contact_info')) ? eval($hook) : null; ?>
<?php if (!empty($forum_page['user_contact'])): ?>
			<div class="ct-set data-set set<?php echo ++$forum_page['item_count'] ?>">
				<div class="ct-box data-box">
					<h3 class="ct-legend hn"><span><?= __('Contact info', 'profile') ?></span></h3>
					<ul class="data-list">
						<?php echo implode("\n\t\t\t\t\t\t", $forum_page['user_contact'])."\n" ?>
					</ul>
				</div>
			</div>
<?php endif; ($hook = get_hook('pf_view_details_pre_user_activity_info')) ? eval($hook) : null; ?>
<?php if (!empty($forum_page['user_activity'])): ?>
			<div class="ct-set data-set set<?php echo ++$forum_page['item_count'] ?>">
				<div class="ct-box data-box">
					<h3 class="ct-legend hn"><span><?= __('Posts and topics', 'profile') ?></span></h3>
					<ul class="data-box">
						<?php echo implode("\n\t\t\t\t\t\t", $forum_page['user_activity']) ?>
					</ul>
				</div>
			</div>
<?php endif; ($hook = get_hook('pf_view_details_pre_user_sig_info')) ? eval($hook) : null; ?>
<?php if (isset($forum_page['sig_demo'])): ?>
			<div class="ct-set data-set set<?php echo ++$forum_page['item_count'] ?>">
				<div class="ct-box data-box">
					<h3 class="ct-legend hn"><span><?= __('Current signature', 'profile') ?></span></h3>
					<div class="sig-demo"><?php echo $forum_page['sig_demo']."\n" ?></div>
				</div>
			</div>
<?php endif; ?>
		</div>
<?php ($hook = get_hook('pf_view_details_user_info_end')) ? eval($hook) : null; ?>
	</div>
<?php

($hook = get_hook('pf_view_details_end')) ? eval($hook) : null;
