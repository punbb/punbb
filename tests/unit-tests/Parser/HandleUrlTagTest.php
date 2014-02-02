<?php

class HandleUrlTagTest extends PHPUnit_Framework_TestCase {
	public function testHandleUrlTag() {
		$this->assertEquals('<a href="http://ya.ru/">http://ya.ru/</a>', handle_url_tag('http://ya.ru/'));
		$this->assertEquals('<a href="http://ya.ru">http://ya.ru</a>', handle_url_tag('http://ya.ru'));
		$this->assertEquals('<a href="http://ya.ru">ya.ru</a>', handle_url_tag('ya.ru'));
		$this->assertEquals('<a href="http://www.ya.ru">www.ya.ru</a>', handle_url_tag('www.ya.ru'));
		$this->assertEquals('<a href="ftp://ya.ru/">ftp://ya.ru/</a>', handle_url_tag('ftp://ya.ru/'));
	}

	public function testHandleUrlTagWithBadChars() {
		$this->assertEquals('<a href="http://ya.ru/?cache=123">http://ya.ru/?cache=123</a>', handle_url_tag('http://ya.ru/?cache=123'));
		$this->assertEquals('<a href="http://ya.ru/?cache=123%204">http://ya.ru/?cache=123 4</a>', handle_url_tag('http://ya.ru/?cache=123 4'));
		$this->assertEquals('<a href="http://ya.ru/?cache=123">http://ya.ru/?cache=123"</a>', handle_url_tag('http://ya.ru/?cache=123"'));
	}

	public function testHandleUrlTagWithBBcode() {
		$this->assertEquals('[url=http://ya.ru][/url]', handle_url_tag('http://ya.ru', '', TRUE));
		$this->assertEquals('[url=http://ya.ru][/url]', handle_url_tag('ya.ru', '', TRUE));
		$this->assertEquals('[url=http://www.ya.ru][/url]', handle_url_tag('www.ya.ru', '', TRUE));
		$this->assertEquals('[url=ftp://ya.ru/][/url]', handle_url_tag('ftp://ya.ru/', '', TRUE));
	}

	public function testHandleUrlTagInternational() {
        if (!defined('FORUM_ENABLE_IDNA')) {
            $this->markTestSkipped('The FORUM_ENABLE_IDNA is not turned on.');
        }

		$this->assertEquals('<a href="http://xn--l1adgmc.xn--p1ai?viewtopic=1234#p4">http://форум.рф?viewtopic=1234#p4</a>', handle_url_tag('http://форум.рф?viewtopic=1234#p4'));
		$this->assertEquals('<a href="http://xn--caf-dma.com">http://café.com</a>', handle_url_tag('http://café.com'));
		$this->assertEquals('<a href="http://xn--caf-dma.com">http://café.com</a>', handle_url_tag('http://xn--caf-dma.com'));
	}
}
