<?php
namespace punbb;

global $forum_url;

$return = ($hook = get_hook('fn_generate_navlinks_start')) ? eval($hook) : null;
if ($return != null) {
	return $return;
}

echo '<ul>';

// Index should always be displayed
$links['index'] = '<li id="navindex"'.((FORUM_PAGE == 'index') ? ' class="isactive"' : '').'><a href="'.forum_link($forum_url['index']).'">'.
	__('Index') . '</a></li>';

if (user()->g_read_board == '1' && user()->g_view_users == '1')
	$links['userlist'] = '<li id="navuserlist"'.((FORUM_PAGE == 'userlist') ? ' class="isactive"' : '').'><a href="'.forum_link($forum_url['users']).'">'.
	__('User list') . '</a></li>';

if (config()->o_rules == '1' && (!user()->is_guest || user()->g_read_board == '1' ||
		config()->o_regs_allow == '1'))
	$links['rules'] = '<li id="navrules"'.((FORUM_PAGE == 'rules') ? ' class="isactive"' : '').'><a href="'.forum_link($forum_url['rules']).'">'.
	__('Rules') . '</a></li>';

if (user()->is_guest) {
	if (user()->g_read_board == '1' && user()->g_search == '1')
		$links['search'] = '<li id="navsearch"'.((FORUM_PAGE == 'search') ? ' class="isactive"' : '').'><a href="'.forum_link($forum_url['search']).'">'.
		__('Search') . '</a></li>';

	$links['register'] = '<li id="navregister"'.((FORUM_PAGE == 'register') ? ' class="isactive"' : '').'><a href="'.forum_link($forum_url['register']).'">'.
		__('Register') . '</a></li>';
	$links['login'] = '<li id="navlogin"'.((FORUM_PAGE == 'login') ? ' class="isactive"' : '').'><a href="'.forum_link($forum_url['login']).'">'.
		__('Login') . '</a></li>';
}
else {
	if (!user()->is_admmod) {
		if (user()->g_read_board == '1' && user()->g_search == '1')
			$links['search'] = '<li id="navsearch"'.((FORUM_PAGE == 'search') ? ' class="isactive"' : '').'><a href="'.forum_link($forum_url['search']).'">'.
			__('Search') . '</a></li>';

		$links['profile'] = '<li id="navprofile"'.((substr(FORUM_PAGE, 0, 7) == 'profile') ? ' class="isactive"' : '').'><a href="'.
			forum_link($forum_url['user'], user()->id).'">'.
			__('Profile') . '</a></li>';
		$links['logout'] = '<li id="navlogout"><a href="'.
			forum_link($forum_url['logout'], array(user()->id, generate_form_token('logout'.user()->id))).'">'.
			__('Logout') . '</a></li>';
	}
	else {
		$links['search'] = '<li id="navsearch"'.((FORUM_PAGE == 'search') ? ' class="isactive"' : '').'><a href="'.forum_link($forum_url['search']).'">'.
			__('Search') . '</a></li>';
		$links['profile'] = '<li id="navprofile"'.((substr(FORUM_PAGE, 0, 7) == 'profile') ? ' class="isactive"' : '').'><a href="'.forum_link($forum_url['user'], user()->id).'">'.
			__('Profile') . '</a></li>';
		$links['admin'] = '<li id="navadmin"'.((substr(FORUM_PAGE, 0, 5) == 'admin') ? ' class="isactive"' : '').'><a href="'.forum_link($forum_url['admin_index']).'">'.
			__('Admin') . '</a></li>';
		$links['logout'] = '<li id="navlogout"><a href="'.forum_link($forum_url['logout'], array(user()->id, generate_form_token('logout'.user()->id))).'">'.
			__('Logout') . '</a></li>';
	}
}

// Are there any additional navlinks we should insert into the array before imploding it?
if (config()->o_additional_navlinks != '' &&
		preg_match_all('#([0-9]+)\s*=\s*(.*?)\n#s',
			config()->o_additional_navlinks."\n", $extra_links)) {
	// Insert any additional links into the $links array (at the correct index)
	$num_links = count($extra_links[1]);
	for ($i = 0; $i < $num_links; ++$i) {
		array_insert($links, (int)$extra_links[1][$i],
			'<li id="navextra' . ($i + 1) . '">' . $extra_links[2][$i] . '</li>');
	}
}

($hook = get_hook('fn_generate_navlinks_end')) ? eval($hook) : null;

echo implode("\n\t\t", $links);

echo '</ul>';
