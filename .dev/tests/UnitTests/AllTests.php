<?php

require_once 'PHPUnit/Autoload.php';

require_once dirname(__FILE__) . '/TestHelper.php';
require_once dirname(__FILE__) . '/Unicode/UnicodeTest.php';

require_once dirname(__FILE__) . '/Functions/FunctionsTest.php';
require_once dirname(__FILE__) . '/Functions/CleanVersionTest.php';
require_once dirname(__FILE__) . '/Functions/GenerateAvatarMarkupTest.php';
require_once dirname(__FILE__) . '/Functions/ArrayInsertTest.php';

require_once dirname(__FILE__) . '/Censor/CensorWordsTest.php';
require_once dirname(__FILE__) . '/Parser/HandleUrlTagTest.php';
require_once dirname(__FILE__) . '/Parser/DoClicableTest.php';
require_once dirname(__FILE__) . '/Parser/ParseMessageTest.php';

require_once dirname(__FILE__) . '/FlashMessenger/FlashMessengerTest.php';


class AllTests {
    public static function suite() {
        $suite = new PHPUnit_Framework_TestSuite("Forum_AllTests");

        $suite->addTestSuite('UnicodeTest');
        $suite->addTestSuite('FunctionsTest');
        $suite->addTestSuite('CleanVersionTest');
        $suite->addTestSuite('GenerateAvatarMarkupTest');
        $suite->addTestSuite('ArrayInsertTest');
        $suite->addTestSuite('CensorWordsTest');
        $suite->addTestSuite('HandleUrlTagTest');
        $suite->addTestSuite('DoClicableTest');
        $suite->addTestSuite('ParseMessageTest');
        $suite->addTestSuite('FlashMessengerTest');

        return $suite;
    }

    public static function runAndReturnResultAsJSON() {
        $suite = self::suite();

        $listener = new PHPUnit_Util_Log_TAP;

        $testResult = new PHPUnit_Framework_TestResult();
        $testResult->addListener($listener);

        $result = $suite->run($testResult);

        return $result;
    }
}
