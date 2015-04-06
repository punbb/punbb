<?php

($hook = get_hook('li_login_output_start')) ? eval($hook) : null;

?>
	<div class="main-head">
		<h2 class="hn"><span><?php echo sprintf($lang_login['Login info'], $forum_config['o_board_title']) ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<div class="content-head">
			<p class="hn"><?php printf($lang_login['Login options'], '<a href="'.forum_link($forum_url['register']).'">'.$lang_login['register'].'</a>', '<a href="'.forum_link($forum_url['request_password']).'">'.$lang_login['Obtain pass'].'</a>') ?></p>
		</div>

		<?php include view('login/errors') ?>

		<div id="req-msg" class="req-warn ct-box error-box">
			<p class="important"><?= __('Required warn') ?></p>
		</div>
		<form id="afocus" class="frm-form" method="post" accept-charset="utf-8" action="<?php echo $forum_page['form_action'] ?>">
			<div class="hidden">
				<?php echo implode("\n\t\t\t\t", $forum_page['hidden_fields'])."\n" ?>
			</div>
<?php ($hook = get_hook('li_login_pre_login_group')) ? eval($hook) : null; ?>
			<div class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
<?php ($hook = get_hook('li_login_pre_username')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text required">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_login['Username'] ?></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="req_username" value="<?php if (isset($_POST['req_username'])) echo forum_htmlencode($_POST['req_username']); ?>" size="35" maxlength="25" required spellcheck="false" /></span>
					</div>
				</div>
<?php ($hook = get_hook('li_login_pre_pass')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text required">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_login['Password'] ?></span></label><br />
						<span class="fld-input"><input type="password" id="fld<?php echo $forum_page['fld_count'] ?>" name="req_password" value="<?php if (isset($_POST['req_password'])) echo forum_htmlencode($_POST['req_password']); ?>" size="35" required /></span>
					</div>
				</div>
<?php ($hook = get_hook('li_login_pre_remember_me_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="save_pass" value="1"<?php if (isset($_POST['save_pass'])) echo ' checked="checked"'; ?> /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo $lang_login['Remember me'] ?></label>
					</div>
				</div>
<?php ($hook = get_hook('li_login_pre_group_end')) ? eval($hook) : null; ?>
			</div>
<?php ($hook = get_hook('li_login_group_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit primary"><input type="submit" name="login" value="<?php echo $lang_login['Login'] ?>" /></span>
			</div>
		</form>
	</div>
<?php

($hook = get_hook('li_end')) ? eval($hook) : null;
