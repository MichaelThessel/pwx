<?php

namespace AppBundle\Tests\Controller;

use Silex\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function createApplication()
    {
        $app = require __DIR__.'/../../../app/app.php';

        $app['debug'] = true;

        unset($app['exception_handler']);

        return $app;
    }

    public function testIndex()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isOk());

        // Check if all form elements are available
        $this->assertEquals(1, $crawler->filter('input[name="userName"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="password"]')->count());
        $this->assertEquals(1, $crawler->filter('textarea[name="comment"]')->count());
        $this->assertEquals(1, $crawler->filter('select[name="period"]')->count());
        $this->assertEquals(1, $crawler->filter('button[type="submit"]')->count());
    }
}
