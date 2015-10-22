<?php

namespace AppBundle\Tests\Model;

use App\Model\CryptRSAService;

class CryptRSAServiceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var CryptRSAService
     */
    protected $rsa;

    public function setUp()
    {
        $this->rsa = new CryptRSAService();
    }

    public function testLengthHash()
    {
        $hash = $this->rsa->createHash(10);
        $this->assertEquals(10, strlen($hash));
        $hash = $this->rsa->createHash(32);
        $this->assertEquals(32, strlen($hash));
    }
}
