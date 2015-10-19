<?php

namespace AppBundle\Tests\Controller;

use Silex\WebTestCase;
use Symfony\Component\DomCrawler\Link;

class DefaultControllerTest extends WebTestCase
{

    protected $credentials = array(
            'userName' => 'nameOfUser',
            'password' => 'passwordOfUser',
            'comment' => 'commentOfUser',
            'period' => 3600
    );

    public function createApplication()
    {
        $app = require __DIR__.'/../../../app/app.php';

        $app['debug'] = true;

        unset($app['exception_handler']);

        return $app;
    }

    /**
     * Test credential creation
     *
     * @return void
     */
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

    /**
     * Test redirect after credential creation, test if link on password share
     * page leads to password view page
     *
     * @return void
     */
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
     * Test credential viewing
     *
     * @param Symfony\Component\DomCrawler\Link $link Link to test
     *
     * @depends testSubmitCredentialsAndRedirectToLinkPage
     */
    public function testRevealCredentialsPage($link)
    {
        $client = $this->createClient();
        $crawler = $client->click($link);

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertEquals($this->credentials['userName'], $crawler->filter('#userName > span')->text());
        $this->assertEquals($this->credentials['password'], $crawler->filter('#password > span')->text());
        $this->assertEquals($this->credentials['comment'], trim($crawler->filter('#comment')->text()));
    }

    /**
     * Test credential deletion
     *
     * @param Symfony\Component\DomCrawler\Link $link Link to test
     *
     * @depends testSubmitCredentialsAndRedirectToLinkPage
     */
    public function testClickOnDeleteCredentials($link)
    {
        $client = $this->createClient();
        $crawler = $client->click($link);

        // Delete credential
        $form = $crawler->filter('#deleteCredentialsForm')->form();
        $client->submit($form);
        $client->followRedirect();
        $this->assertTrue($client->getResponse()->isOk());

        // Test if redirects to '/'
        $this->assertEquals('/', $client->getRequest()->getRequestUri());

        // Visit the link page again and see of the entry is deleted
        $client = $this->createClient();
        $crawler = $client->click($link);
        $this->assertEquals(1, $crawler->filter('#credentialsExpired')->count());
    }
}
