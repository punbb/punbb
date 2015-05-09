<?php
namespace punbb;

($hook = get_hook('aus_search_form_output_start')) ? eval($hook) : null;

?>
	<div class="main-subhead">
		<h2 class="hn"><span><?php echo __('Search head', 'admin_users') ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<form class="frm-form" method="get" accept-charset="utf-8" action="<?php echo link('admin_users') ?>">
			<div class="hidden">
				<input type="hidden" name="csrf_token" value="<?php echo generate_form_token(link('admin_users').'?action=find_user') ?>" />
			</div>
			<div class="content-head">
				<h3 class="hn"><span><?php echo __('User search head', 'admin_users') ?></span></h3>
			</div>
<?php ($hook = get_hook('aus_search_form_pre_user_details_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo __('Searches personal legend', 'admin_users') ?></strong></legend>
<?php ($hook = get_hook('aus_search_form_pre_username')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Username label', 'admin_users') ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[username]" size="35" maxlength="25" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aus_search_form_pre_user_title')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Title label', 'admin_users') ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[title]" size="35" maxlength="50" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aus_search_form_pre_realname')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Real name label', 'admin_users') ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[realname]" size="35" maxlength="40" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aus_search_form_pre_location')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Location label', 'admin_users') ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[location]" size="35" maxlength="30" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aus_search_form_pre_signature')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Signature label', 'admin_users') ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[signature]" size="35" maxlength="512" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aus_search_form_pre_admin_note')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Admin note label', 'admin_users') ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[admin_note]" size="35" maxlength="30" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aus_search_form_pre_user_details_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('aus_search_form_user_details_fieldset_end')) ? eval($hook) : null; ?>
<?php $forum_page['item_count'] = 0; ?>
<?php ($hook = get_hook('aus_search_form_pre_user_contacts_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo __('Searches contact legend', 'admin_users') ?></strong></legend>
<?php ($hook = get_hook('aus_search_form_pre_email')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('E-mail address label', 'admin_users') ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[email]" size="35" maxlength="80" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aus_search_form_pre_website')) ? eval($hook) : null; ?>				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Website label', 'admin_users') ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[url]" size="35" maxlength="100" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aus_search_form_pre_jabber')) ? eval($hook) : null; ?>				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Jabber label', 'admin_users') ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[jabber]" size="35" maxlength="80" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aus_search_form_pre_icq')) ? eval($hook) : null; ?>				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('ICQ label', 'admin_users') ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[icq]" size="12" maxlength="12" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aus_search_form_pre_msn')) ? eval($hook) : null; ?>				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('MSN Messenger label', 'admin_users') ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[msn]" size="35" maxlength="80" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aus_search_form_pre_aim')) ? eval($hook) : null; ?>				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('AOL IM label', 'admin_users') ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[aim]" size="20" maxlength="20" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aus_search_form_pre_yahoo')) ? eval($hook) : null; ?>				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Yahoo Messenger label', 'admin_users') ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="form[yahoo]" size="20" maxlength="20" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aus_search_form_pre_user_contacts_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('aus_search_form_user_contacts_fieldset_end')) ? eval($hook) : null; ?>
<?php $forum_page['item_count'] = 0; ?>
<?php ($hook = get_hook('aus_search_form_pre_user_activity_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo __('Searches activity legend', 'admin_users') ?></strong></legend>
<?php ($hook = get_hook('aus_search_form_pre_min_posts')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box frm-short text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('More posts label', 'admin_users') ?></span> <small><?php echo __('Number of posts help', 'admin_users') ?></small></label><br />
						<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="posts_greater" size="5" maxlength="8" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aus_search_form_pre_max_posts')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box frm-short text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Less posts label', 'admin_users') ?></span> <small><?php echo __('Number of posts help', 'admin_users') ?></small></label><br />
						<span class="fld-input"><input type="number" id="fld<?php echo $forum_page['fld_count'] ?>" name="posts_less" size="5" maxlength="8" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aus_search_form_pre_last_post_after')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Last post after label', 'admin_users') ?></span> <small><?php echo __('Date format help', 'admin_users') ?></small></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="last_post_after" size="24" maxlength="19" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aus_search_form_pre_last_post_before')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Last post before label', 'admin_users') ?></span><small><?php echo __('Date format help', 'admin_users') ?></small></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="last_post_before" size="24" maxlength="19" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aus_search_form_pre_registered_after')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Registered after label', 'admin_users') ?></span> <small><?php echo __('Date format help', 'admin_users') ?></small></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="registered_after" size="24" maxlength="19" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aus_search_form_pre_registered_before')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Registered before label', 'admin_users') ?></span> <small><?php echo __('Date format help', 'admin_users') ?></small></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="registered_before" size="24" maxlength="19" /></span>
					</div>
				</div>
<?php ($hook = get_hook('aus_search_form_pre_user_activity_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php

($hook = get_hook('aus_search_form_user_activity_fieldset_end')) ? eval($hook) : null;

// Reset counter
$forum_page['group_count'] = $forum_page['item_count'] = 0;

?>
			<div class="content-head">
				<h3 class="hn"><span><?php echo __('User results head', 'admin_users') ?></span></h3>
			</div>
<?php ($hook = get_hook('aus_search_form_pre_results_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo __('User results legend', 'admin_users') ?></strong></legend>
<?php ($hook = get_hook('aus_search_form_pre_sort_by')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box select">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Order by label', 'admin_users') ?></span></label><br />
						<span class="fld-input"><select id="fld<?php echo $forum_page['fld_count'] ?>" name="order_by">
							<option value="username" selected="selected"><?php echo __('Username', 'admin_users') ?></option>
							<option value="email"><?php echo __('E-mail', 'admin_users') ?></option>
							<option value="num_posts"><?php echo __('Posts', 'admin_users') ?></option>
							<option value="last_post"><?php echo __('Last post', 'admin_users') ?></option>
							<option value="registered"><?php echo __('Registered', 'admin_users') ?></option>
<?php ($hook = get_hook('aus_search_form_new_sort_by_option')) ? eval($hook) : null; ?>
						</select></span>
					</div>
				</div>
<?php ($hook = get_hook('aus_search_form_pre_sort_order')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box select">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('Sort order label', 'admin_users') ?></span></label><br />
						<span class="fld-input"><select id="fld<?php echo $forum_page['fld_count'] ?>" name="direction">
							<option value="ASC" selected="selected"><?php echo __('Ascending', 'admin_users') ?></option>
							<option value="DESC"><?php echo __('Descending', 'admin_users') ?></option>
						</select></span>
					</div>
				</div>
<?php ($hook = get_hook('aus_search_form_pre_filter_group')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box select">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('User group label', 'admin_users') ?></span></label><br />
						<span class="fld-input"><select id="fld<?php echo $forum_page['fld_count'] ?>" name="user_group">
							<option value="-1" selected="selected"><?php echo __('All groups', 'admin_users') ?></option>
							<option value="<?php echo FORUM_UNVERIFIED ?>"><?php echo __('Unverified users', 'admin_users') ?></option>
<?php
while ($cur_group = db()->fetch_assoc($result))
	echo "\t\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'">'.forum_htmlencode($cur_group['g_title']).'</option>'."\n";

($hook = get_hook('aus_search_form_new_filter_group_option')) ? eval($hook) : null;

?>
						</select></span>
					</div>
				</div>
<?php ($hook = get_hook('aus_search_form_pre_results_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('aus_search_form_results_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="find_user" value="<?php echo __('Submit search', 'admin_users') ?>" /></span>
			</div>
		</form>
	</div>
<?php

// Reset counter
$forum_page['group_count'] = $forum_page['item_count'] = 0;

?>

	<div class="main-subhead">
		<h2 class="hn"><span><?php echo __('IP search head', 'admin_users') ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<form class="frm-form" method="get" accept-charset="utf-8" action="<?php echo link('admin_users') ?>">
<?php ($hook = get_hook('aus_search_form_pre_ip_search_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo __('IP search legend', 'admin_users') ?></strong></legend>
<?php ($hook = get_hook('aus_search_form_pre_ip_address')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo __('IP address label', 'admin_users') ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="show_users" size="18" maxlength="15" required /></span>
					</div>
				</div>
<?php ($hook = get_hook('aus_search_form_pre_ip_search_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('aus_search_form_ip_search_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" value=" <?php echo __('Submit search', 'admin_users') ?> " /></span>
			</div>
		</form>
	</div>
<?php

($hook = get_hook('aus_end')) ? eval($hook) : null;
