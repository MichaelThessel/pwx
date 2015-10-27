<?php

namespace AppBundle\Tests\Controller;

use Silex\WebTestCase;

class DefaultApiControllerTest extends WebTestCase
{
    /** @var  \App\Service\CredentialsService */
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
     * Test redirect after credential creation
     */
    public function testApiPostCredentialsAndGetLinkToPasswordPage()
    {
        // Load index page
        $client = $this->createClient();
        $client->request(
            'POST',
            '/api/',
            $this->credentials
        );

        $this->assertTrue($client->getResponse()->isOk());

        $link = json_decode($client->getResponse()->getContent(), true)['link'];
        $hash = array();

        $this->assertEquals(1, preg_match('/api\/(.*)$/', $link, $hash));
        $this->credentialsService->delete($hash[1]);
    }

    /**
     * Test credential viewing
     */
    public function testApiGetCredentials()
    {
        $credentials = $this->credentialsService->save($this->credentials);

        $client = $this->createClient();
        $client->request('GET', '/api/' . $credentials->getHash());
        $credentialsArray = json_decode($client->getResponse()->getContent(), true);

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertEquals($this->credentials['userName'], $credentialsArray['userName']);
        $this->assertEquals($this->credentials['password'], $credentialsArray['password']);
        $this->assertEquals($this->credentials['comment'], $credentialsArray['comment']);
        $this->credentialsService->delete($credentials->getHash());
    }

    /**
     * Test credential deletion
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
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}
