<?php

use Paste\Math\Base62;

class Base62Test extends PHPUnit_Framework_TestCase
{
    protected $encoder;

    public function setUp()
    {
        $this->encoder = new Base62;
    }

    public function testEncode()
    {
        $this->assertEquals('0',  $this->encoder->encode(0));
        $this->assertEquals('a',  $this->encoder->encode(10));
        $this->assertEquals('A',  $this->encoder->encode(36));
        $this->assertEquals('Z',  $this->encoder->encode(61));
        $this->assertEquals('1a', $this->encoder->encode(72));
        $this->assertEquals('1A', $this->encoder->encode(98));
        $this->assertEquals('1Z', $this->encoder->encode(123));
    }

    public function testDecode()
    {
        $this->assertEquals(0,   $this->encoder->decode('0'));
        $this->assertEquals(10,  $this->encoder->decode('a'));
        $this->assertEquals(36,  $this->encoder->decode('A'));
        $this->assertEquals(61,  $this->encoder->decode('Z'));
        $this->assertEquals(72,  $this->encoder->decode('1a'));
        $this->assertEquals(98,  $this->encoder->decode('1A'));
        $this->assertEquals(123, $this->encoder->decode('1Z'));
    }
}
