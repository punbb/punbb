<?php

class censor_words_do_Test extends PHPUnit_TestCase {

	// Test with Unicode support
    public function test_censor_words_do_unicode_true() {
		$this->do_test(TRUE);
    }


	// Test without Unicode support
    public function test_censor_words_do_unicode_false() {
		$this->do_test(FALSE);
    }


	// Create a censor words array
	private function create_censor($search_for, $replace) {
		return array(0 => array('id' => '1', 'search_for' => $search_for, 'replace_with' => $replace));
	}


	// make a real test
	private function do_test($unicode) {
		$this->assertEquals('f***e', censor_words_do($this->create_censor('false', 'f***e'), 'false', $unicode));
		$this->assertEquals('i have a f***e apple', censor_words_do($this->create_censor('false', 'f***e'), 'i have a false apple', $unicode));
		$this->assertEquals('i have a TRUE apple', censor_words_do($this->create_censor('false', 'TRUE'), 'i have a false apple', $unicode));
		$this->assertEquals('i have a falseapple', censor_words_do($this->create_censor('false', 'TRUE'), 'i have a falseapple', $unicode));
		$this->assertEquals('i have a TRUE', censor_words_do($this->create_censor('false*', 'TRUE'), 'i have a falseapple', $unicode));
		$this->assertEquals('i have a TRUE', censor_words_do($this->create_censor('fa*se', 'TRUE'), 'i have a false', $unicode));

		// Word borders
		$this->assertEquals('TRUE falseing', censor_words_do($this->create_censor('false', 'TRUE'), 'false falseing', $unicode));

		$this->assertEquals('My XXX', censor_words_do($this->create_censor('tes*', 'XXX'), 'My test', $unicode));
		$this->assertEquals('My XXX!', censor_words_do($this->create_censor('tes*', 'XXX'), 'My test!', $unicode));
		$this->assertEquals('My XXX.', censor_words_do($this->create_censor('tes*', 'XXX'), 'My test.', $unicode));

		// Russian only with unicode
		if ($unicode) {
			$this->assertEquals('цензура раму-- 123', censor_words_do($this->create_censor('раму', 'цензура'), 'раму раму-- 123', $unicode));
			$this->assertEquals('у нас тест', censor_words_do($this->create_censor('проверка', 'тест'), 'у нас проверка', $unicode));
			$this->assertEquals('На пепелаце транслюкатор прикрепляется к так называемой цаппе.', censor_words_do($this->create_censor('гра*па', 'транслюкатор'), 'На пепелаце гравицаппа прикрепляется к так называемой цаппе.', $unicode));
			$this->assertEquals('На пепелаце гравицаппа прикрепляется к так называемой цаппе.', censor_words_do($this->create_censor('цаппа', 'транслюкатор'), 'На пепелаце гравицаппа прикрепляется к так называемой цаппе.', $unicode));
			$this->assertEquals('На пепелаце гравицаппа прикрепляется к так называемой XXX.', censor_words_do($this->create_censor('цап*', 'XXX'), 'На пепелаце гравицаппа прикрепляется к так называемой цаппе.', $unicode));
		}
	}
}





?>
