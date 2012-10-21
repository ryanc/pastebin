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

        $app['db']->exec(file_get_contents($app['pastebin.schema']));

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
        $crawler = $client->request('GET', '/new');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filterXPath("//form"));
        $this->assertCount(1, $crawler->filterXPath("//form/textarea"));
        $this->assertCount(1, $crawler->filterXPath("//form//input[contains(@type, 'text')]"));
        $this->assertCount(2, $crawler->filterXPath("//form//input[contains(@type, 'checkbox')]"));
        $this->assertCount(1, $crawler->filterXPath("//button"));
    }

    public function testNewPaste()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/new');

        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, array(
            'paste[content]'     => 'Hello :)',
            'paste[filename]'    => 'test.txt',
            'paste[convertTabs]' => '1',
            'paste[highlight]'   => '1',
        ));

        $this->assertTrue($client->getResponse()->isRedirect('/p/1'));

        $crawler = $client->request('GET', '/p/1');

        $this->assertCount(1, $crawler->filterXPath("//code"));
        $this->assertEquals('Hello :)', $crawler->filterXPath("//code")->text());
    }

    public function testNewPasteWithNullContent()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/new');

        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, array(
            'paste[content]'     => null,
            'paste[filename]'    => 'test.txt',
            'paste[convertTabs]' => '1',
            'paste[highlight]'   => '1',
        ));

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertFalse($client->getResponse()->isRedirect());
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
        $crawler = $client->request('GET', '/history');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filterXPath("//h3"));
    }

    public function testAbout()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/about');

        $response = $client->getResponse();

        $this->assertTrue($response->isOk());
        $this->assertCount(1, $crawler->filterXPath("//h3"));
        $this->assertContains('http://cavi.cc', $response->getContent());
        $this->assertContains('http://github.com/ryanc/pastebin', $response->getContent());
        $this->assertContains('Ryan Cavicchioni', $response->getContent());
        $this->assertTrue($response->headers->contains('Cache-Control', 's-maxage=31536000'));
    }

    public function testClearPasteHistory()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/history/clear');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filterXPath("//h3"));
    }

    public function testApiSuccess()
    {
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api', array(
            'content'  => 'Hello :)',
            'filename' => 'test.txt',
        ));

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->has('Location'));
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

    public function testGetPaste()
    {
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api', array(
            'content' => 'Hello :)',
        ));

        $crawler = $client->request('GET', '/p/1');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filterXPath("//code"));
        $this->assertEquals('Hello :)', $crawler->filterXPath("//code")->text());
    }

    public function testPasteNotFound()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/p/1');

        $this->assertFalse($client->getResponse()->isOk());
        $this->assertTrue($client->getResponse()->isNotFound());
    }

    public function testGetRawPaste()
    {
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api', array(
            'content' => 'Hello :)',
        ));

        $crawler = $client->request('GET', '/p/1/raw');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertEquals('Hello :)', $client->getResponse()->getContent());
    }

    public function testDuplicatePaste()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/new');

        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, array(
            'paste[content]'     => 'Hello :)',
            'paste[filename]'    => 'test.txt',
            'paste[convertTabs]' => '1',
            'paste[highlight]'   => '1',
        ));

        $this->assertTrue($client->getResponse()->isRedirect('/p/1'));

        $crawler = $client->request('GET', '/p/1');

        $this->assertCount(1, $crawler->filterXPath("//code"));
        $this->assertEquals('Hello :)', $crawler->filterXPath("//code")->text());

        $crawler = $client->request('GET', '/p/1/clone');

        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form);

        $crawler = $client->request('GET', '/p/1');

        $this->assertCount(1, $crawler->filterXPath("//code"));
        $this->assertEquals('Hello :)', $crawler->filterXPath("//code")->text());
    }

    public function testDownloadPaste()
    {
        $client = $this->createClient();
        $crawler = $client->request('POST', '/api', array(
            'content' => 'Hello :)',
        ));

        $this->assertTrue($client->getResponse()->isRedirect());

        $crawler = $client->request('GET', '/p/1/download');

        $this->assertTrue($client->getResponse()->isOk());
    }

    public function testPasteWithHighlighting()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/new');

        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, array(
            'paste[content]'     => 'Hello :)',
            'paste[filename]'    => 'test.txt',
            'paste[convertTabs]' => '1',
            'paste[highlight]'   => true,
        ));

        $this->assertTrue($client->getResponse()->isRedirect('/p/1'));

        $crawler = $client->request('GET', '/p/1');

        $this->assertCount(1, $crawler->filterXPath("//code"));
        $this->assertCount(0, $crawler->filterXPath("//code[contains(@class, 'no-highlight')]"));
        $this->assertEquals('Hello :)', $crawler->filterXPath("//code")->text());
    }

    public function testPasteWithoutHighlighting()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/new');

        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, array(
            'paste[content]'     => 'Hello :)',
            'paste[filename]'    => 'test.txt',
            'paste[convertTabs]' => '1',
            'paste[highlight]'   => false,
        ));

        $this->assertTrue($client->getResponse()->isRedirect('/p/1'));

        $crawler = $client->request('GET', '/p/1');

        $this->assertCount(1, $crawler->filterXPath("//code"));
        $this->assertCount(1, $crawler->filterXPath("//code[contains(@class, 'no-highlight')]"));
        $this->assertEquals('Hello :)', $crawler->filterXPath("//code")->text());
    }

    public function testGetLatestPaste()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/new');

        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, array(
            'paste[content]'     => 'Hello :)',
            'paste[filename]'    => 'test.txt',
            'paste[convertTabs]' => '1',
            'paste[highlight]'   => '1',
        ));

        $crawler = $client->request('GET', '/latest');

        $this->assertTrue($client->getResponse()->isRedirect('/p/1'));
    }
}
