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

		$result = '<div class="quotebox"><cite>Певица [] Ёлка wrote:</cite><blockquote><p>This is the text I want to quote.</p></blockquote></div>';
		$src = '[quote="Певица [] Ёлка"]This is the text I want to quote.[/quote]';

		$this->assertEquals($result, parse_message(preparse_bbcode(forum_trim($src), $errors), $errors));
	}

	// quoting
	public function test_parse_message_quote_4() {
		$errors = array();

		$result = '<div class="quotebox"><cite>Певица [] Ёлка wrote:</cite><blockquote><p>This is the text I want to quote.</p></blockquote></div>';
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
}





?>
