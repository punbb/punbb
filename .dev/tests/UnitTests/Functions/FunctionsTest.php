<?php

class FunctionsTest extends PHPUnit_Framework_TestCase {
	public function testAllCaps() {
		$this->assertTrue(check_is_all_caps('ТЕСТ'));
		$this->assertTrue(check_is_all_caps('THIS IS A TEST'));
		$this->assertTrue(check_is_all_caps('ПРОВЕРКА '));

		$this->assertFalse(check_is_all_caps('THIS IS NOT a TEST'));
		$this->assertFalse(check_is_all_caps('Тест'));
		$this->assertFalse(check_is_all_caps('тест'));
		$this->assertFalse(check_is_all_caps('5580'));
		$this->assertFalse(check_is_all_caps('tEsT Run'));
	}

	public function testEscapeCdata() {
		$this->assertEquals('test cdata', escape_cdata('test cdata'));
		$this->assertEquals('<![CDATA[test cdata]]&gt;', escape_cdata('<![CDATA[test cdata]]>'));
	}
}
