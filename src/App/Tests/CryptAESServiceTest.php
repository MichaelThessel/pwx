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

    public function testEncryptCredentials()
    {
        $credentials = new Credentials();
        $credentials->setUsername('testUser');
        $credentials->setPassword('testPassword');
        $credentials->setComment('testComment');
        $credentials->setExpires('3600');

        $encryptedCredentials = $this->aes->encrypt(clone $credentials);
        $decryptedCredentials = $this->aes->decrypt(clone $encryptedCredentials);

        var_dump($credentials);
        var_dump($encryptedCredentials);
        var_dump($decryptedCredentials);
    }


}
