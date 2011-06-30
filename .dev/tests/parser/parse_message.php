<?php

class parse_message_Test extends PHPUnit_TestCase {

	// [i]
	public function test_parse_message_i() {
		$errors = array();

		$result = '<p><em>In vino veritas — Ёжик тумане (封鎖進階設定)</em></p>';
		$src = '[i]In vino veritas — Ёжик тумане (封鎖進階設定)[/i]';

		$this->assertEquals($result, parse_message(preparse_bbcode(forum_trim($src), $errors), $errors));
	}

	// [b]
	public function test_parse_message_b() {
		$errors = array();

		$result = '<p><strong>In vino veritas — Ёжик тумане (封鎖進階設定)</strong></p>';
		$src = '[b]In vino veritas — Ёжик тумане (封鎖進階設定)[/b]';

		$this->assertEquals($result, parse_message(preparse_bbcode(forum_trim($src), $errors), $errors));
	}

	// [h]
	public function test_parse_message_h() {
		$errors = array();

		$result = '<h5>In vino veritas — Ёжик тумане (封鎖進階設定)</h5>';
		$src = '[h]In vino veritas — Ёжик тумане (封鎖進階設定)[/h]';

		$this->assertEquals($result, parse_message(preparse_bbcode(forum_trim($src), $errors), $errors));
	}

	// [u]
	public function test_parse_message_u() {
		$errors = array();

		$result = '<p><span class="bbu">In vino veritas — Ёжик тумане (封鎖進階設定)</span></p>';
		$src = '[u]In vino veritas — Ёжик тумане (封鎖進階設定)[/u]';

		$this->assertEquals($result, parse_message(preparse_bbcode(forum_trim($src), $errors), $errors));
	}

	// [color]
	public function test_parse_message_color_hex() {
		$errors = array();

		$result = '<p><span style="color: #ff0000">In vino veritas — Ёжик тумане (封鎖進階設定)</span></p>';
		$src = '[color=#ff0000]In vino veritas — Ёжик тумане (封鎖進階設定)[/color]';

		$this->assertEquals($result, parse_message(preparse_bbcode(forum_trim($src), $errors), $errors));
	}

	// [color]
	public function test_parse_message_color_name() {
		$errors = array();

		$result = '<p><span style="color: red">In vino veritas — Ёжик тумане (封鎖進階設定)</span></p>';
		$src = '[color=red]In vino veritas — Ёжик тумане (封鎖進階設定)[/color]';

		$this->assertEquals($result, parse_message(preparse_bbcode(forum_trim($src), $errors), $errors));
	}

	// combine simple tags
	public function test_parse_message_combine_simple_tags() {
		$errors = array();

		$result = '<p><em><span class="bbu"><strong><span style="color: red">In vino veritas — Ёжик тумане (封鎖進階設定)</span></strong></span></em></p>';
		$src = '[i][u][b][color=red]In vino veritas — Ёжик тумане (封鎖進階設定)[/color][/b][/u][/i]';

		$this->assertEquals($result, parse_message(preparse_bbcode(forum_trim($src), $errors), $errors));
	}


	// urls
	public function test_parse_message_url_1() {
		$errors = array();

		$result = '<p><a href="http://localhost/punbb13/">Форум</a></p>';
		$src = '[url=http://localhost/punbb13/]Форум[/url]';

		$this->assertEquals($result, parse_message(preparse_bbcode(forum_trim($src), $errors), $errors));
	}

	// urls
	public function test_parse_message_url_2() {
		$errors = array();

		$result = '<p><a href="http://localhost/punbb13/">http://localhost/punbb13/</a></p>';
		$src = '[url]http://localhost/punbb13/[/url]';

		$this->assertEquals($result, parse_message(preparse_bbcode(forum_trim($src), $errors), $errors));
	}

	// urls email
	public function test_parse_message_url_mailto_1() {
		$errors = array();

		$result = '<p><a href="mailto:name@example.com">name@example.com</a></p>';
		$src = '[email]name@example.com[/email]';

		$this->assertEquals($result, parse_message(preparse_bbcode(forum_trim($src), $errors), $errors));
	}

	// urls email
	public function test_parse_message_url_mailto_2() {
		$errors = array();

		$result = '<p><a href="mailto:name@example.com">My e-mail address (Мой элетроящик)</a></p>';
		$src = '[email=name@example.com]My e-mail address (Мой элетроящик)[/email]';

		$this->assertEquals($result, parse_message(preparse_bbcode(forum_trim($src), $errors), $errors));
	}


	// quoting
	public function test_parse_message_quote_1() {
		$errors = array();

		$result = '<div class="quotebox"><cite>James wrote:</cite><blockquote><p>This is the text I want to quote.</p></blockquote></div>';
		$src = '[quote=James]This is the text I want to quote.[/quote]';

		$this->assertEquals($result, parse_message(preparse_bbcode(forum_trim($src), $errors), $errors));
	}

	// quoting
	public function test_parse_message_quote_2() {
		$errors = array();

		$result = '<div class="quotebox"><blockquote><p>This is the text I want to quote.</p></blockquote></div>';
		$src = '[quote]This is the text I want to quote.[/quote]';

		$this->assertEquals($result, parse_message(preparse_bbcode(forum_trim($src), $errors), $errors));
	}

	// quoting
	public function test_parse_message_quote_3() {
		$errors = array();

		$result = '<div class="quotebox"><cite>Певица &#91;] Ёлка wrote:</cite><blockquote><p>This is the text I want to quote.</p></blockquote></div>';
		$src = '[quote="Певица [] Ёлка"]This is the text I want to quote.[/quote]';

		$this->assertEquals($result, parse_message(preparse_bbcode(forum_trim($src), $errors), $errors));
	}

	// quoting
	public function test_parse_message_quote_4() {
		$errors = array();

		$result = '<div class="quotebox"><cite>Певица &#91;] Ёлка wrote:</cite><blockquote><p>This is the text I want to quote.</p></blockquote></div>';
		$src = "[quote='Певица [] Ёлка']This is the text I want to quote.[/quote]";

		$this->assertEquals($result, parse_message(preparse_bbcode(forum_trim($src), $errors), $errors));
	}


	// Code
	public function test_parse_message_code_1() {
		$errors = array();

		$result = '<div class="codebox"><pre><code>This is some code.</code></pre></div>';
		$src = '[code]This is some code.[/code]';

		$this->assertEquals($result, parse_message(preparse_bbcode(forum_trim($src), $errors), $errors));
	}

	// Code
	public function test_parse_message_code_long() {
		$errors = array();

		$result = '<div class="codebox"><pre><code>This is a long piece of code. [i]This is a long piece of code.[/i] [url=&quot;http://yandex.ru/&quot;]This is a long piece of code[/url]. This is a long piece of code. This is a long piece of code.</code></pre></div>';
		$src = '[code]This is a long piece of code. [i]This is a long piece of code.[/i] [url="http://yandex.ru/"]This is a long piece of code[/url]. This is a long piece of code. This is a long piece of code.[/code]';

		$this->assertEquals($result, parse_message(preparse_bbcode(forum_trim($src), $errors), $errors));
	}

	// Code
	public function test_parse_message_code_quote() {
		$errors = array();

		$result = '<div class="codebox"><pre><code>This is a long &#039;piece&#039; of code</code></pre></div>';
		$src = "[code]This is a long 'piece' of code[/code]";

		$this->assertEquals($result, parse_message(preparse_bbcode(forum_trim($src), $errors), $errors));
	}



	// Big message with a many elements
	public function test_parse_message_large_1() {
		$errors = array();

		$result = '<p>PunBB 1.3.5 is released</p><p><strong>1.3.4 to 1.3.5 changes</strong><br /></p><ul><li><p>a lot of bugs fixed (CSS &amp; markup, added missing lang entries on language files, correct path and alerts on install, fixed typos and more);</p></li><li><p>hidden guest email;</p></li><li><p>increased visit timeout;</p></li><li><p>deleting non-activated users on registering;</p></li><li><p>correct deprecated function calls;</p></li><li><p>added paginal navigation on pages admin users and bans, added validation timezone and report length, added hooks;</p></li></ul><p><strong>How to upgrade</strong><br /></p><ul><li><p>make a backup of the database and files;</p></li><li><p>turn the Maintenance mode on (via admin panel);</p></li><li><p>overwrite old files with new ones;</p></li><li><p>after owerwriting, verify that the cache, img/avatars and extensions (for pun_repository) directories have enough write permissions (usually 777);</p></li><li><p>clear cache directory;</p></li><li><p>go to the forum index and run db_update.php script;</p></li><li><p>turn the Maintenance mode off.</p></li></ul><p><strong>A note to developers</strong><br />Please check your extensions, styles and language packs for compatibility with the version 1.3.5. The following may be of use to you:<br /></p><ul><li><p><a href="http://punbb.informer.com/trac/changeset?new=1725%40punbb%2Ftrunk%2Fstyle%2FOxygen&amp;old=1668%40punbb%2Ftrunk%2Fstyle%2FOxygen">CSS changes</a>;</p></li><li><p><a href="http://punbb.informer.com/trac/changeset?new=1725%40punbb%2Ftrunk%2Flang%2FEnglish&amp;old=1668%40punbb%2Ftrunk%2Flang%2FEnglish">language files changes</a>.</p></li></ul><p>Downloads: get the latest PunBB on <a href="http://punbb.informer.com/downloads.php#1.3.5">Downloads page</a> or via <a href="http://punbb.informer.com/trac/browser/punbb/tags/punbb-1.3.5">Subversion repository</a>.</p><div class="codebox"><pre><code>// Code
    public function test_parse_message_code_long() {
    $errors = array();
    $this-&gt;assertEquals($result, parse_message(preparse_bbcode(forum_trim($src), $errors), $errors));
}</code></pre></div><p><a href="http://xn--d1acpjx3f.xn--p1ai">http://яндекс.рф</a></p>';

		$src = 'PunBB 1.3.5 is released

[b]1.3.4 to 1.3.5 changes[/b]
[list=*]
[*]a lot of bugs fixed (CSS & markup, added missing lang entries on language files, correct path and alerts on install, fixed typos and more);[/*]
[*]hidden guest email;[/*]
[*]increased visit timeout;[/*]
[*]deleting non-activated users on registering;[/*]
[*]correct deprecated function calls;[/*]
[*]added paginal navigation on pages admin users and bans, added validation timezone and report length, added hooks;[/*]
[/list]

[b]How to upgrade[/b]
[list=*]
[*]make a backup of the database and files;[/*]
[*]turn the Maintenance mode on (via admin panel);[/*]
[*]overwrite old files with new ones;[/*]
[*]after owerwriting, verify that the cache, img/avatars and extensions (for pun_repository) directories have enough write permissions (usually 777);[/*]
[*]clear cache directory;[/*]
[*]go to the forum index and run db_update.php script;[/*]
[*]turn the Maintenance mode off.[/*]
[/list]

[b]A note to developers[/b]
Please check your extensions, styles and language packs for compatibility with the version 1.3.5. The following may be of use to you:
[list=*]
[*][url=http://punbb.informer.com/trac/changeset?new=1725%40punbb%2Ftrunk%2Fstyle%2FOxygen&old=1668%40punbb%2Ftrunk%2Fstyle%2FOxygen]CSS changes[/url];[/*]
[*][url=http://punbb.informer.com/trac/changeset?new=1725%40punbb%2Ftrunk%2Flang%2FEnglish&old=1668%40punbb%2Ftrunk%2Flang%2FEnglish]language files changes[/url].[/*]
[/list]
Downloads: get the latest PunBB on [url=http://punbb.informer.com/downloads.php#1.3.5]Downloads page[/url] or via [url=http://punbb.informer.com/trac/browser/punbb/tags/punbb-1.3.5]Subversion repository[/url].

[code]
// Code
    public function test_parse_message_code_long() {
    $errors = array();
    $this->assertEquals($result, parse_message(preparse_bbcode(forum_trim($src), $errors), $errors));
}[/code]

http://яндекс.рф';

		$this->assertEquals($result, parse_message(preparse_bbcode(forum_trim($src), $errors), $errors));
	}
}





?>
