<?php

namespace AppBundle\Tests\Controller;

use Silex\WebTestCase;
use Symfony\Component\DomCrawler\Link;

class DefaultControllerTest extends WebTestCase
{

    protected $credentials;

    public function createApplication()
    {
        $app = require __DIR__.'/../../../app/app.php';

        $app['debug'] = true;

        unset($app['exception_handler']);

        return $app;
    }

    /**
     * @before
     */
    public function setCredentials()
    {
        $this->credentials = array(
            'userName' => 'nameOfUser',
            'password' => 'passwordOfUser',
            'comment' => 'commentOfUser',
            'period' => 3600);
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

    public function testSubmitCredentialsAndRedirectToLinkPage()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');
        $form = $crawler->filter('#submitCredentialsForm')->form();

        $form->setValues($this->credentials);
        $client->submit($form);
        $crawler = $client->followRedirect();

        // Test URI and Link-Url
        $hash = substr(strrchr($crawler->filter('#passwordlink')->text(), '/'), 1);

        // Get and follow the link to revealed passwords
        $link = $crawler->filter('#passwordlink')->link();

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertContains('/link/' . $hash, $client->getRequest()->getUri());
        $this->assertContains('/pw/' . $hash, $crawler->filter('#passwordlink')->text());

        return $link;
    }

    /**
     * @param Link $link
     *
     * @depends testSubmitCredentialsAndRedirectToLinkPage
     */
    public function testRevealCredentialsPage($link)
    {
        $client = $this->createClient();
        $crawler = $client->click($link);

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertEquals($this->credentials['userName'], $crawler->filter('#userName  > span')->text());
        $this->assertEquals($this->credentials['password'], $crawler->filter('#password  > span')->text());
        $this->assertEquals($this->credentials['comment'], trim($crawler->filter('#comment')->text()));
    }

    /**
     * @param Link $link
     *
     * @depends testSubmitCredentialsAndRedirectToLinkPage
     */
    public function testClickOnDeleteCredentials($link)
    {
        $client = $this->createClient();
        $crawler = $client->click($link);
        $hash = substr(strrchr($link->getUri(), '/'), 1);

        // Delete entry with redirect to '/'
        $form = $crawler->filter('#deleteCredentialsForm')->form();
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isOk());

        // Repeating the Assertions from testIndex
        // Smells a bit ugly
        // better is to get the Route from the framework or add an unique ID or title to home
        $this->assertEquals(1, $crawler->filter('input[name="userName"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="password"]')->count());
        $this->assertEquals(1, $crawler->filter('textarea[name="comment"]')->count());
        $this->assertEquals(1, $crawler->filter('select[name="period"]')->count());
        $this->assertEquals(1, $crawler->filter('button[type="submit"]')->count());

        // Go again to the password-link page
        // The password is expired
        $crawler = $client->request('GET', '/pw/'.$hash);
        $this->assertEquals(1, $crawler->filter('#credentialsExpired')->count());
    }
}
