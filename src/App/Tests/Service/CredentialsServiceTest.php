<?php

namespace AppBundle\Tests\Service;

use PHPUnit_Framework_TestCase;

class CredentialsServiceTest extends PHPUnit_Framework_TestCase
{
    protected $credentialsService;
    protected $app;

    protected $credentials = array(
        'userName' => 'userName',
        'password' => 'password',
        'comment' => 'comment',
        'expires' => 3600,
    );

    public function setUp()
    {
        $this->app = require __DIR__.'/../../../../app/app.php';
        $this->credentialsService = $this->app['credentials_service'];
    }

    /**
     * Test saving of credentials
     *
     * @return string Hash of new credentials
     */
    public function testSave()
    {
        $credentials = $this->credentialsService->save(
            $this->credentials
        );

        $this->assertSame($credentials->getUsername(), $this->credentials['userName']);
        $this->assertSame($credentials->getPassword(), $this->credentials['password']);
        $this->assertSame($credentials->getcomment(), $this->credentials['comment']);
        $this->assertSame($credentials->getExpires(), $this->credentials['expires'] + time());

        return $credentials->getHash();
    }

    /**
     * Test find credentials
     *
     * @depends testSave
     */
    public function testFind($hash)
    {
        $credentials = $this->credentialsService->find($hash);

        $this->assertSame($credentials->getUsername(), $this->credentials['userName']);
        $this->assertSame($credentials->getPassword(), $this->credentials['password']);
        $this->assertSame($credentials->getcomment(), $this->credentials['comment']);
    }

    public function testFindNoResult()
    {
        $this->assertNull($this->credentialsService->find('test'));
    }

    /**
     * Test delete credentials
     *
     * @depends testSave
     */
    public function testDelete($hash)
    {
        $this->assertTrue(is_object($this->credentialsService->find($hash)));
        $credentials = $this->credentialsService->delete($hash);
        $this->assertTrue(is_null($this->credentialsService->find($hash)));
    }
}
