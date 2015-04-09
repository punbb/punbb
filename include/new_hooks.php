<?php
namespace punbb;

if (!function_exists('init_new_style_hooks')) {

function init_new_style_hooks() {
	if (defined('FORUM_DISABLE_HOOKS') || !defined('FORUM_NEW_HOOKS_STYLE')) {
		return;
	}

	// include init.php for all extensions
	foreach (glob(FORUM_ROOT . 'extensions/*') as $f) {
		if (!is_dir($f)) {
			continue;
		}
		$f .= '/init.php';
		if (file_exists($f)) {
			require $f;
		}
	}

	global $forum_new_hooks;
	$forum_new_hooks = array();
	$cache_fname = FORUM_ROOT . '/cache/new_hooks.php';

	// load hooks table from cache
	if (file_exists($cache_fname)) {
		$forum_new_hooks = include $cache_fname;
	}
	if (!empty($forum_new_hooks)) {
		return;
	}

	// generate hooks table cache for new hooks
	$functions = get_defined_functions();
	$prefix = 'punbb_hook_';
	foreach ($functions['user'] as $fn) {
		$l = strlen($prefix);
		if (substr($fn, 0, $l) == $prefix) {
			$p = strpos($fn, '__');
			if ($p !== false) {
				$forum_new_hooks[substr($fn, $l, $p - $l)][] = $fn;
			}
		}
	}

	file_put_contents($cache_fname,
		'<?php' . PHP_EOL . PHP_EOL .
			'return ' . var_export($forum_new_hooks, true) . ';');
}

init_new_style_hooks();

}
