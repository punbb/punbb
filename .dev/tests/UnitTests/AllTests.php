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


if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'AllTests::runInCLI');
}

class AllTests {
    public static function runInCLI() {
        //PHPUnit_TextUI_TestRunner::run(self::getTestSuite(), array());
    }

    public static function getTestSuite() {
        $suite = new \PHPUnit_Framework_TestSuite("PunBB - All Tests");

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
        $suite = self::getTestSuite();

        $listener = new PHPUnit_Util_Log_TAP;

        $testResult = new PHPUnit_Framework_TestResult();
        $testResult->addListener($listener);

        $result = $suite->run($testResult);

        return $result;
    }
}

if (PHPUnit_MAIN_METHOD == 'AllTests::runInCLI') {
    AllTests::runInCLI();
}
