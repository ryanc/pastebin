<?php

use Math\Base62;

class Base62Test extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    public function testEncode()
    {
        $encoder = new Base62;

        $this->assertEquals(0, $encoder->encode(0));
    }
}
