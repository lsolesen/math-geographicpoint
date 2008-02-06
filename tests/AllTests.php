<?php
if (!defined('PHPUNIT_MAIN_METHOD')) {
    define('PHPUNIT_MAIN_METHOD', 'GeographicPointTests::main');
}

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'LatitudeLongitudeTest.php';
require_once 'LambertTest.php';
require_once 'UTMTest.php';

class GeographicPointTests {
    public static function main() {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite() {
        $suite = new PHPUnit_Framework_TestSuite('Tests');

        $suite->addTestSuite('LatitudeLongitudeTest');
        $suite->addTestSuite('UTMTest');
        $suite->addTestSuite('LambertTest');

        return $suite;
    }
}

if (PHPUNIT_MAIN_METHOD == 'GeographicPointTests::main') {
    GeographicPointTests::main();
}
?>