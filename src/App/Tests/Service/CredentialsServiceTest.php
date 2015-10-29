<?php

namespace AppBundle\Tests\Service;

use PHPUnit_Framework_TestCase;

class CredentialsServiceTest extends PHPUnit_Framework_TestCase
{
    /** @var  \App\Service\CredentialsService */
    protected $credentialsService;
    protected $app;

    protected $credentials = array(
        'userName' => 'userName',
        'password' => 'password',
        'comment' => 'comment',
        'expires' => 3600,
        'oneTimeView' => false
    );

    public function setUp()
    {
        $this->app = require __DIR__.'/../../../../app/app.php';
        $this->credentialsService = $this->app['credentials_service'];
    }

    /**
     * Test saving of credentials
     */
    public function testSave()
    {
        $credentials = $this->credentialsService->save($this->credentials);

        $this->assertSame($credentials->getUserName(), $this->credentials['userName']);
        $this->assertSame($credentials->getPassword(), $this->credentials['password']);
        $this->assertSame($credentials->getcomment(), $this->credentials['comment']);

        // Test that expires is within 4s of what its supposed to be, to avoid timing issues when testing
        $expires = $this->credentials['expires'] + time();
        $this->assertTrue(
            $credentials->getExpires() - 2 <= $expires &&
            $credentials->getExpires() + 2 >= $expires
        );

        $this->credentialsService->delete($credentials->getHash());
    }

    /**
     * Test find credentials
     */
    public function testFind()
    {
        $credentials = $this->credentialsService->save($this->credentials);
        $credentials = $this->credentialsService->find($credentials->getHash());

        $this->assertSame($credentials->getUserName(), $this->credentials['userName']);
        $this->assertSame($credentials->getPassword(), $this->credentials['password']);
        $this->assertSame($credentials->getcomment(), $this->credentials['comment']);

        $this->credentialsService->delete($credentials->getHash());
    }

    /**
     * Test find non-existing credentials
     */
    public function testFindNoResult()
    {
        $this->assertNull($this->credentialsService->find('test'));
    }

    /**
     * Test delete credentials
     */
    public function testDelete()
    {
        $credentials = $this->credentialsService->save($this->credentials);
        $hash = $credentials->getHash();

        $this->assertTrue(is_object($this->credentialsService->find($hash)));
        $credentials = $this->credentialsService->delete($hash);
        $this->assertTrue(is_null($this->credentialsService->find($hash)));
    }

    /**
     * Test expires limitation
     */
    public function testLimitExpires()
    {
        $testCredentials = $this->credentials;
        $testCredentials['expires'] = 0;
        $credentials = $this->credentialsService->save($testCredentials);

        $this->assertNotSame($credentials->getExpires(), $testCredentials['expires'] + time());

        $this->credentialsService->delete($credentials->getHash());
    }
}
