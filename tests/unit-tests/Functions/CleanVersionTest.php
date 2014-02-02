<?php

class CleanVersionTest extends PHPUnit_Framework_TestCase {
    public function testCleanVersion() {
        $this->assertEquals('1.5', clean_version('1.5.0'));
        $this->assertEquals('0.5.10', clean_version('0.5.10'));
        $this->assertEquals('0.5.100', clean_version('0.5.100'));
        $this->assertEquals('1.5.1', clean_version('1.5.1'));
    }
}
