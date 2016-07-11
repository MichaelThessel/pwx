<?php

namespace AppBundle\Tests\Controller;

use Silex\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    /** @var  \App\Service\CredentialsService */
    protected $credentialsService;

    protected $credentials = array(
        'userName' => 'nameOfUser',
        'password' => 'passwordOfUser',
        'comment' => 'commentOfUser',
        'expires' => 3600,
        'oneTimeView' => false
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
        $crawler = $client->request('GET', '/', array(), array(), array('HTTPS' => true));

        $this->assertTrue($client->getResponse()->isOk());

        // Check if all form elements are available
        $this->assertEquals(1, $crawler->filter('input[name="userName"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="password"]')->count());
        $this->assertEquals(1, $crawler->filter('textarea[name="comment"]')->count());
        $this->assertEquals(1, $crawler->filter('select[name="expires"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="oneTimeView"]')->count());
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
        $this->assertTrue($client->getResponse()->isOk());

        $link = $crawler->filter('#passwordlink')->link()->getUri();
        $hash = array();
        $this->assertEquals(1, preg_match('/pw\/(.*)$/', $link, $hash));

        $this->credentialsService->delete($hash[1]);
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

        $this->credentialsService->delete($credentials->getHash());
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

    /**
     * Test oneTimeView Off
     */
    public function testOneTimeViewOff()
    {
        $credentials = $this->credentialsService->save($this->credentials);

        // Access resource first time
        $client = $this->createClient();
        $client->request('GET', '/pw/' . $credentials->getHash());
        $this->assertTrue($client->getResponse()->isOk());

        // Visit the link page again and see of the entry is deleted
        $client = $this->createClient();
        $crawler = $client->request('GET', '/pw/' . $credentials->getHash());
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertEquals(1, $crawler->filter('#deleteCredentialsForm')->count());
    }

    /**
     * Test oneTimeView On
     */
    public function testOneTimeViewOn()
    {
        $this->credentials['oneTimeView'] = true;
        $credentials = $this->credentialsService->save($this->credentials);

        // Access resource first time
        $client = $this->createClient();
        $crawler = $client->request('GET', '/pw/' . $credentials->getHash());
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertEquals(0, $crawler->filter('#credentialsExpired')->count());

        // Visit the link page again and see of the entry is deleted
        $client = $this->createClient();
        $crawler = $client->request('GET', '/pw/' . $credentials->getHash());
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertEquals(1, $crawler->filter('#credentialsExpired')->count());
        $this->assertEquals(0, $crawler->filter('#deleteCredentialsForm')->count());
    }

    /**
     * Test API, submit credentials and get link to password-page
     */
    public function testApiPostCredentialsAndGetLinkToPasswordPage()
    {
        // Load index page
        $client = $this->createClient();
        $client->request(
            'POST',
            '/api',
            $this->credentials,
            array(),
            array('HTTPS' => true)
        );

        $this->assertTrue($client->getResponse()->isOk());
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertTrue(is_array($response));
        $this->assertTrue(array_key_exists('hash', $response));

        $this->credentialsService->delete($response['hash']);
    }

    /**
     * Test API, submit empty credentials
     */
    public function testApiPostCredentialsEmpty()
    {
        // Load index page
        $client = $this->createClient();
        $client->request(
            'POST',
            '/api',
            array(),
            array(),
            array('HTTPS' => true)
        );

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame(400, $client->getResponse()->getStatusCode());
        $this->assertTrue(is_array($response));
        $this->assertTrue(array_key_exists('message', $response));
    }

    /**
     * Test API, show credentials by hash
     */
    public function testApiGetCredentials()
    {
        $credentials = $this->credentialsService->save($this->credentials);

        $client = $this->createClient();
        $client->request('GET', '/api/' . $credentials->getHash());
        $response = json_decode($client->getResponse()->getContent(), true);

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertEquals($this->credentials['userName'], $response['userName']);
        $this->assertEquals($this->credentials['password'], $response['password']);
        $this->assertEquals($this->credentials['comment'], $response['comment']);

        $this->credentialsService->delete($credentials->getHash());
    }

    /**
     * Test API, show credentials by hash
     */
    public function testApiGetCredentialsInvalid()
    {

        $client = $this->createClient();
        $client->request('GET', '/api/invalid');

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame(410, $client->getResponse()->getStatusCode());
        $this->assertTrue(is_array($response));
        $this->assertTrue(array_key_exists('message', $response));
    }

    /**
     * Test API, delete credentials by hash
     */
    public function testApiDeleteCredentials()
    {
        $credentials = $this->credentialsService->save($this->credentials);

        $client = $this->createClient();
        $client->request('GET', '/api/' . $credentials->getHash());
        $this->assertTrue($client->getResponse()->isOk());

        // Delete credential
        // Response is empty with statusCode 204
        $client->request('DELETE', '/api/' . $credentials->getHash());
        $this->assertEquals(204, $client->getResponse()->getStatusCode());

        // Visit the link page again and see of the entry is deleted
        $client->request('GET', '/api/' . $credentials->getHash());
        $this->assertEquals(410, $client->getResponse()->getStatusCode());
    }
}
