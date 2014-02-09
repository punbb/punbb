<?php

class RandomKeyRegexpTest extends PHPUnit_Framework_TestCase
{
    const MAX_STEPS = 256;
    const MAX_SHA1_HASH_LENGTH = 40;

    public function testOutputLengthNotReadableAndHash()
    {
        for ($i = 1; $i <= self::MAX_STEPS; $i++) {
            $length = min($i, self::MAX_SHA1_HASH_LENGTH);

            $this->assertRegExp('#^[0-9a-z]+$#', random_key($length, FALSE, TRUE));
        }
    }

    public function testOutputLengthReadable()
    {
        for ($i = 1; $i <= self::MAX_STEPS; $i++) {
            $length = $i;

            $this->assertRegExp('#^[0-9a-zA-Z]+$#', random_key($length, TRUE, FALSE));
        }
    }
}
