<?php

require_once 'PHPUnit/Autoload.php';

require_once __DIR__ . '/TestHelper.php';
require_once __DIR__ . '/Unicode/UnicodeTest.php';

require_once __DIR__ . '/Functions/FunctionsTest.php';
require_once __DIR__ . '/Functions/CleanVersionTest.php';
require_once __DIR__ . '/Functions/GenerateAvatarMarkupTest.php';
require_once __DIR__ . '/Functions/ArrayInsertTest.php';

require_once __DIR__ . '/Censor/CensorWordsTest.php';
require_once __DIR__ . '/Parser/HandleUrlTagTest.php';
require_once __DIR__ . '/Parser/DoClicableTest.php';
require_once __DIR__ . '/Parser/ParseMessageTest.php';


class AllTests {
    public static function suite() {
        $suite = new \PHPUnit_Framework_TestSuite("Forum_AllTests");

        $suite->addTestSuite('UnicodeTest');
        $suite->addTestSuite('FunctionsTest');
        $suite->addTestSuite('CleanVersionTest');
        $suite->addTestSuite('GenerateAvatarMarkupTest');
        $suite->addTestSuite('ArrayInsertTest');
        $suite->addTestSuite('CensorWordsTest');
        $suite->addTestSuite('HandleUrlTagTest');
        $suite->addTestSuite('DoClicableTest');
        $suite->addTestSuite('ParseMessageTest');

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
