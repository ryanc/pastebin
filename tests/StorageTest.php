<?php

namespace Paste\Tests;

use Paste\Entity;
use Paste\Storage;

class StorageTest extends \PHPUnit_Framework_TestCase
{
    private $path;

    private $db;

    private $app;

    public function setUp()
    {
        $app = require __DIR__ . '/../src/app.php';

        $this->app = $app;

        $schema = file_get_contents($app['pastebin.schema']);

        $this->db = $app['db'];
        $this->db->exec($schema);
    }

    public function tearDown()
    {
        $this->db->close();
    }

    public function testPasteSave()
    {
        $paste = new Entity\Paste;
        $paste->setContent('This is a test.');
        $paste->setTimestamp(new \DateTime);
        $paste->setFilename('test.txt');
        $paste->setIp('127.0.0.1');

        $storage = $this->app['storage'];
        $id = $storage->save($paste);

        $this->assertNotSame(false, $id);

        $dbPaste = $storage->get($id);
        $this->assertEquals($dbPaste->getContent(), $paste->getContent());
        $this->assertEquals($dbPaste->getFilename(), $paste->getFilename());
        $this->assertEquals($dbPaste->getDigest(), $paste->getDigest());
        $this->assertEquals($dbPaste->getIp(), $paste->getIp());
        $this->assertEquals($dbPaste->getTimestamp(), $paste->getTimestamp());
        $this->assertEquals($dbPaste->getHighlight(), $paste->getHighlight());
    }

    public function testGetReturnsFalseIfNotInDatabase()
    {
        $storage = $this->app['storage'];
        $paste = $storage->get(99999);

        $this->assertFalse($paste);
    }
}
