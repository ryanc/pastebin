<?php

namespace Paste\Tests;

use Silex\WebTestCase;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class AppTest extends WebTestCase
{
    public function createApplication()
    {
        $app = require __DIR__ . '/../src/app.php';
        $app['debug'] = true;
        unset($app['exception_handler']);

        $app['monolog.logfile'] = '/tmp/pastebin.log';

        $app['session.test'] = true;

        $app['db.options'] = array(
            'driver' => 'pdo_sqlite',
            'path'   => ':memory:',
        );

        $app['db']->exec(file_get_contents(__DIR__ . '/../sql/schema.sql'));

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

    public function testPageNotFound()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/does-not-exist');

        $this->assertTrue($client->getResponse()->isNotFound());
    }

    public function testMethodNotAllowed()
    {
        $client = $this->createClient();

        try {
            $crawler = $client->request('GET', '/api');
        }
        catch (MethodNotAllowedHttpException $ex) {
            return;
        }

        $this->fail();
    }

    public function testPasteHistory()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/p/history');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filterXPath("//h3"));
    }

    public function testClearPasteHistory()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/p/history/clear');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filterXPath("//h3"));
    }

    public function testApiSuccess()
    {
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api', array(
            'content' => 'Hello :)',
        ));

        $this->assertTrue($client->getResponse()->isOk());

        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertTrue($content['success']);
    }

    public function testApiFailure()
    {
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api', array(
            'unicorn' => 'Hello :)',
        ));

        $this->assertFalse($client->getResponse()->isOk());
        $this->assertEquals(400, $client->getResponse()->getStatusCode());

        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertFalse($content['success']);
    }
}
