<?php

($hook = get_hook('pf_change_details_about_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php printf(($forum_user['id'] == $id) ? $lang_profile['Profile welcome'] : $lang_profile['Profile welcome user'], forum_htmlencode($user['username'])) ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<p class="content-options options"><?php echo implode(' ', $forum_page['user_options']) ?></p>
<?php ($hook = get_hook('pf_change_details_about_pre_user_info')) ? eval($hook) : null; ?>
		<div class="profile ct-group data-group vcard">
<?php ($hook = get_hook('pf_change_details_about_pre_user_ident_info')) ? eval($hook) : null; ?>
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
<?php ($hook = get_hook('pf_change_details_about_pre_user_contact_info')) ? eval($hook) : null; ?>
<?php if (!empty($forum_page['user_contact'])): ?>
			<div class="ct-set data-set set<?php echo ++$forum_page['item_count'] ?>">
				<div class="ct-box data-box">
					<h4 class="ct-legend hn"><span><?php echo $lang_profile['Contact info'] ?></span></h4>
					<ul class="data-box">
						<?php echo implode("\n\t\t\t\t\t\t", $forum_page['user_contact'])."\n" ?>
					</ul>
				</div>
			</div>
<?php ($hook = get_hook('pf_change_details_about_pre_user_activity_info')) ? eval($hook) : null; ?>
<?php endif; if (!empty($forum_page['user_activity'])): ?>
			<div class="ct-set data-set set<?php echo ++$forum_page['item_count'] ?>">
				<div class="ct-box data-box">
					<h4 class="ct-legend hn"><span><?php echo $lang_profile['Posts and topics'] ?></span></h4>
					<ul class="data-box">
						<?php echo implode("\n\t\t\t\t\t\t", $forum_page['user_activity']) ?>
					</ul>
				</div>
			</div>
<?php ($hook = get_hook('pf_change_details_about_pre_user_sig_info')) ? eval($hook) : null; ?>
<?php endif; if (isset($forum_page['sig_demo'])): ?>
			<div class="ct-set data-set set<?php echo ++$forum_page['item_count'] ?>">
				<div class="ct-box data-box">
					<h4 class="ct-legend hn"><span><?php echo $lang_profile['Current signature'] ?></span></h4>
					<div class="sig-demo"><?php echo $forum_page['sig_demo'] ?></div>
				</div>
			</div>
<?php endif; ?>
<?php ($hook = get_hook('pf_change_details_about_pre_user_private_info')) ? eval($hook) : null; ?>
<?php if (!empty($forum_page['user_private'])): ?>
			<div id="private-profile" class="ct-set data-set set<?php echo ++$forum_page['item_count'] ?>">
				<div class="ct-box data-box">
					<h3 class="ct-legend hn"><span><?php echo $lang_profile['Private info'] ?></span></h3>
					<ul class="data-list">
						<?php echo implode("\n\t\t\t\t\t\t", $forum_page['user_private'])."\n" ?>
					</ul>
				</div>
			</div>
<?php endif; ?>
		</div>
<?php ($hook = get_hook('pf_change_details_about_user_info_end')) ? eval($hook) : null; ?>
	</div>
<?php

($hook = get_hook('pf_change_details_about_end')) ? eval($hook) : null;
