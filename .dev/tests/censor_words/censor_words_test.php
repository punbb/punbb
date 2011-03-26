<?php

class censor_words_do_Test extends PHPUnit_TestCase {

	//
    function test_censor_words_do_unicode_true() {
		$this->do_test(TRUE);
    }


	//
    function test_censor_words_do_unicode_false() {
		$this->do_test(FALSE);
    }


	//
	private function create_censor($search_for, $replace) {
		return array(0 => array('id' => '1', 'search_for' => $search_for, 'replace_with' => $replace));
	}


	//
	private function do_test($unicode) {
		$this->assertEquals('f***e', censor_words_do($this->create_censor('false', 'f***e'), 'false', $unicode));
		$this->assertEquals('i have a f***e apple', censor_words_do($this->create_censor('false', 'f***e'), 'i have a false apple', $unicode));
		$this->assertEquals('i have a TRUE apple', censor_words_do($this->create_censor('false', 'TRUE'), 'i have a false apple', $unicode));
		$this->assertEquals('i have a falseapple', censor_words_do($this->create_censor('false', 'TRUE'), 'i have a falseapple', $unicode));
		$this->assertEquals('i have a TRUE', censor_words_do($this->create_censor('false*', 'TRUE'), 'i have a falseapple', $unicode));
		$this->assertEquals('i have a TRUE', censor_words_do($this->create_censor('fa*se', 'TRUE'), 'i have a false', $unicode));

		// Word borders
		$this->assertEquals('TRUE falseing', censor_words_do($this->create_censor('false', 'TRUE'), 'false falseing', $unicode));
		$this->assertEquals('цензура раму-- 123', censor_words_do($this->create_censor('раму', 'цензура'), 'раму раму-- 123', $unicode));

		// Russian
		$this->assertEquals('у нас тест', censor_words_do($this->create_censor('проверка', 'тест'), 'у нас проверка', $unicode));
	}
}





?>
