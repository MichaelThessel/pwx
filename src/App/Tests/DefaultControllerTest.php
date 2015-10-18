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

    public function testPostIndex()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');
        $form = $crawler->filter('#passwordSubmitForm')->form();
        $form->setValues(array(
            'userName' => 'nameOfUser',
            'password' => 'passwordOfUser',
            'comment' => 'commentOfUser',
            'period' => 3600));

        $client->submit($form);

        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertContains('/pw/', $crawler->filter('#passwordlink')->text());

        $link = $crawler->filter('#passwordlink')->link();
        $crawler = $client->click($link);

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertEquals('nameOfUser', trim(strip_tags($crawler->filter('#userName')->text())));
        $this->assertEquals('passwordOfUser', trim(strip_tags($crawler->filter('#password')->text())));
        $this->assertEquals('commentOfUser', trim(strip_tags($crawler->filter('#comment')->text())));
    }
}
