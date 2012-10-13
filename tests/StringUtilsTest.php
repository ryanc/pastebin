<?php

use Utils\String;

class StringUtilsTest extends PHPUnit_Framework_TestCase
{
    public function testEqual()
    {
        $this->assertTrue(Utils\String::timeSafeCompare("abcdef", "abcdef"));
        $this->assertTrue(Utils\String::timeSafeCompare("", ""));
    }

    public function testNotEqual()
    {
        $this->assertFalse(Utils\String::timeSafeCompare("abcdef", "fedcba"));
        $this->assertFalse(Utils\String::timeSafeCompare("abcdef", "abcdefabcdef"));
        $this->assertFalse(Utils\String::timeSafeCompare(1, 1));
        $this->assertFalse(Utils\String::timeSafeCompare(1.5, 1.5));
        $this->assertFalse(Utils\String::timeSafeCompare(true, true));
        $this->assertFalse(Utils\String::timeSafeCompare(array(), array()));
    }
}
