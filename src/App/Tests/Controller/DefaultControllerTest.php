<?php

namespace AppBundle\Tests\Controller;

use Silex\WebTestCase;
use Symfony\Component\DomCrawler\Link;

class DefaultControllerTest extends WebTestCase
{
    protected $credentialsService;

    protected $credentials = array(
        'userName' => 'nameOfUser',
        'password' => 'passwordOfUser',
        'comment' => 'commentOfUser',
        'expires' => 3600,
    );

    public function createApplication()
    {
        $app = require __DIR__.'/../../../../app/app.php';

        $this->credentialsService = $app['credentials_service'];

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
        $this->assertEquals(1, $crawler->filter('select[name="expires"]')->count());
        $this->assertEquals(1, $crawler->filter('button[type="submit"]')->count());
    }

    /**
     * Test redirect after credential creation
     */
    public function testSaveCredentialsAndRedirectToSharePage()
    {
        // Load index page
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');

        // Save credentials
        $form = $crawler->filter('#submitCredentialsForm')->form();
        $form->setValues($this->credentials);
        $client->submit($form);

        // Follow redirect to share link page
        $crawler = $client->followRedirect();

        // Test URI and Link-Url
        $hash = substr(strrchr($crawler->filter('#passwordlink')->text(), '/'),1);
        $this->assertTrue($client->getResponse()->isOk());

        $link = $crawler->filter('#passwordlink')->link()->getUri();
        $hash = array();
        $this->assertEquals(1, preg_match('/pw\/(.*)$/', $link, $hash));
    }

    /**
     * Test credential viewing
     */
    public function testViewCredentials()
    {
        $credentials = $this->credentialsService->save($this->credentials);

        $client = $this->createClient();
        $crawler = $client->request('GET', '/pw/' . $credentials->getHash());

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertEquals($this->credentials['userName'], $crawler->filter('#userName > span')->text());
        $this->assertEquals($this->credentials['password'], $crawler->filter('#password > span')->text());
        $this->assertEquals($this->credentials['comment'], trim($crawler->filter('#comment')->text()));
    }

    /**
     * Test credential deletion
     */
    public function testDeleteCredentials()
    {
        $credentials = $this->credentialsService->save($this->credentials);

        $client = $this->createClient();
        $crawler = $client->request('GET', '/pw/' . $credentials->getHash());

        // Delete credential
        $form = $crawler->filter('#deleteCredentialsForm')->form();
        $client->submit($form);
        $client->followRedirect();
        $this->assertTrue($client->getResponse()->isOk());

        // Test if redirects to '/'
        $this->assertEquals('/', $client->getRequest()->getRequestUri());

        // Visit the link page again and see of the entry is deleted
        $client = $this->createClient();
        $crawler = $client->request('GET', '/pw/' . $credentials->getHash());
        $this->assertEquals(1, $crawler->filter('#credentialsExpired')->count());
    }
}
