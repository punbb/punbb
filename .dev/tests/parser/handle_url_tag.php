<?php

class handle_url_tag_Test extends PHPUnit_TestCase {


	public function test_handle_url_tag() {
		$this->assertEquals('<a href="http://ya.ru/">http://ya.ru/</a>', handle_url_tag('http://ya.ru/'));
		$this->assertEquals('<a href="http://ya.ru">http://ya.ru</a>', handle_url_tag('http://ya.ru'));
		$this->assertEquals('<a href="http://ya.ru">ya.ru</a>', handle_url_tag('ya.ru'));
		$this->assertEquals('<a href="http://www.ya.ru">www.ya.ru</a>', handle_url_tag('www.ya.ru'));
		$this->assertEquals('<a href="ftp://ya.ru/">ftp://ya.ru/</a>', handle_url_tag('ftp://ya.ru/'));
	}


	public function test_handle_url_tag_bad_chars() {
		$this->assertEquals('<a href="http://ya.ru/?cache=123">http://ya.ru/?cache=123</a>', handle_url_tag('http://ya.ru/?cache=123'));
		$this->assertEquals('<a href="http://ya.ru/?cache=123%204">http://ya.ru/?cache=123 4</a>', handle_url_tag('http://ya.ru/?cache=123 4'));
		$this->assertEquals('<a href="http://ya.ru/?cache=123">http://ya.ru/?cache=123"</a>', handle_url_tag('http://ya.ru/?cache=123"'));
	}


	public function test_handle_url_tag_with_bbcode() {
		$this->assertEquals('[url=http://ya.ru][/url]', handle_url_tag('http://ya.ru', '', TRUE));
		$this->assertEquals('[url=http://ya.ru][/url]', handle_url_tag('ya.ru', '', TRUE));
		$this->assertEquals('[url=http://www.ya.ru][/url]', handle_url_tag('www.ya.ru', '', TRUE));
		$this->assertEquals('[url=ftp://ya.ru/][/url]', handle_url_tag('ftp://ya.ru/', '', TRUE));
	}

	public function test_handle_url_tag_international() {
		$this->assertEquals('<a href="http://xn--l1adgmc.xn--p1ai?viewtopic=1234#p4">http://форум.рф?viewtopic=1234#p4</a>', handle_url_tag('http://форум.рф?viewtopic=1234#p4'));
		$this->assertEquals('<a href="http://xn--caf-dma.com">http://café.com</a>', handle_url_tag('http://café.com'));
		$this->assertEquals('<a href="http://xn--caf-dma.com">http://café.com</a>', handle_url_tag('http://xn--caf-dma.com'));
	}

	public function test_do_clickable() {
		$this->assertEquals('[url=http://xn--caf-dma.com]http://café.com[/url]', do_clickable('http://xn--caf-dma.com', TRUE));
		$this->assertEquals('[url=http://xn--d1acpjx3f.xn--p1ai]http://яндекс.рф[/url]', do_clickable('http://яндекс.рф', TRUE));
		$this->assertEquals('В лесу родилась [url=http://xn--d1acpjx3f.xn--p1ai/?text=ёлочка]http://яндекс.рф/?text=ёлочка[/url] и...', do_clickable('В лесу родилась http://яндекс.рф/?text=ёлочка и...', TRUE));
		$this->assertEquals('[url=http://xn--d1acpjx3f.xn--p1ai]http://яндекс.рф[/url]', do_clickable('http://xn--d1acpjx3f.xn--p1ai', TRUE));
		$this->assertEquals('[url=http://xn--d1acpjx3f.xn--p1ai/]http://яндекс.рф/[/url]', do_clickable('http://xn--d1acpjx3f.xn--p1ai/', TRUE));
		$this->assertEquals('[url=http://xn--d1acpjx3f.xn--p1ai/]http://яндекс.рф/[/url]', do_clickable('http://яндекс.рф/', TRUE));
		$this->assertEquals('[url]http://ya.ru/[/url]', do_clickable('http://ya.ru/'));
	}

}





?>
