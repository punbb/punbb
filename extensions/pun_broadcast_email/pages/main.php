<?php

/**
 * pun_broadcast_email page
 *
 * @copyright (C) 2009 PunBB
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package pun_broadcast_email
 */

require FORUM_ROOT.'header.php';

// START SUBST - <!-- forum_main -->
ob_start();

?>
<div class="main-subhead">
	<h2 class="hn">
		<span><?php echo $lang_pun_broadcast_email['Ext name']; ?></span>
	</h2>
</div>
<div class="main-content main-forum">
<?php if (!empty($forum_page['errors'])) : ?>
	<div class="ct-box error-box">
		<h2 class="warn hn"><?php echo $lang_pun_broadcast_email['Email errors'] ?></h2>
		<ul class="error-list">
			<?php foreach ($forum_page['errors'] as $cur_error) { ?>
				<li class="warn"><span><?php echo $cur_error; ?></span></li>
			<?php } ?>
		</ul>
	</div>
<?php endif; ?>
<?php if (empty($forum_page['errors']) && isset($_POST['preview'])): ?>
<div>
	<p><?php echo $forum_page['preview']['email_subject']; ?></p>
	<p><?php echo $forum_page['preview']['email_message']; ?></p>
</div>
<?php endif; ?>
	<?php if (count($forum_page['groups']) > 0): ?>
	<form class="frm-form" id="broadcast-email-form" method="post" accept-charset="utf-8" action="<?php echo $forum_page['form_action'] ?>">
		<div class="hidden">
			<?php foreach ($forum_page['hidden_fields'] as $field_name => $field_value) {?>
			<input type="hidden" name="<?php echo $field_name; ?>" value="<?php echo $field_value; ?>" />
			<?php } ?>
		</div>
		<div class="ct-group">
			<table cellspacing="0" summary="<?php echo $lang_pun_broadcast_email['Table summary'] ?>">
				<thead>
					<tr>
						<th class="tc0" scope="col"><?php echo $lang_pun_broadcast_email['Group'] ?></th>
						<th class="tc1" scope="col"><?php echo $lang_pun_broadcast_email['Group title'] ?> </th>
						<th class="tc2" scope="col"><?php echo $lang_pun_broadcast_email['Members count'] ?> </th>
						<th class="tc3" scope="col"></th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($forum_page['groups'] as $cur_group) { ?>
					<tr>
						<td class="tc0"><?php echo $cur_group['g_title']; ?></td>
						<td class="tc1"><?php echo $cur_group['g_user_title']; ?></td>
						<td class="tc2"><?php echo $cur_group['user_count']; ?></td>
						<td class="tc3"><input <?php echo in_array($cur_group['group_id'], $forum_page['selected_groups']) ? 'checked' : ''; ?> type="checkbox" value="<?php echo $cur_group['group_id']; ?>" name="groups[]" /></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
		<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
			<div class="sf-box checkbox">
				<span class="fld-input">
					<input id="fld<?php echo ++$forum_page['fld_count'] ?>" type="checkbox" <?php echo isset($forum_page['parse_mail']) && $forum_page['parse_mail'] ? 'checked="checked" ' : '';?>value="1" name="parse_mail" />
				</span>
				<label for="fld<?php echo $forum_page['fld_count'] ?>">
					<span><?php echo $lang_pun_broadcast_email['Tpl vars'] ?></span>
					<?php echo sprintf($lang_pun_broadcast_email['Tpl vars info'], forum_link($forum_url['pun_broadcast_email_help'])) ?>
				</label>
			</div>
		</div>
		<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
			<div class="sf-box text required">
				<label for="fld<?php echo ++$forum_page['fld_count'] ?>">
					<span>
						<?php echo $lang_pun_broadcast_email['Email subject'] ?>
						<em><?php echo $lang_common['Required'] ?></em>
					</span>
				</label>
				<br/>
				<span class="fld-input">
					<input id="fld<?php echo $forum_page['fld_count'] ?>" type="text" maxlength="70" size="70" name="req_subject" value="<?php echo $forum_page['email_subject'] ?>"/>
				</span>
			</div>
		</div>
		<div class="txt-set set<?php echo ++$forum_page['item_count'] ?>">
			<div class="txt-box textarea required">
				<label for="fld<?php echo ++$forum_page['fld_count'] ?>">
					<span>
						<?php echo $lang_pun_broadcast_email['Email message'] ?>
						<em><?php echo $lang_common['Required'] ?></em>
					</span>
				</label>
				<div class="txt-input">
					<span class="fld-input">
						<textarea id="fld<?php echo $forum_page['fld_count'] ?>" cols="95" rows="14" name="req_message"><?php echo $forum_page['email_message'] ?></textarea>
					</span>
				</div>
			</div>
		</div>
		<div class="frm-buttons">
			<span class="submit"><input type="submit" name="submit" value="<?php echo $lang_pun_broadcast_email['Submit'] ?>" /></span>
			<span class="submit"><input type="submit" name="preview" value="<?php echo $lang_pun_broadcast_email['Preview'] ?>" /></span>
		</div>
	</form>
	<?php endif; ?>
</div>
<?php

$tpl_temp = forum_trim(ob_get_contents());
$tpl_main = str_replace('<!-- forum_main -->', $tpl_temp, $tpl_main);
ob_end_clean();
// END SUBST - <!-- forum_main -->

require FORUM_ROOT.'footer.php';

?>