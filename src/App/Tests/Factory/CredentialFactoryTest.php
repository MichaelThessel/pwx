<?php

namespace AppBundle\Tests\Factory;

use PHPUnit_Framework_TestCase;

class CredentialsFactory extends PHPUnit_Framework_TestCase
{
    /** @var  \App\Factory\CredentialsFactory */
    protected $credentialsFactory;

    /** @var  \Pimple */
    protected $app;

    public function setUp()
    {
        $this->app = require __DIR__ . '/../../../../app/app.php';
        $this->credentialsFactory = $this->app['credentials_factory'];
    }

    /**
     * Test retrieval of instance
     *
     * @return void
     */
    public function testInstance()
    {
        $instance = $this->credentialsFactory->getInstance();

        $this->assertInstanceOf('App\Entity\Credentials', $instance);
    }

    /**
     * Test if hash is initialized
     *
     * @return void
     */
    public function testHashIsInitialized()
    {
        $instance = $this->credentialsFactory->getInstance();

        $this->assertNotEmpty($instance->getHash());
        $this->assertSame(10, strlen($instance->getHash()));
    }
}
