<?php

class FlashMessengerTest extends PHPUnit_Framework_TestCase {
    public function testAddError() {
        global $forum_flash;

        $this->expectOutputString('<span class="message_error">Error message</span>');
        $forum_flash->add_error("Error message");
        $forum_flash->show();
    }

    public function testShowOnlyReturn() {
        global $forum_flash;

        $this->expectOutputString('');
        $forum_flash->add_error("Error message");
        $forum_flash->show(TRUE);
    }

    public function testShow() {
        global $forum_flash;

        $this->expectOutputString('<span class="message_error">Error message</span>');
        $forum_flash->add_error("Error message");
        $forum_flash->show(FALSE);
    }

    public function testClear() {
        global $forum_flash;

        $this->expectOutputString("");
        $forum_flash->add_info("Test message");
        $forum_flash->clear();
        $forum_flash->show(FALSE);
    }
}
