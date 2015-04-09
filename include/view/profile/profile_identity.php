<?php
namespace punbb;

($hook = get_hook('pf_change_details_identity_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php printf(($forum_page['own_profile']) ?
			__('Identity welcome', 'profile') :
			__('Identity welcome user', 'profile'), forum_htmlencode($user['username'])) ?></span></h2>
	</div>
	<div class="main-content main-frm">

		<?php helper('errors', array(
			'errors_title' => __('Profile update errors', 'profile')
		)) ?>

<?php
if ($forum_page['has_required']): ?>
		<div id="req-msg" class="req-warn ct-box error-box">
			<p class="important"><?= __('Required warn') ?></p>
		</div>
<?php endif; ?>
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo $forum_page['form_action'] ?>">
			<div class="hidden">
				<?php echo implode("\n\t\t\t\t", $forum_page['hidden_fields'])."\n" ?>
			</div>
<?php if ($forum_page['has_required']): ($hook = get_hook('pf_change_details_identity_pre_req_info_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?= __('Required information') ?></strong></legend>
<?php ($hook = get_hook('pf_change_details_identity_pre_username')) ? eval($hook) : null; ?>
<?php if ($forum_user['is_admmod'] && ($forum_user['g_id'] == FORUM_ADMIN || $forum_user['g_mod_rename_users'] == '1')): ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text required">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Username', 'profile') ?></span> <small><?= __('Username help', 'profile') ?></small></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="req_username" value="<?php echo(isset($_POST['req_username']) ? forum_htmlencode($_POST['req_username']) : forum_htmlencode($user['username'])) ?>" size="35" maxlength="25" required /></span>
					</div>
				</div>
<?php endif; ($hook = get_hook('pf_change_details_identity_pre_email')) ? eval($hook) : null; ?>
<?php if ($forum_user['is_admmod']): ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text required">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('E-mail', 'profile') ?></span> <small><?= __('E-mail help', 'profile') ?></small></label><br />
						<span class="fld-input"><input type="email" id="fld<?php echo $forum_page['fld_count'] ?>" name="req_email" value="<?php echo(isset($_POST['req_email']) ? forum_htmlencode($_POST['req_email']) : forum_htmlencode($user['email'])) ?>" size="35" maxlength="80" required /></span>
					</div>
				</div>
<?php endif; ($hook = get_hook('pf_change_details_identity_pre_req_info_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('pf_change_details_identity_req_info_fieldset_end')) ? eval($hook) : null; ?>
<?php endif; ($hook = get_hook('pf_change_details_identity_pre_personal_fieldset')) ? eval($hook) : null; ?><?php $forum_page['item_count'] = 0; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?= __('Personal legend', 'profile') ?></strong></legend>
<?php ($hook = get_hook('pf_change_details_identity_pre_realname')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Realname', 'profile') ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[realname]" value="<?php echo(isset($form['realname']) ? forum_htmlencode($form['realname']) : forum_htmlencode($user['realname'])) ?>" size="35" maxlength="40" /></span>
					</div>
				</div>
<?php ($hook = get_hook('pf_change_details_identity_pre_title')) ? eval($hook) : null; ?>
<?php if ($forum_user['g_set_title'] == '1'): ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Title', 'profile') ?></span><small><?= __('Leave blank', 'profile') ?></small></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="title" value="<?php echo(isset($_POST['title']) ? forum_htmlencode($_POST['title']) : forum_htmlencode($user['title'])) ?>" size="35" maxlength="50" /></span><br />
					</div>
				</div>
<?php endif; ?>
<?php ($hook = get_hook('pf_change_details_identity_pre_location')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Location', 'profile') ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[location]" value="<?php echo((isset($form['location']) ? forum_htmlencode($form['location']) : forum_htmlencode($user['location']))) ?>" size="35" maxlength="30" /></span>
					</div>
				</div>
<?php ($hook = get_hook('pf_change_details_identity_pre_admin_note')) ? eval($hook) : null; ?>
<?php if ($forum_user['is_admmod']): ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Admin note', 'profile') ?></span></label><br />
						<span class="fld-input"><input id="fld<?php echo $forum_page['fld_count'] ?>" type="text" name="admin_note" value="<?php echo(isset($_POST['admin_note']) ? forum_htmlencode($_POST['admin_note']) : forum_htmlencode($user['admin_note'])) ?>" size="35" maxlength="30" /></span>
					</div>
				</div>
<?php endif; ($hook = get_hook('pf_change_details_identity_pre_num_posts')) ? eval($hook) : null; ?>
<?php if ($forum_user['g_id'] == FORUM_ADMIN): ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Edit count', 'profile') ?></span></label><br />
						<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="num_posts" value="<?php echo $user['num_posts'] ?>" size="8" maxlength="8" /></span>
					</div>
				</div>
<?php endif; ($hook = get_hook('pf_change_details_identity_pre_personal_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('pf_change_details_identity_personal_fieldset_end')) ? eval($hook) : null; ?><?php $forum_page['item_count'] = 0; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?= __('Contact legend', 'profile') ?></strong></legend>
<?php ($hook = get_hook('pf_change_details_identity_pre_url')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Website', 'profile') ?></span></label><br />
						<span class="fld-input"><input type="url" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[url]" value="<?php echo(isset($form['url']) ? forum_htmlencode($form['url']) : forum_htmlencode($user['url'])) ?>" size="35" maxlength="80" /></span>
					</div>
				</div>
<?php ($hook = get_hook('pf_change_details_identity_pre_facebook')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Facebook', 'profile') ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[facebook]" placeholder="<?= __('Name or Url', 'profile') ?>" value="<?php echo(isset($form['facebook']) ? forum_htmlencode($form['facebook']) : forum_htmlencode($user['facebook'])) ?>" size="35" maxlength="80" /></span>
					</div>
				</div>
<?php ($hook = get_hook('pf_change_details_identity_pre_twitter')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Twitter', 'profile') ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[twitter]" placeholder="<?= __('Name or Url', 'profile') ?>" value="<?php echo(isset($form['twitter']) ? forum_htmlencode($form['twitter']) : forum_htmlencode($user['twitter'])) ?>" size="35" maxlength="80" /></span>
					</div>
				</div>
<?php ($hook = get_hook('pf_change_details_identity_pre_linkedin')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('LinkedIn', 'profile') ?></span></label><br />
						<span class="fld-input"><input type="url" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[linkedin]" value="<?php echo(isset($form['linkedin']) ? forum_htmlencode($form['linkedin']) : forum_htmlencode($user['linkedin'])) ?>" size="35" maxlength="80" /></span>
					</div>
				</div>
			</fieldset>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?= __('Contact messengers legend', 'profile') ?></strong></legend>
<?php ($hook = get_hook('pf_change_details_identity_pre_jabber')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Jabber', 'profile') ?></span></label><br />
						<span class="fld-input"><input id="fld<?php echo $forum_page['fld_count'] ?>" type="email" name="form[jabber]" value="<?php echo(isset($form['jabber']) ? forum_htmlencode($form['jabber']) : forum_htmlencode($user['jabber'])) ?>" size="35" maxlength="80" /></span>
					</div>
				</div>
<?php ($hook = get_hook('pf_change_details_identity_pre_skype')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Skype', 'profile') ?></span></label><br />
						<span class="fld-input"><input id="fld<?php echo $forum_page['fld_count'] ?>" type="text" name="form[skype]" value="<?php echo(isset($form['skype']) ? forum_htmlencode($form['skype']) : forum_htmlencode($user['skype'])) ?>" size="35" maxlength="80" /></span>
					</div>
				</div>
<?php ($hook = get_hook('pf_change_details_identity_pre_msn')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('MSN', 'profile') ?></span></label><br />
						<span class="fld-input"><input id="fld<?php echo $forum_page['fld_count'] ?>" type="text" name="form[msn]" value="<?php echo(isset($form['msn']) ? forum_htmlencode($form['msn']) : forum_htmlencode($user['msn'])) ?>" size="35" maxlength="80" /></span>
					</div>
				</div>
<?php ($hook = get_hook('pf_change_details_identity_pre_icq')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('ICQ', 'profile') ?></span></label><br />
						<span class="fld-input"><input id="fld<?php echo $forum_page['fld_count'] ?>" type="text" name="form[icq]" value="<?php echo(isset($form['icq']) ? forum_htmlencode($form['icq']) : $user['icq']) ?>" size="20" maxlength="12" /></span>
					</div>
				</div>
<?php ($hook = get_hook('pf_change_details_identity_pre_aim')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('AOL IM', 'profile') ?></span></label><br />
						<span class="fld-input"><input id="fld<?php echo $forum_page['fld_count'] ?>" type="text" name="form[aim]" value="<?php echo(isset($form['aim']) ? forum_htmlencode($form['aim']) : forum_htmlencode($user['aim'])) ?>" size="20" maxlength="30" /></span>
					</div>
				</div>
<?php ($hook = get_hook('pf_change_details_identity_pre_yahoo')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?= __('Yahoo', 'profile') ?></span></label><br />
						<span class="fld-input"><input id="fld<?php echo $forum_page['fld_count'] ?>" type="text" name="form[yahoo]" value="<?php echo(isset($form['yahoo']) ? forum_htmlencode($form['yahoo']) : forum_htmlencode($user['yahoo'])) ?>" size="20" maxlength="30" /></span>
					</div>
				</div>
<?php ($hook = get_hook('pf_change_details_identity_pre_contact_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('pf_change_details_identity_contact_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="update" value="<?= __('Update profile', 'profile') ?>" /></span>
			</div>
		</form>
	</div>
<?php

($hook = get_hook('pf_change_details_identity_end')) ? eval($hook) : null;
