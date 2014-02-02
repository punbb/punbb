<?php

class UnicodeTest extends PHPUnit_Framework_TestCase {
	public function testUnicodeStrlen() {
		$this->assertEquals(0, utf8_strlen(''), 'Should be 0');
		$this->assertEquals(4, utf8_strlen('test'), 'Should be 4');
		$this->assertEquals(4, utf8_strlen('тест'), 'Should be 4');
		$this->assertEquals(10, utf8_strlen(' проверка '), 'Should be 10');
		$this->assertEquals(20, utf8_strlen("I\xc3\xb1t\xc3\xabrn\xc3\xa2ti\xc3\xb4n\xc3\xa0liz\xc3\xa6ti\xc3\xb8n"), 'Should be 20');
	}

	public function testUnicodeTrim() {
		$this->assertEquals('a b', forum_trim(' a b '));
		$this->assertEquals('0a b0', forum_trim(' 0a b0'));
		$this->assertEquals('\0a b0', forum_trim(' \0a b0'));
		$this->assertEquals('封鎖進階設定', forum_trim('  封鎖進階設定 '));
		$this->assertEquals('封 鎖進 階設定', forum_trim('  封 鎖進 階設定'));
		$this->assertEquals('Ёма ЙО', forum_trim(' Ёма ЙО '));
		$this->assertEquals('x', forum_trim(" \t\n\r\x00\x0B\xC2\xA0x"));
		$this->assertEquals('e', forum_trim("\xc5\x98e-", "\xc5\x98-"));
	}
}
