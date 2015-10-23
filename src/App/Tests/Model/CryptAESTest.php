<?php

namespace AppBundle\Tests\Model;

use PHPUnit_Framework_TestCase;
use App\Model\CryptAES;
use Crypt_AES;

class CryptAESTest extends PHPUnit_Framework_TestCase
{
    protected $crypt;
    protected $secret = 'foo';

    public function setUp()
    {
        $this->crypt = new CryptAES;
    }


    /**
     * Test encryption and decryption of CryptAES
     *
     * @return void
     */
    public function testEncrypt()
    {
        $this->assertNotEquals(
            $this->secret,
            $this->crypt->encrypt($this->secret)
        );
    }

    /**
     * Test encryption and decryption of CryptAES
     *
     * @return void
     */
    public function testEnDecrypt()
    {
        $this->assertEquals(
            $this->secret,
            $this->crypt->decrypt($this->crypt->encrypt($this->secret))
        );
    }

    /**
     * Tests if CryptAES encrypts identical to Crypt_AES
     *
     * @return void
     */
    public function testIdentical()
    {
        $cipher = new Crypt_AES(CRYPT_AES_MODE_ECB);
        $cipher->setKey(APP_SECRET);
        $this->assertEquals(
            $this->crypt->encrypt($this->secret),
            $cipher->encrypt($this->secret)
        );
    }
}
