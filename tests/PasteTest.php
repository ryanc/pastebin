<?php

namespace Paste\Tests;

use Paste\Entity\Paste;

class PasteTest extends \PHPUnit_Framework_TestCase
{
    public function testId()
    {
        $paste = new Paste;
        $paste->setId(1);
        $this->assertEquals(1, $paste->getId());
    }

    public function testContent()
    {
        $paste = new Paste;
        $paste->setContent("Hello :)");
        $this->assertEquals("Hello :)", $paste->getContent());
    }

    public function testTimestamp()
    {
        $timestamp = time();

        $paste = new Paste;
        $paste->setTimestamp($timestamp);
        $this->assertEquals($timestamp, $paste->getTimestamp());
    }

    public function testToken()
    {
        $paste = new Paste;
        $paste->setToken('1A');
        $this->assertEquals('1A', $paste->getToken());
    }

    public function testFilename()
    {
        $paste = new Paste;
        $paste->setFilename('test.txt');
        $this->assertEquals('test.txt', $paste->getFilename());
    }

    public function testIp()
    {
        $paste = new Paste;
        $paste->setIp('127.0.0.1');
        $this->assertEquals('127.0.0.1', $paste->getIp());
    }

    public function testBinaryIp()
    {
        $binaryIp = inet_pton('127.0.0.1');

        $paste = new Paste;
        $paste->setIp('127.0.0.1');
        $this->assertEquals($binaryIp, $paste->getBinaryIp());

        $paste = null;

        $paste = new Paste;
        $paste->setBinaryIp($binaryIp);
        $this->assertEquals('127.0.0.1', $paste->getIp());
    }

    public function testDigest()
    {
        $digest = md5("Hello :)");

        $paste = new Paste;
        $paste->setContent("Hello :)");
        $this->assertEquals($digest, $paste->getDigest());
    }

    public function testNormalizeContent()
    {
        $paste = new Paste;
        $paste->setContent("Hello :)\r\nHello :)");
        $this->assertEquals("Hello :)\nHello :)", $paste->getContent());

        $paste = null;

        $paste = new Paste;
        $paste->setContent("Hello :)\rHello :)");
        $this->assertEquals("Hello :)\nHello :)", $paste->getContent());
    }

    public function testTrimContent()
    {
        $paste = new Paste;
        $paste->setContent("Hello :)\n");
        $this->assertEquals("Hello :)", $paste->getContent());
    }
}
