<?php

class RandomKeyOutputLengthTest extends PHPUnit_Framework_TestCase
{
    const MAX_STEPS = 256;
    const MAX_SHA1_HASH_LENGTH = 40;

    public function testOutputLengthNotReadableNotHash()
    {
        for ($i = 1; $i <= self::MAX_STEPS; $i++) {
            $length = $i;

            $this->assertSame($length, strlen(random_key($length, false, false)));
        }
    }

    public function testOutputLengthNotReadableAndHash()
    {
        for ($i = 1; $i <= self::MAX_STEPS; $i++) {
            $length = min($i, self::MAX_SHA1_HASH_LENGTH);

            $this->assertSame($length, strlen(random_key($length, false, true)));
        }
    }

    public function testOutputLengthReadable()
    {
        for ($i = 1; $i <= self::MAX_STEPS; $i++) {
            $length = $i;

            $this->assertSame($length, strlen(random_key($length, true, false)));
        }
    }

    public function testOutputLengthReadableAndHash()
    {
        for ($i = 1; $i <= self::MAX_STEPS; $i++) {
            $length = min($i, self::MAX_SHA1_HASH_LENGTH);

            $this->assertSame($length, strlen(random_key($length, true, true)));
        }
    }
}
