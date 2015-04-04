<?php

($hook = get_hook('pf_change_details_avatar_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php printf(($forum_page['own_profile']) ? $lang_profile['Avatar welcome'] : $lang_profile['Avatar welcome user'], forum_htmlencode($user['username'])) ?></span></h2>
	</div>
	<div class="main-content main-frm">

		<?= helper('errors', array(
			'errors_title' => $lang_profile['Profile update errors']
		)) ?>

		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo $forum_page['form_action'] ?>" enctype="multipart/form-data">
			<div class="hidden">
				<?php echo implode("\n\t\t\t\t", $forum_page['hidden_fields'])."\n" ?>
			</div>
<?php ($hook = get_hook('pf_change_details_avatar_pre_fieldset')) ? eval($hook) : null; ?>
			<div class="ct-box info-box">
				<ul class="info-list">
					<?php echo implode("\n\t\t\t\t\t", $forum_page['frm_info'])."\n" ?>
				</ul>
			</div>
			<div id="req-msg" class="req-warn ct-box info-box">
				<p class="important"><?php echo $lang_profile['No upload warn'] ?></p>
			</div>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo $lang_profile['Avatar'] ?></strong></legend>
<?php ($hook = get_hook('pf_change_details_avatar_pre_cur_avatar_info')) ? eval($hook) : null; ?>
				<div class="ct-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="ct-box">
						<h3 class="hn ct-legend"><?php echo $lang_profile['Current avatar'] ?></h3>
<?php if (isset($forum_page['avatar_demo'])): ?>
						<p class="avatar-demo"><span><?php echo $forum_page['avatar_demo'] ?></span></p>
<?php endif; ?>
						<p><?php echo (isset($forum_page['avatar_demo'])) ? '<a href="'.forum_link($forum_url['delete_avatar'], array($id, generate_form_token('delete_avatar'.$id.$forum_user['id']))).'">'.$lang_profile['Delete avatar info'].'</a>' : $lang_profile['No avatar info'] ?></p>
					</div>
				</div>
<?php ($hook = get_hook('pf_change_details_avatar_pre_avatar_upload')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text required">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_profile['Upload avatar file'] ?></span><small><?php echo $lang_profile['Avatar upload help'] ?></small></label><br />
						<span class="fld-input"><input id="fld<?php echo $forum_page['fld_count'] ?>" name="req_file" type="file" size="40" /></span>
					</div>
				</div>
<?php ($hook = get_hook('pf_change_details_avatar_pre_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('pf_change_details_avatar_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="update" value="<?php echo $lang_profile['Update profile'] ?>" /></span>
			</div>
		</form>
	</div>
<?php

($hook = get_hook('pf_change_details_avatar_end')) ? eval($hook) : null;
