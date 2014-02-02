<?php

class RandomKeyUniqueTest extends PHPUnit_Framework_TestCase
{
    const MIN_STEPS     = 6;
    const MAX_STEPS     = 40;
    const MAX_PASSWORDS = 512;

    public function testUniqueNotReadableNotHash()
    {
        for ($i = self::MIN_STEPS; $i <= self::MAX_STEPS; $i++) {
            $passwords_count = self::MAX_PASSWORDS;
            $generated_keys  = array();
            $length          = $i;

            while ($passwords_count-- > 0) {
                $key = sha1(random_key($length, false, false));
                $this->assertFalse(in_array($key, $generated_keys));
                $generated_keys[] = $key;
            }
        }
    }

    public function testOutputLengthNotReadableAndHash()
    {
        for ($i = self::MIN_STEPS; $i <= self::MAX_STEPS; $i++) {
            $passwords_count = self::MAX_PASSWORDS;
            $generated_keys  = array();
            $length          = $i;

            while ($passwords_count-- > 0) {
                $key = sha1(random_key($length, false, true));
                $this->assertFalse(in_array($key, $generated_keys));
                $generated_keys[] = $key;
            }
        }
    }

    public function testOutputLengthReadable()
    {
        for ($i = self::MIN_STEPS; $i <= self::MAX_STEPS; $i++) {
            $passwords_count = self::MAX_PASSWORDS;
            $generated_keys  = array();
            $length          = $i;

            while ($passwords_count-- > 0) {
                $key = sha1(random_key($length, true, false));
                $this->assertFalse(in_array($key, $generated_keys));
                $generated_keys[] = $key;
            }
        }
    }

    public function testOutputLengthReadableAndHash()
    {
        for ($i = self::MIN_STEPS; $i <= self::MAX_STEPS; $i++) {
            $passwords_count = self::MAX_PASSWORDS;
            $generated_keys  = array();
            $length          = $i;

            while ($passwords_count-- > 0) {
                $key = sha1(random_key($length, true, true));
                $this->assertFalse(in_array($key, $generated_keys));
                $generated_keys[] = $key;
            }
        }
    }
}
