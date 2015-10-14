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
        $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertContains('Password Exchange', $client->getResponse()->getContent());
    }
}
