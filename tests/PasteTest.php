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
        $timestamp = new \DateTime;

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

        $paste = null;

        $paste = new Paste;
        $this->assertNull($paste->getDigest());
    }

    public function testConvertTabs()
    {
        $paste = new Paste;
        $paste->setConvertTabs(true);
        $this->assertEquals(true, $paste->getConvertTabs());
    }

    public function testHighlight()
    {
        $paste = new Paste;
        $this->assertEquals(true, $paste->getHighlight());
        $paste->setHighlight(true);
        $this->assertEquals(true, $paste->getHighlight());
        $paste->setHighlight(false);
        $this->assertEquals(false, $paste->getHighlight());
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

    public function testTabsToSpaces()
    {
        $paste = new Paste;
        $paste->setContent("Hello :)\tHello :)");
        $this->assertEquals("Hello :)\tHello :)", $paste->getContent());

        $paste = null;

        $paste = new Paste;
        $paste->setContent("Hello :)\tHello :)");
        $paste->setConvertTabs(true);
        $this->assertEquals("Hello :)    Hello :)", $paste->getContent());

        $paste = null;

        $paste = new Paste;
        $paste->setContent("Hello :)\tHello :)");
        $paste->setConvertTabs(false);
        $this->assertEquals("Hello :)\tHello :)", $paste->getContent());
    }

    public function testFluentInterface()
    {
        $now = new \DateTime;

        $paste = new Paste;
        $paste
            ->setId(1)
            ->setContent('Hello :)')
            ->setTimestamp($now)
            ->setToken('1')
            ->setFilename('test.txt')
            ->setIp('127.0.0.1')
            ->setBinaryIp(inet_pton('127.0.0.1'))
            ->setConvertTabs(true)
            ->setHighlight(true)
        ;

        $this->assertEquals(1, $paste->getId());
        $this->assertEquals('Hello :)', $paste->getContent());
        $this->assertEquals($now, $paste->getTimestamp());
        $this->assertEquals('1', $paste->getToken());
        $this->assertEquals('test.txt', $paste->getFilename());
        $this->assertEquals('127.0.0.1', $paste->getIp());
        $this->assertEquals(inet_pton('127.0.0.1'), $paste->getBinaryIp());
        $this->assertTrue($paste->getConvertTabs());
        $this->assertTrue($paste->getHighlight());
    }
}
