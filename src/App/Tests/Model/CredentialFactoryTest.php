<?php

namespace AppBundle\Tests\Model;

use PHPUnit_Framework_TestCase;

class CredentialsFactory extends PHPUnit_Framework_TestCase
{
    protected $credentialsFactory;
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
}
