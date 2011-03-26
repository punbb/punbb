<?php

class utf8_Test extends PHPUnit_TestCase {
	//
    function test_utf8_strlen() {
		$this->assertEquals(0, utf8_strlen(''), 'Should be 0');
		$this->assertEquals(4, utf8_strlen('test'), 'Should be 4');
		$this->assertEquals(4, utf8_strlen('тест'), 'Should be 4');
		$this->assertEquals(20, utf8_strlen("I\xc3\xb1t\xc3\xabrn\xc3\xa2ti\xc3\xb4n\xc3\xa0liz\xc3\xa6ti\xc3\xb8n"), 'Should be 20');
    }

	//
	function test_utf8_trim() {
		$this->assertEquals('a b', forum_trim(' a b '));
		$this->assertEquals('0a b0', forum_trim(' 0a b0'));
		$this->assertEquals('\0a b0', forum_trim(' \0a b0'));
		$this->assertEquals('封鎖進階設定', forum_trim('  封鎖進階設定 '));
		$this->assertEquals('封 鎖進 階設定', forum_trim('  封 鎖進 階設定'));
		$this->assertEquals('Ёма ЙО', forum_trim(' Ёма ЙО '));
		$this->assertEquals('x', forum_trim(" \t\n\r\x00\x0B\xC2\xA0x"));
		$this->assertEquals('e', forum_trim("\xc5\x98e-", "\xc5\x98-"));
	}

	//
	function test_utf8_all_caps() {
		$this->assertTrue(check_is_all_caps('ТЕСТ'));
		$this->assertTrue(check_is_all_caps('THIS IS A TEST'));

		$this->assertFalse(check_is_all_caps('THIS IS NOT a TEST'));
		$this->assertFalse(check_is_all_caps('Тест'));
		$this->assertFalse(check_is_all_caps('тест'));
		$this->assertFalse(check_is_all_caps('5580'));
		$this->assertFalse(check_is_all_caps('tEsT Run'));
	}

	//
	function test_escape_cdata() {
		$this->assertEquals('test cdata', escape_cdata('test cdata'));
		$this->assertEquals('<![CDATA[test cdata]]&gt;', escape_cdata('<![CDATA[test cdata]]>'));
	}
}





?>
