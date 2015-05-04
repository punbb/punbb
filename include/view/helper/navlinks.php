<?php
namespace punbb;

$config = config();
$user = user();
?>

<ul>
	<li id="navindex" <?= FORUM_PAGE == 'index'? ' class="isactive"' : '' ?>>
		<a href="<?= link('index') ?>"><?= __('Index') ?></a>
	</li>

	<?php if ($user->g_read_board == '1' && $user->g_view_users == '1') { ?>
		<li id="navuserlist" <?= FORUM_PAGE == 'userlist'? ' class="isactive"' : '' ?>>
			<a href="<?= link('users') ?>"><?= __('User list') ?></a>
		</li>
	<?php } ?>

	<?php if ($config->o_rules == '1' &&
			(!$user->is_guest || $user->g_read_board == '1' ||
			$config->o_regs_allow == '1')) { ?>
		<li id="navrules" <?= FORUM_PAGE == 'rules'? ' class="isactive"' : '' ?>>
			<a href="<?= link('rules') ?>"><?= __('Rules') ?></a>
		</li>
	<?php } ?>

	<?php if ($user->is_guest) { ?>
		<?php if ($user->g_read_board == '1' && $user->g_search == '1') { ?>
			<li id="navsearch" <?= FORUM_PAGE == 'search'? ' class="isactive"' : '' ?>>
				<a href="<?= link('search') ?>"><?= __('Search') ?></a>
			</li>
		<?php } ?>
		<li id="navregister" <?= FORUM_PAGE == 'register'? ' class="isactive"' : '' ?>>
			<a href="<?= link('register') ?>"><?=	__('Register') ?></a>
		</li>
		<li id="navlogin" <?= FORUM_PAGE == 'login'? ' class="isactive"' : ''?>>
			<a href="<?= link('login') ?>"><?= __('Login') ?></a>
		</li>

	<?php } else { ?>

		<?php if (!$user->is_admmod) {
		if ($user->g_read_board == '1' && $user->g_search == '1')
			$links['search'] = '<li id="navsearch"'.((FORUM_PAGE == 'search') ? ' class="isactive"' : '').'><a href="'.link('search').'">'.
			__('Search') . '</a></li>';

		$links['profile'] = '<li id="navprofile"'.((substr(FORUM_PAGE, 0, 7) == 'profile') ? ' class="isactive"' : '').'><a href="'.
			link('user', $user->id).'">'.
			__('Profile') . '</a></li>';
		$links['logout'] = '<li id="navlogout"><a href="'.
			link('logout', array($user->id, generate_form_token('logout'.$user->id))).'">'.
			__('Logout') . '</a></li>';
	}
	else {
		$links['search'] = '<li id="navsearch"'.((FORUM_PAGE == 'search') ? ' class="isactive"' : '').'><a href="'.link('search').'">'.
			__('Search') . '</a></li>';
		$links['profile'] = '<li id="navprofile"'.((substr(FORUM_PAGE, 0, 7) == 'profile') ? ' class="isactive"' : '').'><a href="'.link('user', $user->id).'">'.
			__('Profile') . '</a></li>';
		$links['admin'] = '<li id="navadmin"'.((substr(FORUM_PAGE, 0, 5) == 'admin') ? ' class="isactive"' : '').'><a href="'.link('admin_index').'">'.
			__('Admin') . '</a></li>';
		$links['logout'] = '<li id="navlogout"><a href="'.link('logout', array($user->id, generate_form_token('logout'.$user->id))).'">'.
			__('Logout') . '</a></li>';
	}
}

// Are there any additional navlinks we should insert into the array before imploding it?
if ($config->o_additional_navlinks != '' &&
		preg_match_all('#([0-9]+)\s*=\s*(.*?)\n#s',
			$config->o_additional_navlinks."\n", $extra_links)) {
	// Insert any additional links into the $links array (at the correct index)
	$num_links = count($extra_links[1]);
	for ($i = 0; $i < $num_links; ++$i) {
		array_insert($links, (int)$extra_links[1][$i],
			'<li id="navextra' . ($i + 1) . '">' . $extra_links[2][$i] . '</li>');
	}
}

echo implode("\n\t\t", $links);
?>

</ul>
