<?php

namespace AppBundle\Tests\Model;

use App\Entity\Credentials;
use App\Model\CryptAESService;

class CryptAESFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CryptAESService
     */
    protected $aes;

    protected $config = array(
        'secret' => "ThisIsTheSecretForTesting"
    );

    public function setup()
    {
        $this->aes = new CryptAESService($this->config);
    }

    public function testEncryptDecryptCredentials()
    {
        $credentials = new Credentials();
        $credentials->setUsername('testUser');
        $credentials->setPassword('testPassword');
        $credentials->setComment('testComment');
        $credentials->setExpires('3600');

        $encryptedCredentials = $this->aes->encrypt(clone $credentials);
        $decryptedCredentials = $this->aes->decrypt(clone $encryptedCredentials);

        $this->assertNotEquals($credentials->getUsername(), $encryptedCredentials->getUsername());
        $this->assertNotEquals($credentials->getPassword(), $encryptedCredentials->getPassword());
        $this->assertNotEquals($credentials->getComment(), $encryptedCredentials->getComment());
        $this->assertEquals($credentials->getUsername(), $decryptedCredentials->getUsername());
        $this->assertEquals($credentials->getPassword(), $decryptedCredentials->getPassword());
        $this->assertEquals($credentials->getComment(), $decryptedCredentials->getComment());
    }


}
