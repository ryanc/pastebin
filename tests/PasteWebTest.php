<?php

namespace Paste\Tests;

use Silex\WebTestCase;

class AppTest extends WebTestCase
{
    public function createApplication()
    {
        $app = require __DIR__ . '/../src/app.php';
        $app['debug'] = true;
        unset($app['exception_handler']);

        $app['monolog.logfile'] = '/tmp/pastebin.log';
        $app['session.test'] = true;

        /* Import the controllers or else none of the routes will be 
           found. */
        require __DIR__ . '/../src/controllers.php';

        return $app;
    }

    public function testInitialPage()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isOk());
    }

    public function testNewPastePage()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/p/new');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filterXPath("//form"));
        $this->assertCount(1, $crawler->filterXPath("//form/textarea"));
        $this->assertCount(1, $crawler->filterXPath("//form//input[contains(@type, 'text')]"));
        $this->assertCount(1, $crawler->filterXPath("//form//input[contains(@type, 'checkbox')]"));
        $this->assertCount(1, $crawler->filterXPath("//button"));
    }
}
