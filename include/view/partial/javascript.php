<?php
namespace punbb;

$forum_javascript_commonjs_urls = '
	if (typeof PUNBB === \'undefined\' || !PUNBB) {
		var PUNBB = {};
	}

	PUNBB.env = {
		base_url: "'.forum_htmlencode($base_url).'/",
		base_js_url: "'.forum_htmlencode($base_url).'/include/js/",
		user_lang: "'.forum_htmlencode($forum_user['language']).'",
		user_style: "'.forum_htmlencode($forum_user['style']).'",
		user_is_guest: "'.forum_htmlencode(($forum_user['is_guest'] == 1) ? "1" : "0").'",
		page: "'.forum_htmlencode((defined("FORUM_PAGE")) ? FORUM_PAGE : "unknown" ).'"
	};';


$forum_loader->add_js($forum_javascript_commonjs_urls, array('type' => 'inline', 'weight' => 50, 'group' => FORUM_JS_GROUP_SYSTEM));
$forum_loader->add_js($base_url.'/include/js/min/punbb.common.min.js', array('weight' => 55, 'async' => false, 'group' => FORUM_JS_GROUP_SYSTEM));

($hook = get_hook('ft_js_include')) ? eval($hook) : null;

echo $forum_loader->render_js();